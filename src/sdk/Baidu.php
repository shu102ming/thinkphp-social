<?php
/**
 * Baidu SDK
 */

namespace shu\social\sdk;

use shu\social\Oauth;

class Baidu extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://openapi.baidu.com/oauth/2.0/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://openapi.baidu.com/oauth/2.0/token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://openapi.baidu.com/rest/2.0/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api 百度 API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* 百度调用公共参数 */
        $params = array(
            'access_token' => $this->Token['access_token'],
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
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in'] && $data['refresh_token']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取百度ACCESS_TOKEN出错：{$data['error']}");
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->call('passport/users/getInfo');
        if (isset($data['userid']))
            return $data['userid'];
        else
            throw new \Exception('没有获取到百度用户ID！');
    }
}