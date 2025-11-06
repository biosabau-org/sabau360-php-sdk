<?php

/*
 * This file is part of the Sabau360/SDK package.
 *
 * @author Adrian Zurkiewicz
 * @email adrian@biosabau.com
 * @license MIT
 */

namespace Sabau360\SDK\Auth;

use RuntimeException;


class JWKS
{

    /**
     * 
     * @var array
     */
    protected static array $keys = [];

    /**
     * 
     * @var string
     */
    public readonly string $host;


    /**
     * 
     * @param string $host
     */
    public function __construct(string $host = 'https://auth.sabau360.net/')
    {
        $this->host = trim(rtrim($host, "/"));
    }


    /**
     * 
     * @throws \RuntimeException
     * @return string[]
     */
    public function getPublicKeys(): array
    {

        if (!empty(self::$keys)) {

            return self::$keys;
        }

        self::$keys = [];

        $url = "{$this->host}/.well-known/jwks.json";

        $data = file_get_contents($url);
        $data = json_decode($data, true);

        if (!is_array($data) || !$data) {

            throw new RuntimeException("SSL keys not found on {$url}", 1);

        }

        foreach ($data as $key => $value) {

            self::$keys[] = "{$value['crv']}:{$value['x']}";
        }

        return self::$keys;

    }



}