<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Customer;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;


#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, User $user): Response
    {
        return $this->render('company/index.html.twig', [
            'companys' => $companyRepository->findAll(),
        ]);
    }

    #[Route('{id}/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $company = new Company();
        $user = $security->getUser();

        if (!$security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez vous connecter pour crer une organization petit malin...');
        }
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $company->setCreatedAt($now);
            $company->setState('Online');
            $company->setVerified(false);
            $company->setCreatedBy();

            $entityManager->persist($company);

            // Gestion du logo
            $logoFile = $form->get('logo')->getData();
            if ($logoFile instanceof UploadedFile) {
                $filesystem = new Filesystem();
                $logoFileName = md5(uniqid()) . '.' . $logoFile->guessExtension();
                $logoFile->move($this->getParameter('logos_directory'), $logoFileName);
                $company->setLogo($logoFileName);
            }

            // Gestion du banner
            $bannerFile = $form->get('banner')->getData();
            if ($bannerFile instanceof UploadedFile) {
                $filesystem = new Filesystem();
                $bannerFileName = md5(uniqid()) . '.' . $bannerFile->guessExtension();
                $bannerFile->move($this->getParameter('banners_directory'), $bannerFileName);
                $company->setBanner($bannerFileName);
            }


            // Création de l'identifiant client dans Stripe
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $stripeCustomer = Customer::create([
                'email' => $company->getEmail(),
                'name' => $company->getName(),

            ]);

            // Associez l'identifiant client à l'utilisateur dans votre application
            $company->setStripeCustomerId($stripeCustomer->id);
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company, $slug): Response
    {
        $slug;
        dd($slug);
        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
