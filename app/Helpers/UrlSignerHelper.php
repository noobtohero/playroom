<?php

namespace App\Helpers;

class UrlSignerHelper
{
    /**
     * Generates a signed URL.
     */
    public static function sign(string $url, int $expiration = 3600): string
    {
        $expires = time() + $expiration;
        $key = config('Encryption')->key;
        
        $signature = hash_hmac('sha256', $url . $expires, $key);
        
        $separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';
        
        return $url . $separator . 'expires=' . $expires . '&signature=' . $signature;
    }

    /**
     * Validates a signed URL signature.
     */
    public static function verify(string $url, string $signature, int $expires): bool
    {
        if (time() > $expires) {
            return false;
        }

        $key = config('Encryption')->key;
        $expectedSignature = hash_hmac('sha256', $url . $expires, $key);

        return hash_equals($expectedSignature, $signature);
    }
}
