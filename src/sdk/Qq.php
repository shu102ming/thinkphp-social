<?php
/**
 * QQ SDK
 */

namespace shu\social\sdk;

use shu\social\Oauth;

class Qq extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://graph.qq.com/oauth2.0/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://graph.qq.com/oauth2.0/token';

    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'scope=get_user_info';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://graph.qq.com/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api QQ API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* 腾讯QQ调用公共参数 */
        $params = array(
            'oauth_consumer_key' => $this->AppKey,
            'access_token' => $this->Token['access_token'],
            'openid' => $this->openid(),
            'format' => 'json'
        );
        $data = $this->http($this->url($api), $this->param($params, $param), $method);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend)
    {
        parse_str($result, $data);
        if ($data['access_token'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取腾讯QQ ACCESS_TOKEN 出错：{$result}");
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid($unionid=false)
    {
        if ($unionid){
            return $this->unionid();
        }
        $data = $this->Token;
        if ($data['access_token']) {
            $data = $this->http($this->url('oauth2.0/me'), array('access_token' => $data['access_token']));
            $data = json_decode(trim(substr($data, 9), " );\n"), true);
            if (isset($data['openid']))
                return $data['openid'];
            else
                throw new \Exception("获取用户openid出错：{$data['error_description']}");
        } else {
            throw new \Exception('没有获取到openid！');
        }
    }

    /**
     * 获取当前授权应用的unionid
     * @return string
     */
    public function unionid()
    {
        $data = $this->Token;
        if ($data['access_token']) {
            $data = $this->http($this->url('oauth2.0/me'), array('access_token' => $data['access_token'],'unionid'=>1));
            $data = json_decode(trim(substr($data, 9), " );\n"), true);
            if (isset($data['unionid']))
                return $data['unionid'];
            else
                throw new \Exception("获取用户unionid出错：{$data['error_description']}");
        } else {
            throw new \Exception('没有获取到unionid！');
        }
    }
}