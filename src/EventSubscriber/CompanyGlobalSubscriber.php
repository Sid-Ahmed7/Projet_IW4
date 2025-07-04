<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\CompanyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CompanyGlobalSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $security;
    private $companyRepository;

    public function __construct(Environment $twig, Security $security, CompanyRepository $companyRepository)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->companyRepository = $companyRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $companies = $this->companyRepository->findBy(['createdBy' => $user->getId()]);
            $this->twig->addGlobal('companies', $companies);
        } else {
            $this->twig->addGlobal('companies', []);
        }
    }
} 