<?php

  use \Firebase\JWT\JWT;

  /**
   * Class Jwt Authentication
   */
  class JWTAuth {

    private function __construct() {}

    /**
     * This method create a valid token.
     * @param Int $id - user id
     * @param String $user - username
     * @return String JWT - valid token.
     */
    private static function getToken($id, $user) {
      $secret = SECRET;

      // date: now
      $now = date('Y-m-d H:i:s');
      // date: now +2 hours
      $future = date('Y-m-d H:i:s', mktime(date('H') + 2, date('i'), date('s'), date('m'), date('d'), date('Y')));

      $token = array(
          'header' => [ // User Information
              'id' => $id, // User id
              'user' => $user // username
          ],
          'payload' => [
            'iat' => $now, // Start time of the token
            'exp' => $future // Time the token expires (+2 hours)
          ]
      );

      // Encode Jwt Authentication Token
      return JWT::encode($token, $secret, "HS256");
    }

    /**
     * This method decode a token.
     * @param String $token - valid token.
     * @return Object token.
     */
    function verifyToken($token) {
      $secret = SECRET;

      // Decode Jwt Authentication Token
      return JWT::decode($token, $secret, array("HS256"));
    }
  }

?>
