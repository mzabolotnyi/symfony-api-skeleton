<?php

namespace App\Service\OAuth;

use App\Entity\User\User;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2AuthenticateException;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;

class OAuth2 extends \OAuth2\OAuth2
{
    const ERROR_INVALID_TOKEN = 'invalid_token';
    const ERROR_EXPIRED_TOKEN = 'expired_token';
    const ERROR_UNCONFIRMED_EMAIL = 'unconfirmed_email';

    /**
     * @inheritdoc
     */
    public function verifyAccessToken($tokenParam, $scope = null)
    {
        $tokenType = $this->getVariable(self::CONFIG_TOKEN_TYPE);
        $realm = $this->getVariable(self::CONFIG_WWW_REALM);

        if (!$tokenParam) { // Access token was not provided
            throw new OAuth2AuthenticateException(Response::HTTP_BAD_REQUEST, $tokenType, $realm, self::ERROR_INVALID_REQUEST, 'The request is missing a required parameter, includes an unsupported parameter or parameter value, repeats the same parameter, uses more than one method for including an access token, or is otherwise malformed.', $scope);
        }

        // Get the stored token data (from the implementing subclass)
        $token = $this->storage->getAccessToken($tokenParam);
        if (!$token) {
            throw new OAuth2AuthenticateException(self::HTTP_UNAUTHORIZED, $tokenType, $realm, self::ERROR_INVALID_TOKEN, 'The access token provided is invalid.', $scope);
        }

        // Check token expiration (expires is a mandatory paramter)
        if ($token->hasExpired()) {
            throw new OAuth2AuthenticateException(self::HTTP_UNAUTHORIZED, $tokenType, $realm, self::ERROR_EXPIRED_TOKEN, 'The access token provided has expired.', $scope);
        }

        // Check scope, if provided
        // If token doesn't have a scope, it's null/empty, or it's insufficient, then throw an error
        if ($scope && (!$token->getScope() || !$this->checkScope($scope, $token->getScope()))) {
            throw new OAuth2AuthenticateException(Response::HTTP_FORBIDDEN, $tokenType, $realm, self::ERROR_INSUFFICIENT_SCOPE, 'The request requires higher privileges than provided by the access token.', $scope);
        }

        return $token;
    }

    public function createAccessTokenDirectly(User $user, $clientId)
    {
        $client = $this->storage->getClient($clientId);

        return $client instanceof IOAuth2Client ? $this->createAccessToken($client, $user) : null;
    }

    protected function grantAccessTokenUserCredentials(IOAuth2Client $client, array $input)
    {
        $stored = parent::grantAccessTokenUserCredentials($client, $input);

        /** @var User $user */
        $user = $stored['data'];

        if ($user->needEmailConfirm() && !$user->isEmailConfirmed()) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_UNCONFIRMED_EMAIL, "Need confirm email");
        }

        return $stored;
    }
}