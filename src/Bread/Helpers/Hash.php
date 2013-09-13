<?php
namespace Bread\Helpers;

class Hash
{

    public static function NTLM($input)
    {
        // Convert the password from UTF8 to UTF16 (little endian)
        $input = iconv('UTF-8', 'UTF-16LE', $input);
        
        // Encrypt it with the MD4 hash
        //$MD4Hash = bin2hex(mhash(MHASH_MD4, $input));
        
        // You could use this instead, but mhash works on PHP 4 and 5 or above
        // The hash function only works on 5 or above
        $MD4Hash = hash('md4', $input);
        
        // Make it uppercase, not necessary, but it's common to do so with NTLM hashes
        $NTLMHash = strtoupper($MD4Hash);
        
        return $NTLMHash;
    }
}