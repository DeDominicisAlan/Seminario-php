<?php

namespace App\Config;

class Config
{

  public static function auth()
  {
    return [
      'secret' => trim(file_get_contents(__DIR__ . '/../../mykey.pem')),
      'expires' => 60 // in minutes
    ];
  }
  
  public static function public(){
    return trim(file_get_contents(__DIR__ . '/../../mykey.pub'));
  }

  // JWT settings
  public static function jwt()
  {
    return [
      "path" => ["/"],
      "ignore" => ["/login"],
      "secret" => self::auth()['secret'],
      "relaxed" => ["localhost"],
      "secure" => false,
      "error" => function ($response, $arguments) {
        return $response->withJson([
          'success' => false,
          'errors' => $arguments["message"]
        ], 401);
      },

    ];
  }
}

?>