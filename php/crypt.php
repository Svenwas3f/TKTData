<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to en and decrypt ticketToken
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $methode: Visit PHP Documentation page of openssl_encrypt, openssl_decrypt [private]
 * $options: Visit PHP Documentation page of openssl_encrypt, openssl_decrypt [private]
 * $iv: Visit PHP Documentation page of openssl_encrypt, openssl_decrypt [private]
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Crypt->encrypt ( $data [String to encrypt])
 *
 * Crypt->decrypt ( $data [Encrypted string to decrypt])
 *
 */
class Crypt {
  private static $methode = 'AES-128-CTR';
  private static $options = 0;
  private static $iv = '1234567891011121';

  public static function encrypt($data){
    return  openssl_encrypt($data, self::$methode, SALT_STRING, self::$options, self::$iv);
  }

  public static function decrypt($data){
    return openssl_decrypt($data, self::$methode, SALT_STRING, self::$options, self::$iv);
  }
}
 ?>
