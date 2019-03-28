<?php

namespace App\Controller\User;

use App\Constant\Serialization\Group;
use App\Controller\RestController;
use App\Entity\User\User;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Form\User\RegistrationType;
use App\Service\Notification\Mailer;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Constant\ApiDoc\Example;
use App\Constant\ApiDoc\Tag;

/**
 * @Route("/registration")
 *
 * @SWG\Tag(name=Tag::USER_REGISTRATION)
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
     * @SWG\Post(summary="Register new user by email",
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
     *          @Model(type=RegistrationType::class)
     *      )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->submit($request->request->all());

        $user->setNeedEmailConfirm(true)
            ->setConfirmationToken($this->tokenGenerator->generateToken());

        if (!$form->isValid()) {
            return $this->response($form);
        }

        $this->userManager->updateUser($user);

        $this->mailer->sendConfirmationEmailMessage($user);

        return $this->response($user, Group::LIST_DETAIL);
    }

    /**
     * @Route("/confirm", methods={"POST"})
     *
     * @SWG\Post(summary="Confirm user registration",
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
     *              @SWG\Property(property="token", example=Example::CONFIRMATION_TOKEN, type="string", description="Confirmation token")
     *          )
     *      )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function confirm(Request $request)
    {
        $token = $request->get('token');

        /** @var User $user */
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundException('error.not_found.user');
        }

        $user->setConfirmationToken(null)
            ->setEmailConfirmedAt(new \DateTime());

        $this->userManager->updateUser($user);

        return $this->response();
    }

    /**
     * @Route("/confirmation-email", methods={"POST"})
     *
     * @SWG\Post(summary="Resend confirmation email",
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
     * @param Request $request
     *
     * @return Response
     */
    public function resendConfirmationEmail(Request $request)
    {
        $email = $request->get('email');

        /** @var User $user */
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundException('error.not_found.user');
        }

        if (!$user->needConfirmEmail()) {
            throw new ForbiddenException('error.forbidden.common');
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());

        $this->userManager->updateUser($user);

        $this->mailer->sendConfirmationEmailMessage($user);

        return $this->response();
    }
}