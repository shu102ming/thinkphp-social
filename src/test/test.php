<?php

namespace app\admin\controller;
use app\common\controller\Base;
class Oauth extends Base
{
    //登录地址
        public function login($type = null)
        {
            if ($type == null) {
                $this->error('参数错误');
            }
            // 获取对象实例
            $sns = \shu\social\Oauth::getInstance($type);
            //跳转到授权页面
            $this->redirect($sns->getRequestCodeURL());
        }
    
        //授权回调地址
        public function callback($type = null, $code = null)
        {
            if ($type == null || $code == null) {
                $this->error('参数错误');
            }
            $sns = \shu\social\Oauth::getInstance($type);
            // 获取TOKEN
            $token = $sns->getAccessToken($code);
            //获取当前第三方登录用户信息
            if (is_array($token)) {
                $user_info = \shu\social\GetInfo::getInstance($type, $token);
                dump($user_info);// 获取第三方用户资料
                $sns->openid();//统一使用$sns->openid()获取openid
                //$sns->unionid();//QQ和微信、淘宝可以获取unionid
                dump($sns->openid());
                echo '登录成功!!';
                echo '正在持续开发中，敬请期待!!';
            } else {
                echo "获取第三方用户的基本信息失败";
            }
        }
}
