<?php

namespace App\Controller;

use App\Entity\Requests;
use App\Entity\User;
use App\Form\RequestsType;
use App\Repository\RequestsRepository;
use App\Repository\UserRepository;
use App\Repository\CompanieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/requests')]
class RequestsController extends AbstractController
{
    #[Route('/', name: 'app_requests_index', methods: ['GET'])]
    public function index(RequestsRepository $requestsRepository): Response
    {
        return $this->render('requests/index.html.twig', [
            'requests' => $requestsRepository->findAll(),
        ]);
    }

    
    #[Route('/new', name: 'app_requests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, CompanieRepository $companieRepository, UserRepository $userRepository): Response
    {
        $demande = new Requests();
        $form = $this->createForm(RequestsType::class, $demande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $userFirstname = $form->get('firstname')->getData();
            $userLastname = $form->get('lastname')->getData();
        
            // Recherche de l'utilisateur en fonction du prénom et du nom
            $user = $userRepository->findOneBy(['firstname' => $userFirstname, 'lastname' => $userLastname]);
        
            // Si l'utilisateur n'existe pas, vous pouvez choisir de le créer
            if (!$user) {
                $user = new User();
                $user->setEmail($form->get('email')->getData()); // Use the email from the form
                $user->setFirstname($userFirstname);
                $user->setLastname($userLastname);
                $manager->persist($user);
            }
    
            $demande->setStatus('en cours');
            $demande->setCreatedAt(new \DateTimeImmutable());
            $demande->setUsers($user);
    
            // Persister l'entité dans la base de données
            $manager->persist($demande);
            $manager->flush();
    
            $this->addFlash('success', 'Votre demande a été envoyée avec succès.');
    
            return $this->redirectToRoute('app_requests_index');
        }
    
        // Si le formulaire n'est pas valide ou s'il y a une erreur, afficher le formulaire
        return $this->render('requests/new.html.twig', [
          
            'form' => $form
        ]);
    
}

    #[Route('/{id}', name: 'app_requests_show', methods: ['GET'])]
    public function show(Requests $request): Response
    {
        return $this->render('requests/show.html.twig', [
            'request' => $request,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_requests_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RequestsType::class, $request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_requests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('requests/edit.html.twig', [
            'request' => $request,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_requests_delete', methods: ['POST'])]
    public function delete(Request $request,Requests $requests, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$requests->getId(), $request->request->get('_token'))) {
            $entityManager->remove($request);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_requests_index', [], Response::HTTP_SEE_OTHER);
    }
}
