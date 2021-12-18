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
      set_time_limit(180); // 時間制限無し
      error_reporting(0);
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

      //batch
      $do_login = false;
      if (!$this->isBatchProcess()) {
        $do_login = true;
      }

      //auth
      if ($do_login) {

        //not auth -> logout
        $uri = \Uri::string();
        $uri_auth = array(
          'manage/auth/login',
          'manage/auth/logout'
        );
        if (!\Model\UserManage::isAuth() && 
          !in_array($uri, $uri_auth)) { 

            die("
              <script>
                self.parent.location.href='/manage/auth/logout';
              </script>
            ");
        }

        //timeout
        if(\Model\UserManage::isLoginTimeout() && 
          !in_array($uri, $uri_auth)){

            //
            // \Model\UserLog::addLog(\Model\UserLog::EVENT_NAME_LOGIN_TIMEOUT);
   
            die("
              <script>
                self.parent.location.href='/manage/auth/logout';
              </script>
            ");
        }

      }

      //uri permission
      if ($do_login) {
        if ((
          strpos($uri, 'manage/user') !== false || 
          strpos($uri, 'manage/userlog') !== false
          ) && 
          !\Model\UserManage::isAdmin() ){

          throw new HttpNotFoundException;
        }
      }

  
    }

    private function isBatchProcess() { 
      //
      $uri = \Uri::string();
      if (substr($uri, 0, 6) !== "manage") {
        return true;
      }

      //
      $urls = array(
        'manage/finance/getnews',
        'manage/finance/cleanolddata',
        'manage/finance/resetrankweek',
        'manage/finance/resetrankday',
        'manage/finance/gethist',
        'manage/finance/maketrendline',
        'manage/finance/fetch',
        'manage/finance/bitfly',
        'manage/finance/zaif'
      );
      return in_array($uri, $urls);
    }

    /**
     * プッシュ通知
     * 一回あたり256バイト、一回の接続で5000バイト、という送信制限があるので注意
     *
     * @access  public
     * @return  Response
     */
    public function apns_push($messages = array()) {
        $ret = true;

        if ($_SERVER['FUEL_ENV'] == Fuel::PRODUCTION) {
            // 本番機
            $push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, __DIR__ . '/../../data/apns/server_certificates_production.pem');
        } else {
            // 開発機
            $push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, __DIR__ . '/../../data/apns/server_certificates_sandbox.pem');
        }
        $push->connect();
        try {
            $pcount = 0;
            foreach ($messages as $message) {
                if( $message['os'] === "i" ) {
                    $data = new ApnsPHP_Message($message['device_token']);
                    $data->setBadge(1);
                    $data->setSound();
                    //$data->setCustomProperty($sName, $mValue);
                    $data->setText($message['message']);
                    if( isset($message['olink']) ){
                        $data->setCustomProperty('link', $message['olink']);
                    }
                    $data->setExpiry(30);
                    $push->add($data);
                    $pcount = $pcount + 1;
                }
            }
            if( $pcount > 0 ){
                $push->send();
            }
            $aErrorQueue = $push->getErrors();
            if (!empty($aErrorQueue)) {
                // デバッグ表示
                $ret = $aErrorQueue;
            }
        } catch (ApnsPHP_Message_Exception $e) {
            $ret = $e->getMessage();
        }
        $push->disconnect();

        return $ret;
    }

    /**
     * 会員表示加工
     *
     * @access  public
     * @return  Response
     */
    public function editlist_member($list) {
        $menues = Config::get('reward_menu');
        foreach ($list as $key => $value) {

            $list[$key]['会員ID'] = sprintf("<a target='_blank' href='/manage/member/info/?member_id=%d'>%d</a>", $list[$key]['会員ID'], $list[$key]['会員ID']);
        }

        return $list;
    }

    /**
     * リクエスト一覧加工
     *
     * @access  public
     * @return  Response
     */
    public function editlist_request($list) {
        $menues = Config::get('reward_menu');
        foreach ($list as $key => $value) {
            // リクエスト
            $query = DB::select(DB::expr('COUNT(*) AS count'));
            $query->from('member_request');
            $query->where('member_id', $list[$key]['会員ID']);
            $results = $query->execute()->as_array();
            $list[$key]['リクエスト'] = $results[0]['count'];


            $list[$key]['会員ID'] = sprintf("<a target='_blank' href='/manage/member/info/?member_id=%d'>%d</a>", $list[$key]['会員ID'], $list[$key]['会員ID']);
            if (isset($list[$key]['IPアドレス'])) {
                $list[$key]['IPアドレス'] = sprintf("<a target='_blank' href='/manage/member/ip/?ip=%s'>%s</a>", $list[$key]['IPアドレス'], $list[$key]['IPアドレス']);
            }
        }

        return $list;
    }

}
