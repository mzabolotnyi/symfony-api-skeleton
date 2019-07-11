<?php

namespace App\Controller\Admin\User;

use App\Entity\User\User;
use App\Form\User\ProfileType;
use App\Repository\User\UserRepository;
use App\Controller\RestController;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Constant\Serialization\Group;

/**
 * @Route("user")
 * @SWG\Tag(name="Admin User")
 */
class UserController extends RestController
{
    /**
     * @Route("", methods={"GET"})
     *
     * @SWG\Get(summary="Get list",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={Group::LIST}))
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[limit]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[page]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filters[name]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *      @SWG\Parameter(
     *          name="orders[name]",
     *          in="query",
     *          type="string",
     *          required=false,
     *          enum={"ASC","DESC"},
     *      ),
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request)
    {
        return $this->response($this->getRepository()->findByParams($request->query->all()), Group::LIST);
    }

    /**
     * @Route("/{uuid}", methods={"GET"})
     *
     * @SWG\Get(summary="Get one",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=User::class, groups=Group::LIST_DETAIL)
     *     )
     * )
     *
     * @param User $user
     * @return Response
    */
    public function getOne(User $user)
    {
        return $this->response($user, Group::LIST_DETAIL);
    }

    /**
     * @Route("", methods={"POST"})
     *
     * @SWG\Post(summary="Create",
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
     * @return Response
     */
    public function post(Request $request)
    {
        return $this->updateUser($request, new User);
    }

    /**
     * @Route("/{uuid}", methods={"PUT"})
     *
     * @SWG\Put(summary="Update",
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
     * @param User $user
     * @return Response
     */
    public function put(Request $request, User $user)
    {
        return $this->updateUser($request, $user);
    }

    /**
     * @Route("/{uuid}", methods={"DELETE"})
     *
     * @SWG\Delete(summary="Delete",
     *     @SWG\Response(
     *          response=Response::HTTP_NO_CONTENT,
     *          description="OK"
     *     )
     * )
     *
     * @param User $user
     * @return Response
     */
    public function delete(User $user)
    {
        $this->getEm()->remove($user);
        $this->getEm()->flush();

        return $this->response();
    }

    private function updateUser(Request $request, User $user)
    {
        $form = $this->createForm(ProfileType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        if ($user->isNew()) {
            $this->getEm()->persist($user);
        }

        $this->getEm()->flush();

        return $this->response($user, Group::LIST_DETAIL);
    }

    /**
     * @return UserRepository|ObjectRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }
}