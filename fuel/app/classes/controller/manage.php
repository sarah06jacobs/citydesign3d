<?php

/**
 * 管理画面親コントローラー
 *
 * @package  app
 * @extends  Controller
 */

require_once __DIR__ . '/../../thirdpirty/ApnsPHP/Autoload.php';

class Controller_Manage extends Controller_Apibase {

    /**
     * コンストラクタ
     *
     * @access  public
     * @return  Response
     */
    public function before() {
      $this->auth = false;
      $this->log = false;
      parent::before();

      //basic auth
      $manage = \Config::get('manage');
      if (!isset($_SERVER["PHP_AUTH_USER"])) {
        header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
        header("HTTP/1.0 401 Unauthorized");
        exit;
        
      } else {
        $auth = $_SERVER["PHP_AUTH_USER"] . "/" . $_SERVER["PHP_AUTH_PW"];
        if (array_search($auth, $manage['basicauth']) === false) {
            throw new HttpNotFoundException;
        }
      }
  
    }
}
