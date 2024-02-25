<?php

namespace App\Controller;

use App\Entity\NotificationTemplate;
use App\Form\NotificationTemplateType;
use App\Repository\NotificationTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification/template')]
class NotificationTemplateController extends AbstractController
{
    #[Route('/', name: 'app_notification_template_index', methods: ['GET'])]
    public function index(NotificationTemplateRepository $notificationTemplateRepository): Response
    {
        return $this->render('notification_template/index.html.twig', [
            'notification_templates' => $notificationTemplateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_notification_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notificationTemplate = new NotificationTemplate();
        $form = $this->createForm(NotificationTemplateType::class, $notificationTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notificationTemplate);
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification_template/new.html.twig', [
            'notification_template' => $notificationTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_template_show', methods: ['GET'])]
    public function show(NotificationTemplate $notificationTemplate): Response
    {
        return $this->render('notification_template/show.html.twig', [
            'notification_template' => $notificationTemplate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotificationTemplate $notificationTemplate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotificationTemplateType::class, $notificationTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification_template/edit.html.twig', [
            'notification_template' => $notificationTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_template_delete', methods: ['POST'])]
    public function delete(Request $request, NotificationTemplate $notificationTemplate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notificationTemplate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notificationTemplate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_notification_template_index', [], Response::HTTP_SEE_OTHER);
    }
}
