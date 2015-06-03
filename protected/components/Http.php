<?php

class Http extends CComponent
{

    const HTTP_METHOD_GET = 0;
    const HTTP_METHOD_POST = 1;
    const HTTP_SUCCESS_CODE = 200;
    const HTTP_CONNECT_TIMEOUT = 1;
    const HTTP_TIMEOUT = 3;

    /**
     * @var int 连接超时时间
     */
    public $connectTimeout = 1;

    /**
     * @var int 执行超时时间
     */
    public $timeout = 3;

    /**
     * @var string  附带的COOKIE
     */
    public $cookies = '';

    /**
     * @var string 模拟User-Agent信息
     */
    public $userAgent = '';

    /**
     * @var array 附加请求Header信息
     */
    public $header = array();

    /**
     * @var bool 是否获取响应Header信息
     */
    public $acquireResponseHeader = false;
    /**
     * @var string bundledCrt 证书文件路径
     */
    public $bundledCrt = '';

    public function init()
    {
        if (!function_exists('curl_init')) {
            throw new HttpException(ErrorCode::msg('curl_not_support'), ErrorCode::code('curl_not_support'));
        }
        $this->setConnectTimeout($this->connectTimeout);
        $this->setTimeout($this->timeout);
    }


    /**
     *  执行Curl GET请求
     *
     * @param string $url 请求URL地址
     * @param array  $params 请求参数
     *
     * @throw HttpException
     *
     * @return string 执行结果
     */
    public function get($url, $params = array())
    {
        return $this->request(self::HTTP_METHOD_GET, $url, $params);
    }

    /**
     *  执行Curl POST请求
     *
     * @param string  $url 请求URL地址
     * @param array   $params 请求参数
     * @param boolean $build_post_query 是否转换成键值对形式
     *
     * @throws HttpException
     *
     * @return string 执行结果
     */
    public function post($url, $params = array(), $build_post_query = false)
    {
        return $this->request(self::HTTP_METHOD_POST, $url, $params, $build_post_query);
    }

    /**
     *  执行Curl请求
     *
     * @param int     $httpMethod 执行方法 0:GET 1:POST
     * @param string  $url 请求URL地址
     * @param array   $params 请求参数
     * @param boolean $build_post_query 是否转换成键值对形式
     *
     * @throws HttpException
     *
     * @return string 执行结果
     */
    private function request($httpMethod = self::HTTP_METHOD_GET, $url, $params, $build_post_query = false)
    {
//        $this->parseHttpParams($params);
        $requestUniqueId = $this->generateUniqueId();
        $handler = curl_init();

        if (self::HTTP_METHOD_POST == $httpMethod) {
            curl_setopt($handler, CURLOPT_POST, true);
            curl_setopt($handler, CURLOPT_POSTFIELDS, $build_post_query ? http_build_query($params) : $params);
        } else {
            $url .= $params ? (strrpos($url, '?') ? '' : ($params ? '?' : '')) . http_build_query($params) : '';
        }
        if($this->bundledCrt){
            curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($handler, CURLOPT_CAINFO, $this->bundledCrt);
        }

        curl_setopt_array($handler, array(
                CURLOPT_URL            => $url,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                CURLOPT_TIMEOUT        => $this->timeout,
                CURLOPT_COOKIE         => $this->cookies,
                CURLOPT_HTTPHEADER     => $this->header,
                CURLOPT_USERAGENT      => $this->userAgent,
                CURLOPT_HEADER         => $this->acquireResponseHeader,
                CURLOPT_RETURNTRANSFER => true,
            )
        );
        Yii::log('Http request : ' . implode(' ', array(
                    $requestUniqueId,
                    self::HTTP_METHOD_GET == $httpMethod ? 'GET' : 'POST',
                    $url,
                    self::HTTP_METHOD_GET == $httpMethod ? '' : serialize($params)
                )
            ) . "\n");
        $response = curl_exec($handler);
        //HTTP响应状态码是否正确
        if (self::HTTP_SUCCESS_CODE == curl_getinfo($handler, CURLINFO_HTTP_CODE)) {
            Yii::log('Http response : ' . $requestUniqueId . ' ' . $response . "\n");
            //若需要获取响应Header，则按HTTP规则分割成数组
            if ($this->acquireResponseHeader && $response) {
                $return = explode("\r\n\r\n", $response);
            } else {
                $return = $response ? $response : '';
            }
        } else {
            Yii::log('Http response error : ' . implode(' ', array(
                        $requestUniqueId,
                        curl_getinfo($handler, CURLINFO_HTTP_CODE),
                        curl_errno($handler),
                        curl_error($handler),
                        $url,
                    )
                ),CLogger::LEVEL_ERROR);
            $return =  null;
//            throw new HttpException(ErrorCode::msg('curl_execute_error'), ErrorCode::code('curl_execute_error'));
        }
        curl_close($handler);

        return $return;
    }

    /**
     * 解析自定义请求参数
     *
     * @param array $params CURL请求参数
     */
    public function parseHttpParams(&$params)
    {
        //设置COOKIE
        if (isset($params['http_cookie'])) {
            $this->cookies = is_array($params['http_cookie']) ? http_build_query($params['http_cookie'], '', '; ') : $params['http_cookie'];
            unset($params['http_cookie']);
        }
        //设置连接超时时间
        if (isset($params['http_connect_timeout'])) {
            $this->setConnectTimeout($params['http_connect_timeout']);
            unset($params['http_connect_timeout']);
        }
        //设置执行超时时间
        if (isset($params['http_timeout'])) {
            $this->setTimeout($params['http_timeout']);
            unset($params['http_timeout']);
        }
        //设置是否获取响应Header信息
        if (isset($params['http_return_header']) && $params['http_return_header']) {
            $this->acquireResponseHeader = true;
            unset($params['http_return_header']);
        }
        //设置请求附加Header信息
        if (isset($params['http_header']) && $params['http_header']) {
            $this->header = is_string($params['http_header']) ? (array)$params['http_header'] : $this->compileRequestHeaders($params['http_header']);
            unset($params['http_header']);
        }
        //设置请求User-Agent
        if (isset($params['http_user_agent']) && $params['http_user_agent']) {
            $this->userAgent = (string)$params['http_user_agent'];
            unset($params['http_user_agent']);
        }
        //设置授权证书
        if(isset($params['http_crt']) && file_exists($params['http_crt'])) {
            $this->bundledCrt = $params['http_crt'];
            unset($params['http_crt']);
        }
    }

    /**
     * @param int $time 链接超时时间
     */
    public function setConnectTimeout($time)
    {
        $this->connectTimeout = (int)$time <= 0 ? self::HTTP_CONNECT_TIMEOUT : (int)$time;
    }

    /**
     * 设置URL执行超时时间
     *
     * @param int $time 执行超时时间
     */
    public function setTimeout($time)
    {
        $this->timeout = (int)$time <= 0 ? self::HTTP_TIMEOUT : (int)$time;
    }

    /**
     *  生成请求唯一标识
     *
     * @return string 标识
     */
    private function generateUniqueId()
    {
        return uniqid('http_', false);
    }

    /**
     * 拼装Header为Curl形式
     *
     * @param array $headers
     *
     * @return array
     */
    private function compileRequestHeaders($headers)
    {
        $return = array();

        foreach ($headers as $key => $value) {
            $return[] = $key . ': ' . $value;
        }

        return $return;
    }
}
