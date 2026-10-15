<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Cryptography
{
    public $_CIPHER = "AES-128-CTR";
    public $_KEY = 'd0hictu!2025_KEY';
    public $_OPTIONS = 0;
    public $_IV = 'd0hictu!2025_I_V';
    
    // System encrypt and decrypt
    public function encrypt($str)
    {
        $encrypted = openssl_encrypt($str, $this->_CIPHER, $this->_KEY, $this->_OPTIONS, $this->_IV);

        return strtr(base64_encode($encrypted), '+/', '-_');
    }

    public function decrypt($str)
    {
        $decoded = base64_decode(strtr($str, '-_', '+/'));

        return strtoupper(openssl_decrypt($decoded, $this->_CIPHER, $this->_KEY, $this->_OPTIONS, $this->_IV));
    }


    public function mysql_encrypt_to_php_decrypt($str)
    {
        return strtoupper(openssl_decrypt($str, 'aes-128-ecb', $this->_KEY, OPENSSL_RAW_DATA));
    }

    // DB encrypt and decrypt
    public function mysql_aes_encrypt($str) 
    {   
        return "AES_ENCRYPT('{$str}', '{$this->_KEY}')";
    }
    
    public function mysql_aes_decrypt($str) 
    {
        return "AES_DECRYPT({$str}, '{$this->_KEY}')";
    }
}
