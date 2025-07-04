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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_account_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var User $user */
        $user = $this->getUser();
        $companies = $companyRepository->findBy(['createdBy' => $user->getId()]);
        
        return $this->render('account/company/index.html.twig', [
            'companys' => $companies,
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'app_account_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, CompanyRepository $companyRepository): Response
    {
        $company = new Company();
        /** @var User $user */
        $user = $security->getUser();

        if (!$security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez vous connecter pour créer une entreprise.');
        }

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la duplication (nom ou email)
            $name = $company->getName();
            $email = $company->getEmail();
            if ($companyRepository->existsByNameOrEmail($name, $email)) {
                $this->addFlash('error', 'Une entreprise avec ce nom ou cet email existe déjà.');
                return $this->render('account/company/new.html.twig', [
                    'company' => $company,
                    'form' => $form,
                ]);
            }
            $now = new \DateTimeImmutable();
            $company->setCreatedAt($now);
            $company->setState('Online');
            $company->setVerified(false);
            $company->setCreatedBy($user->getId());
            $company->addhubUser($user);
            $company->setUser($user);
            $user->addCompany($company);

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

            $entityManager->persist($company);
            $entityManager->flush();

            $this->addFlash('success', 'Votre entreprise a été créée avec succès !');
            return $this->redirectToRoute('app_account_company_index');
        }

        return $this->render('account/company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('account/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_account_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_account_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_account_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
