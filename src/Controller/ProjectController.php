<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{

    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {



        $em = $this->getDoctrine()->getManager();
        $projectRepository = $em->getRepository(Project::class);
        $allProjects = $projectRepository->createQueryBuilder('p')->getQuery();

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $allProjects,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('project/index.html.twig', [
            'projects' => $projects
        ]);



    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $now = new \DateTime();
        $project = new Project();
        $project->setDateCreation($now);
        $project->setLastUpdate($now);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {

            return $this->render('project/show.html.twig', [
                'project' => $project,
            ]);

    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $now = new \DateTime();
        $project->setLastUpdate($now);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {

            if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($project);
                $entityManager->flush();
            }

            return $this->redirectToRoute('project_index');

    }
}
