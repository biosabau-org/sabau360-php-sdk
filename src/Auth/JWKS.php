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
     * @throws \RuntimeException
     * @return string[]
     */
    public function getPublicKeys(): array
    {

        if (!empty(self::$keys)) {

            return self::$keys;
        }

        self::$keys = [];

        $host = 'https://auth.sabau360.net/';

        $host = trim(rtrim($host, "/"));
        $url = "{$host}/.well-known/jwks.json";

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