<?php

namespace App\Controller\User;

use App\Constant\Serialization\Group;
use App\Controller\RestController;
use App\Entity\User\User;
use App\Form\User\UserRegistrationType;
use App\Service\Notification\Mailer;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/registration")
 */
class RegistrationController extends RestController
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var Mailer */
    private $mailer;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    public function __construct(UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, Mailer $mailer)
    {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @Route("", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(TranslatorInterface $translator, Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->submit($request->request->all());

        $user->setNeedEmailConfirm(true)
            ->setConfirmationToken($this->tokenGenerator->generateToken());

        if (!$form->isValid()) {
            return $this->response($form);
        }

//        $this->userManager->updateUser($user);

        $this->mailer->sendConfirmationEmailMessage($user);

        return $this->response($user, Group::LIST_DETAIL);
    }
}