<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Exception;

class CryptoHelper
{
    public static function encrypt($text)
    {
        return Crypt::encryptString($text);
    }

    public static function decrypt($encryptedString)
    {
        try {
            return Crypt::decryptString($encryptedString);
        } catch (Exception $e) {
            return null;
        }
    }
}
