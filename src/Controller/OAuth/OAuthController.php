<?php

namespace App\Controller\OAuth;

use App\Controller\RestController;
use App\Service\OAuth\OAuth2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Constant\ApiDoc\Example;
use App\Constant\ApiDoc\Tag;

/**
 * @Route("/oauth")
 *
 * @SWG\Tag(name=Tag::USER_AUTHORIZATION)
 */
class OAuthController extends RestController
{
    /**
     * @Route("/token", methods={"POST"})
     *
     * @SWG\Post(summary="Get authorization token",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK"
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @SWG\Definition(
     *              required={"client_id", "client_secret", "grant_type"},
     *              @SWG\Property(property="client_id", example=Example::OAUTH_CLIENT_ID, type="string", description="OAuth client id"),
     *              @SWG\Property(property="client_secret", example=Example::OAUTH_CLIENT_SECRET, type="string", description="OAuth client secret"),
     *              @SWG\Property(property="grant_type", format="password", example="password", type="string", description="Grant type"),
     *              @SWG\Property(property="username", example=Example::EMAIL, type="string", description="User email"),
     *              @SWG\Property(property="password", example=Example::PASSWORD, type="string", description="User password"),
     *          )
     *      )
     * )
     *
     * @param OAuth2 $server
     * @param Request $request
     *
     * @return Response
     */
    public function token(OAuth2 $server, Request $request)
    {
        return $server->grantAccessToken($request);
    }
}