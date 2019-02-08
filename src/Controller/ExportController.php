<?php

namespace App\Controller;

use App\Entity\Export;
use App\Entity\Ticket;
use App\Form\ExportType;
use App\Repository\ExportRepository;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use phpDocumentor\Reflection\Types\Integer;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * @Route("/export")
 */
class ExportController extends AbstractController
{
    /**
     * @Route("/", name="export_index", methods={"GET"})
     * @param ExportRepository $exportRepository
     * @return Response
     */
    public function index(Request $request,ExportRepository $exportRepository): Response
    {
        return $this->render('export/index.html.twig', [
            'exports' => $exportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="export_new", methods={"GET","POST"})
     */
    public function newExport(Request $request): Response
    {
        $export = new Export();
        $form = $this->createForm(ExportType::class, $export);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tickets = $data->getAllTickets();
            $repository = $this->getDoctrine()->getRepository(Ticket::class);
            foreach ($tickets as $ticket) {
                $toUpdate = $repository->findBy(['id' => $ticket->getId()]);
                $toUpdate[0]->setExport($export);
            }
            $export->setCreationDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($export);
            $entityManager->flush();
            $projects = array();
            foreach ($tickets as $ticket) {
                //initialisations:
                $current = (string)$ticket->getProject();
                $content = "";
                //recherche des tickets appartenant au meme projet:
                foreach ($tickets as $ticket2) {
                    if ($ticket2->getProject() == $current) {
                        $content = $content . "\n" . $ticket2->description();
                    }
                }
                $projects[$current] = $content;
                dump($projects);
            }
            return $this->render('report/report.html.twig', [
                'projects' => $projects,
                'name' =>$export->getName(),
                'form' => $form->createView(),
            ]);
        }

        return $this->render('export/new.html.twig', [
            'export' => $export,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="export_show", methods={"GET"})
     */
    public function show(Export $export): Response
    {
        $tickets = $export->getAllTickets();
        return $this->render('export/show.html.twig', [
            'export' => $export,
            'ticket' => $tickets
        ]);

    }
    /**
     * @Route("/{id}", name="export_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Export $export): Response
    {
        if ($this->isCsrfTokenValid('delete' . $export->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($export);
            $entityManager->flush();
        }

        return $this->redirectToRoute('export_index');
    }
    /**
     * @Route("/{id}/download_csv", name="download_csv", methods={"GET","POST"})
     */
    public function download_csv(Request $request, Export $export): Response
    {
        $file = 'Documents/Csv/'.$export->getName().'.csv';
        $filename =$export->getName().".csv";
        $response = new Response();

    // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($file));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($file));

    // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($file));

        return $response;
    }

    /**
     * @Route("/{id}/download_pdf", name="download_pdf", methods={"GET","POST"})
     */
    public function download_pdf(Request $request, Export $export): Response
    {
        $file = 'Documents/Pdf/'.$export->getName().'.pdf';
        $filename =$export->getName().".pdf";
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($file));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($file));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($file));

        return $response;
    }

    /**
     * @Route("/{id}/download_word", name="download_word", methods={"GET","POST"})
     */
    public function download_word(Request $request, Export $export): Response
    {
        $file = 'Documents/Word/'.$export->getName().'.docx';
        $filename =$export->getName().".docx";
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($file));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($file));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($file));

        return $response;
    }

    /**
     * @Route("/report", name="create_report")
     * @param Request $request
     * @param Export $export
     * @return Response
     * @throws MpdfException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function createReport(Request $request): Response
    {
        $data = $request->request->all();
        $name = $data["expname"];
        dump($name);
        //Creating the pdf
        $pdf = new Mpdf();
        $pdf->WriteHTML('<h1>Report</h1>');
        foreach ($data as $key => $value) {
            if ($key != "expname") {
                $pdf->WriteHTML('<h1>' . $key . '</h1><br>');
                $pdf->WriteHTML('<p>' . $value . '</p><hr>');
            }
        }
        $pdf->Output('Documents/Pdf/'.$name.'.pdf', 'F');
        //Creating the word file
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Report');
        foreach ($data as $key => $value) {
            if ($key != "expname") {
                $section = $phpWord->addSection();
                $section->addText($key);
                $section = $phpWord->addSection();
                $section->addText($value);
            }
        }
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = "Documents/Word/".$name.".docx";
        $objWriter->save($filename, 'Word2007', true);
        //creating the csv
        $handle = fopen("Documents/Csv/".$name.".csv", "w");
        fwrite($handle,"Report");
        foreach ($data as $key => $value) {
            if ($key != "expname") {
                fwrite($handle, $key);
                fwrite($handle, $value);
            }
        }
        fclose($handle);
        //redirection
        return $this->redirectToRoute('ticket_index');
        //return new Response("<html><body>heyhey</body></html>");
    }
}

