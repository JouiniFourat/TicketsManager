<?php

namespace App\Controller;
use App\Entity\Project;
use App\Entity\Ticket;
use App\Form\TicketType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/ticket")
 */
class TicketController extends Controller
{
    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ticketRepository = $em->getRepository(Ticket::class);
        $allTickets = $ticketRepository->createQueryBuilder('p')
                                        ->where('p.export IS NULL')
                                        ->orderBy('p.project', 'ASC')
                                        ->getQuery();


        $paginator = $this->get('knp_paginator');
        $tickets = $paginator->paginate(
            $allTickets,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets
        ]);
    }


    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     */
    public function newTicket(Request $request): Response
    {

        $now = new \DateTime();
        $owner = $this->getUser();
        $ticket = new Ticket();
        $ticket->setUser($owner);
        $ticket->setCreationDate($now);
        $ticket->setLastUpdate($now);
        $ticket->setStat("open");
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            $repository = $entityManager->getRepository(Project::class);
            $project = $ticket->getProject();
            $id = $project->getId();
            $project = $repository->find($id);
            $project->setLastUpdate($now);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

return $this->render('ticket/new.html.twig', [
    'ticket' => $ticket,
    'form' => $form->createView(),
]);
}

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     * @param Ticket $ticket
     * @return Response
     */

    public function show(Ticket $ticket): Response
{
    return $this->render('ticket/show.html.twig', [
        'ticket' => $ticket,
    ]);
}

/**
 * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
 */

    public function edit(Request $request, Ticket $ticket): Response
{

    $now = new \DateTime();
    $ticket->setLastUpdate($now);
    $form = $this->createForm(TicketType::class, $ticket);
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {

        //updating the ticket  lastUpdate field and the project last update field
        //ticket
        $now = new \DateTime();
        $ticket->setLastUpdate($now);
        $this->getDoctrine()->getManager()->flush();
        //project
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Project::class);
        $project = $ticket->getProject();
        $id = $project->getId();
        $project = $repository->find($id);
        $project->setLastUpdate($now);
        $entityManager->flush();
        return $this->redirectToRoute('ticket_index', [
            'id' => $ticket->getId(),
        ]);
    }
    return $this->render('ticket/edit.html.twig', [
        'ticket' => $ticket,
        'form' => $form->createView(),
    ]);
}

/**
 * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
 */
public
function delete(Request $request, Ticket $ticket): Response
{

    if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ticket);
        $entityManager->flush();
    }

    return $this->redirectToRoute('ticket_index');
}

}
