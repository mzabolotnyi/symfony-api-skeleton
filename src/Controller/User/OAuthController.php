<?php

namespace App\Controller\User;

use App\Service\OAuth\OAuth2;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/oauth")
 */
class OAuthController extends AbstractFOSRestController
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
    public function tokenAction(OAuth2 $server, Request $request)
    {
        return $server->grantAccessToken($request);
    }
}