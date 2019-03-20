<?php

namespace App\Controller\OAuth;

use App\Controller\RestController;
use App\Service\OAuth\OAuth2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/oauth")
 */
class OAuthController extends RestController
{
    /**
     * @Route("/token", methods={"POST"})
     *
     * @param OAuth2 $server
     * @param Request $request
     *
     * @return Response
     * @throws \OAuth2\OAuth2ServerException
     */
    public function token(OAuth2 $server, Request $request)
    {
        return $server->grantAccessToken($request);
    }
}