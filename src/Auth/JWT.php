<?php

/*
 * This file is part of the Sabau360/SDK package.
 *
 * @author Adrian Zurkiewicz
 * @email adrian@biosabau.com
 * @license MIT
 */

namespace Sabau360\SDK\Auth;

use Firebase\JWT\Key;
use Sabau360\SDK\Auth\JWKS;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\SignatureInvalidException;


class JWT
{

    /**
     * 
     * 
     * @var string
     */
    protected string $token;

    /**
     * 
     * @var array|null
     */
    protected array $data;

    /**
     * 
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->data = $this->chechToken($token);
    }


    /**
     * 
     * 
     * @param string $token
     * @return array
     */
    private function chechToken(string $token): array
    {

        // dd($token);

        $keys = $this->getPublicKeys();

        // dd($keys);

        foreach ($keys as $key) {

            $key = explode(":", $key, 2);
            $algorithm = $key[0];
            $key = $key[1];

            /**
             * base64url -> binary
             */
            $key = strtr($key, '-_', '+/');
            $key = base64_decode($key, true);

            try {
                $result = FirebaseJWT::decode($token, new Key($key, 'EdDSA'));
            } catch (SignatureInvalidException $th) {

                continue;
            }

        }

        
        if (!isset($result)) {

            throw new \RuntimeException("Invalid token response");
        }

        $result = (array) $result;

        if (!is_array($result) || !$result) {
            throw new \RuntimeException("Invalid token response");
        }

        return $result;

    }

    /**
     * 
     * @return bool
     */
    public function isValid(): bool
    {

        if (is_array($this->data) && !empty($this->data['exp']) && ((int)$this->data['exp'] > time())) {

            return true;
        }

        return false;
    }


    /**
     * 
     * @throws \RuntimeException
     * @return string[]
     */
    protected function getPublicKeys(): array
    {
        return (new JWKS())->getPublicKeys();
    }


    /**
     * Get the value of data
     *
     * @return array<mixed>
     */
    public function getData(): ?array
    {
        return $this->data;
    }



}