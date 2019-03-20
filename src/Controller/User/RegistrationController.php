<?php

namespace App\Controller\User;

use App\Constant\Serialization\Group;
use App\Controller\RestController;
use App\Entity\User\User;
use App\Form\User\UserRegistrationType;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registration")
 */
class RegistrationController extends RestController
{
    /**
     * @Route("", methods={"POST"})
     *
     * @param UserManagerInterface $userManager
     * @param Request $request
     *
     * @return Response
     */
    public function register(UserManagerInterface $userManager, Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->submit($request->request->all());

        $user->setNeedEmailConfirm(true);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        $userManager->updateUser($user);

        return $this->response($user, Group::LIST_DETAIL);
    }
}