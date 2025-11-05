<?php

/*
 * This file is part of the Sabau360/SDK package.
 *
 * @author Adrian Zurkiewicz
 * @email adrian@biosabau.com
 * @license MIT
 */


namespace Sabau360\SDK\Auth;

use Sabau360\SDK\Auth\JWT;

class Token
{

    /**
     * 
     * @var string
     */
    protected string $code;

    /**
     * 
     * @var array|null
     */
    protected array|null $raw;
    protected JWT|null $id_token;
    protected JWT|null $access_token;

    /**
     * 
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
        $this->raw = $this->fetchToken();
        $this->id_token = (new JWT($this->raw['id_token']));
        $this->access_token = (new JWT($this->raw['access_token']));

        // d($this->raw);

    }


    /**
     * 
     * @throws \RuntimeException
     * @return int
     */
    public function getExpiresAt(): int
    {
        $data = $this->access_token->getData();
        return (int)($data['exp'] ?? 0);

    }

    /**
     * 
     * @return bool
     */
    public function getRemember(): bool
    {
        return (bool)($this->raw['remember'] ?? false);
    }

    /**
     * 
     * @throws \RuntimeException
     * @return string
     */
    public function getTokenType(): string
    {

        if (empty($this->raw['token_type'])) {
            throw new \RuntimeException("Token type is not available.");
        }        

        return $this->raw['token_type'];
    }


    /**
     * 
     * @throws \RuntimeException
     * @return string
     */
    public function getRefreshToken(): string
    {

        if (empty($this->raw['refresh_token'])) {
            throw new \RuntimeException("Refresh token is not available.");
        }

        return $this->raw['refresh_token'];
    }


    /**
     * 
     * @throws \RuntimeException
     * @return array
     */
    protected function fetchToken(): array
    {
        $url = 'https://auth.sabau360.net/api/token';
        $postData = http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $this->code,
        ]);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Failed to fetch token, HTTP code: {$httpCode}");
        }

        $response = json_decode($response, true);

        if (!is_array($response) || !$response) {
            throw new \RuntimeException("Invalid token response");
        }


        return $response;
    }

    /**
     * Get the value of id_token
     *
     * @param bool $raw Raw string or array
     * @return array<mixed>|string|null
     */
    public function getIdToken(bool $raw = true): mixed
    {

        if ($raw) {
            return $this->raw['id_token'] ?? null;
        }

        return $this->id_token->getData();
    }

    /**
     * Get the value of access_token
     *
     * @param bool $raw Raw string or array
     * @return array<mixed>|string|null
     */
    public function getAccessToken(bool $raw = true): mixed
    {
        if ($raw) {
            return $this->raw['access_token'] ?? null;
        }   

        return $this->access_token->getData();
    }


    /**
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->id_token->isValid() && $this->access_token->isValid();
    }

}