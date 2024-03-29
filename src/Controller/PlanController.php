<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\User;
use App\Entity\UserPlan;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use App\Repository\UserPlanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/plan')]
class PlanController extends AbstractController
{
    #[Route('/', name: 'app_plan_index', methods: ['GET'])]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('plan/index.html.twig', [
            'plans' => $planRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_plan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable(); 
            $plan->setCreatedAt($now); 
            $entityManager->persist($plan);
            $entityManager->flush();

            return $this->redirectToRoute('create_subscription_plan', ['wplanID' => $plan->getId()]);
        }

        return $this->render('plan/new.html.twig', [
            'plan' => $plan,
            'form' => $form,
        ]);
    }

    #[Route('/show', name: 'app_plan_show', methods: ['GET'])]
    public function show(Plan $plan): Response
    {
        return $this->render('plan/show.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plan/edit.html.twig', [
            'plan' => $plan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plan_delete', methods: ['POST'])]
    public function delete(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plan->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
    }

    

}
