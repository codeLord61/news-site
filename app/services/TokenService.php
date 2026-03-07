<?php

namespace app\services;

class TokenService
{
    /**
     * Generate a cryptographically secure token
     */
    public static function generateToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
