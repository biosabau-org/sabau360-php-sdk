<?php

/*
 * This file is part of the Sabau360/SDK package.
 *
 * @author Adrian Zurkiewicz
 * @email adrian@biosabau.com
 * @license MIT
 */

namespace Sabau360\SDK\Auth;


class Client
{

    /**
     * 
     * @var string
     */
    public string $redirect_uri = '';

    /**
     * 
     * @var string
     */
    public string $state = '';

    /**
     * 
     * @var string
     */
    public string $nonce = '';


    /**
     * Genere auth URI
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getAuthorizationUrl(): string
    {

        if (empty($this->redirect_uri)) {

            throw new \InvalidArgumentException('Redirect URI is required to generate authorization URI.');
        }

        $result = "https://auth.sabau360.net";

        $query = [];
        $query['redirect_uri'] = $this->redirect_uri;

        if (!empty($this->state)) {

            $query['state'] = $this->state;
        }

        if (!empty($this->nonce)) {
            $query['nonce'] = $this->nonce;
        }

        $new_query = http_build_query($query);

        return $result . '?' . $new_query;


    }



    /**
     * 
     * @throws InvalidTokenException
     * @return Token
     */
    public function getToken(string $code, ?string $state = null): Token
    {

        $result = new Token($code);
        $token = $result->getIdToken(false);

        if (hash_equals(($token['nonce'] ?? ''), $this->nonce) !== true) {

            throw new InvalidTokenException("Invalid or losted nonce value in token.");
        }

        if ($state || $this->state) {
            if (hash_equals((string)$state, (string)$this->state) !== true) {

                throw new InvalidTokenException("Invalid or losted state value");
            }
        }

        if (!$result->isValid()) {

            throw new InvalidTokenException("Invalid token");
        }

        return $result;

    }



    public function refreshToken(string $refresh_token): Token {

        $result = new Token($refresh_token, 'refresh_token');

        if (!$result->isValid()) {

            throw new InvalidTokenException("Invalid token");
        }

        return $result;

    }






}