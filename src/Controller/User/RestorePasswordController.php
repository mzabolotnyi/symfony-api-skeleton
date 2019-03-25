<?php

namespace App\Controller\User;

use App\Controller\RestController;
use App\Entity\User\User;
use App\Exception\NotFoundException;
use App\Form\User\ResetPasswordType;
use App\Service\Notification\Mailer;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Constant\ApiDoc\Example;
use App\Constant\ApiDoc\Tag;

/**
 * @Route("/restore-password")
 */
class RestorePasswordController extends RestController
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var Mailer */
    private $mailer;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /** @var int */
    private $tokenTtl;

    public function __construct(UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, Mailer $mailer, ParameterBagInterface $bag)
    {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->tokenTtl = $bag->get('fos_user.resetting.token_ttl');
    }

    /**
     * @Route("/request", methods={"POST"})
     *
     * @SWG\Post(summary="Create request for restore password",
     *     @SWG\Response(
     *          response=Response::HTTP_NO_CONTENT,
     *          description="OK"
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @SWG\Definition(
     *              required={"email"},
     *              @SWG\Property(property="email", example=Example::EMAIL, type="string", description="Account linked email")
     *          )
     *      )
     * )
     *
     * @SWG\Tag(name=Tag::USER_RESTORE_PASSWORD)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function request(Request $request)
    {
        $email = $request->get('email');

        /** @var User $user */
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundException('error.not_found.user');
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken())
            ->setPasswordRequestedAt(new \DateTime());

        $this->userManager->updateUser($user);

        $this->mailer->sendRestorePasswordMessage($user);

        return $this->response();
    }

    /**
     * @Route("/reset", methods={"POST"})
     *
     * @SWG\Post(summary="Reset password by token",
     *     @SWG\Response(
     *          response=Response::HTTP_NO_CONTENT,
     *          description="OK"
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @SWG\Definition(
     *              required={"token"},
     *              @SWG\Property(property="token", example=Example::CONFIRMATION_TOKEN, type="string", description="Confirmation token"),
     *              @SWG\Property(property="plainPassword", example=Example::PASSWORD, type="string", description="New password")
     *          )
     *      )
     * )
     *
     * @SWG\Tag(name=Tag::USER_RESTORE_PASSWORD)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function reset(Request $request)
    {
        $token = $request->get('token');

        /** @var User $user */
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user || !$user->isPasswordRequestNonExpired($this->tokenTtl)) {
            throw new NotFoundException('error.not_found.user');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        $user->setConfirmationToken(null)
            ->setPasswordRequestedAt(null);

        $this->userManager->updateUser($user);

        return $this->response();
    }
}