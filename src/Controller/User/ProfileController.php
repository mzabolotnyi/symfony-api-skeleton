<?php

namespace App\Controller\User;

use App\Constant\Serialization\Group;
use App\Controller\RestController;
use App\Entity\User\User;
use App\Form\User\ChangePasswordType;
use App\Form\User\ProfileType;
use FOS\UserBundle\Model\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Constant\ApiDoc\Tag;

/**
 * @Route("/profile")
 *
 * @SWG\Tag(name=Tag::USER_PROFILE)
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
     * @SWG\Get(summary="Get profile of current authorized user",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=User::class, groups=Group::LIST_DETAIL)
     *     )
     * )
     *
     * @return Response
     */
    public function getProfile()
    {
        return $this->response($this->getUser(), Group::LIST_DETAIL);
    }

    /**
     * @Route("", methods={"PUT"})
     *
     * @SWG\Put(summary="Update profile of current authorized user",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=User::class, groups=Group::LIST_DETAIL)
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=ProfileType::class)
     *      )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateProfile(Request $request)
    {
        return $this->updateUser(ProfileType::class, $request);
    }

    /**
     * @Route("/password", methods={"PUT"})
     *
     * @SWG\Put(summary="Change password of current authorized user",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=User::class, groups=Group::LIST_DETAIL)
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=ChangePasswordType::class)
     *      )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changePassword(Request $request)
    {
        return $this->updateUser(ChangePasswordType::class, $request);
    }

    private function updateUser($type, Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm($type, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        $this->userManager->updateUser($user);

        return $this->response($user, Group::LIST_DETAIL);
    }
}