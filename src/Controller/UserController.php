<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\File;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(User $user, UserRoleRepository $userRoleRepository): Response
    {
        $userRoles = $userRoleRepository->findBy(['usr' => $user->getId()]);
        
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
            'userRoles' => $userRoles,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$user) {
            return $this->redirectToRoute('app_user_index');
        }
        if ($user->getPicture()) {
            $filePath = $this->getParameter('pictures_directory') . '/' . $user->getPicture();
            if (file_exists($filePath)) {
                $user->setPicture(new File($filePath));
            } else {
                $user->setPicture(new File($this->getParameter('pictures_directory') . '/no-user.jpg'));
            }
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Si aucun nouveau fichier n'est téléchargé, conservez le nom du fichier existant
            if (null === $form['picture']->getData()) {
                $user->setPicture($user->getPicture());
            }
        
            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }
        
        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/roles', name: 'app_user_roles', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manageRoles(Request $request, User $user, EntityManagerInterface $entityManager, UserRoleRepository $userRoleRepository): Response
    {
        if ($request->isMethod('POST')) {
            $companyId = $request->request->get('company');
            $roleName = $request->request->get('role');
            
            $userRole = $userRoleRepository->findOneBy([
                'usr' => $user->getId(),
                'company' => $companyId
            ]);

            if (!$userRole) {
                $userRole = new \App\Entity\UserRole();
                $userRole->setUsr($user->getId());
                $userRole->setCompany($companyId);
            }

            $userRole->setRoleName($roleName);
            $userRole->setState('online');
            $userRole->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($userRole);
            $entityManager->flush();

            $this->addFlash('success', 'User role updated successfully');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/users/roles.html.twig', [
            'user' => $user,
            'userRoles' => $userRoleRepository->findBy(['usr' => $user->getId()])
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
