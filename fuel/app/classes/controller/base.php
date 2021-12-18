<?php

/**
 * テンプレート
 *
 * @package  app
 * @extends  Controller
 */

class Controller_Base extends Controller
{
    public function before()
    {
        parent::before();
    }

    public function after($response)
    {
        $response = parent::after($response); // あなた自身のレスポンスオブジェクトを作成する場合は必要ありません。
        return $response; // after() は確実に Response オブジェクトを返すように
    }
}