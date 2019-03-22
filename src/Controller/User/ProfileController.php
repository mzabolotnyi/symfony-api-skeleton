<?php

namespace App\Controller\User;

use App\Constant\Serialization\Group;
use App\Controller\RestController;
use App\Form\User\ProfileType;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends RestController
{
    /** @var UserManagerInterface */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("", methods={"GET"})
     *
     * @return Response
     */
    public function getAction()
    {
        return $this->response($this->getUser(), Group::LIST_DETAIL);
    }

    /**
     * @Route("", methods={"PUT"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        $this->userManager->updateUser($user);

        return $this->response($user, Group::LIST_DETAIL);
    }
}