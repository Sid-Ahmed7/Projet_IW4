<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Notification;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/new/{userID}', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$userID, UserRepository $userRepository): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);
        $usr = $userRepository->findOneBy(['id' => $userID]);
// dd($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable(); 
            $devi->setCreatedAt($now); 
            $devi->setUpdatedAt($now); 
            $devi->setUsers($usr);

            $entityManager->persist($devi);

            
        
            $user = $devi->getUsers();
       
            // Notification
            $notification = new Notification();
            $now = new \DateTimeImmutable();
            $devi->getId();
            $notification->setUsers($devi->getUsers()); // ID du devis
            $notification->setNotificationTemplate(1); // je pense que nous n'aurons plus besoin du notification template mais je laisse au cas ou 
            $notification->setType('Systeme'); // Quel type de notification c'est 
            $notification->setTitle('Votre devis est disponible '); 
            if ($user) {
                $username = $user->getUsername(); // Récupérez le nom d'utilisateur
                $notification->setMessage("Salut {$username}, votre devis est disponible."); 
            }   $notification->setIsRead(false);
            $notification->setCreatedAt($now);
            // dd($notification);
    
            $entityManager->persist($notification);
    
    
            $entityManager->flush();
            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }
        

        return $this->render('devis/new.html.twig', [
            'devi' => $devi,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
    }
}
