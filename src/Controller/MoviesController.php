<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/movies', name: 'index')]
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();
        return $this->render('index.html.twig', ['title' => 'Movies Review', 'movies' => $movies]);
    }

    #[Route('/api/movie/{id}', name: 'show_api', defaults:['id' => null], methods:['GET', 'HEAD'])]
    public function show_api($id): JsonResponse
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->find($id);
        return $this->json([
            'id' => $movie->getId(),
            'title' => $movie->getTitle(),
        ]);
    }

    #[Route('/movie/{id}', name: 'show', defaults:['id' => null], methods:['GET', 'HEAD'])]
    public function show($id): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->find($id);
        return $this->render('show.html.twig', ['movie' => $movie]);
    }

    #[Route('movies/create', name: 'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();
            $imagePath = $form->get('imagePath')->getData();
            if($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . "/public/uploads",
                        $newFileName
                    );
                }
                catch(FileException $e) {
                    return new Response($e->getMessage());
                }
                $newMovie->setImagePath('/uploads/' . $newFileName);
            }
            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movie/edit/{id}', name: 'edit_movie', defaults:['id' => null])]
    public function edit($id, Request $request): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $imagePath = $form->get('imagePath')->getData();
            if($imagePath) {
                // if(isset($movie->getImagePath()) && file_exists($this->getParameter('kernel.project_dir') . $movie->getImagePath())) {
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . "/public/uploads",
                            $newFileName
                        );
                    }
                    catch(FileException $e) {
                        return new Response($e->getMessage());
                    }
                    $movie->setImagePath('/uploads/' . $newFileName);
                // }
            }
           
            $movie->setTitle($form->get('title')->getData());
            $movie->setReleaseYear($form->get('releaseYear')->getData());
            $movie->setDescription($form->get('description')->getData());

            $this->em->flush();
            return $this->redirectToRoute('show', ['id' => $movie->getId()]);
        }

        return $this->render('edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movie/delete/{id}', name: 'delete_movie', defaults:['id' => null], methods:['GET', 'DELETE'])]
    public function delete($id): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->find($id);
        $this->em->remove($movie);
        $this->em->flush();
        return $this->redirectToRoute('index');
    }

    /**
     * old method of route creation
     * 
     * @Route("/old", name="old")
     */
    public function odlMethod(): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies = $repository->findAll();
        // dd($movies);
        return $this->render('index.html.twig');
    }
}
