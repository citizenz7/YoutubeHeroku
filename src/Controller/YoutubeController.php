<?php

namespace App\Controller;

use App\Entity\Youtube;
use App\Form\YoutubeType;
use App\Repository\YoutubeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class YoutubeController extends AbstractController
{
    /**
     * @Route("/", name="youtube_index", methods={"GET"})
     */
    public function index(Request $request, EntityManagerInterface $em, YoutubeRepository $youtubeRepository): Response
    {
        $youtube = new Youtube();

        $form = $this->createForm(YoutubeType::class, $youtube);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $youtube = $form->getData();

            $em->persist($youtube);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('youtube/index.html.twig', [
            'form' => $form->createView(),
            'youtubes' => $youtubeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="youtube_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $youtube = new Youtube();
        $form = $this->createForm(YoutubeType::class, $youtube);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($youtube);
            $entityManager->flush();

            return $this->redirectToRoute('youtube_index');
        }

        return $this->render('youtube/new.html.twig', [
            'youtube' => $youtube,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="youtube_show", methods={"GET"})
     */
    public function show(Youtube $youtube): Response
    {
        return $this->render('youtube/video.html.twig', [
            'name' => $youtube->getName(),
            'url' => $youtube->getUrl(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="youtube_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Youtube $youtube): Response
    {
        $form = $this->createForm(YoutubeType::class, $youtube);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('youtube_index');
        }

        return $this->render('youtube/edit.html.twig', [
            'youtube' => $youtube,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="youtube_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Youtube $youtube): Response
    {
        if ($this->isCsrfTokenValid('delete'.$youtube->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($youtube);
            $entityManager->flush();
        }

        return $this->redirectToRoute('youtube_index');
    }
}
