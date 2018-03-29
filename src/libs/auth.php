<?php

  use \Firebase\JWT\JWT;

  /**
   * Class Jwt Authentication
   */
  class JWTAuth {

    private function __construct() {}

    /**
     * This method create a valid token.
     * @param int $id - user id
     * @param string $user - username
     * @return string JWT - valid token.
     */
    public static function getToken($id, $user) {
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
     * This method verify a token.
     * @param string $token - token.
     * @return boolean valid token.
     */
    public static function verifyToken($token) {
      $secret = SECRET;

      // Decode Jwt Authentication Token
      $obj = JWT::decode($token, $secret, array("HS256"));

      // If payload is defined
      if (isset($obj->payload)) {
        // Gets the actual date
        $now = strtotime(date('Y-m-d H:i:s'));
        // Gets the expiration date
        $exp = strtotime($obj->payload->exp);
        // If token didn't expire
        if (($exp - $now) > 0) {
          return true;
        }
      }

      return false;
    }
  }

?>
