<?php
/**
 * 交互签名算法
 *
 * 此交互算法不受Nginx、Apache等服务软件Rewrite的影响，不受服务框架内部路由规则（Rule）的影响。
 *
 * 为保证公用性约定AppId规则如下：
 *   10xxx 给公司客户端端使用，不含跨公司合作
 *   20xxx 给公司内部交互使用
 *   30xxx 给公司第三方使用，主要是合作方，包含与第三方的合作开发
 *
 * File: MApiSign.php
 * Date: 15-4-15
 * Copyright: 2015 Mofang.com
 */
class BBDApiSign {
    const APPID_MGA = '10001'; // 游戏宝
    const APPID_GIFT = '10002'; // WP礼包
    const APPID_VCR = '10003'; // 录屏软件

    const APPID_LAN_GUANG = '30001'; // 蓝光PC APP

    /**
     * @var array APP对应的密钥，其他交互可另行扩展，不过AppId请使用2段，例如20001
     */
    public $appSecrets = array(
        self::APPID_MGA => '5de48c12848661dc26386d4ff66b0cdb',
        self::APPID_GIFT => 'e7f65c407e23401296d57c6545e0f926',
        self::APPID_VCR => '6d7720a354fde05b754d00a2c79d7c0e',
        self::APPID_LAN_GUANG => '33070de84c4edcc1fc1d60cf83ab90d0',
    );

    /**
     * @var string 计算Sign之前的字符串
     */
    private $_signString = '';

    /**
     * 初始化，如果没有检测POST则做检测
     */
    public function __construct() {
        if (!defined('IS_POST_JSON')) {
            define('IS_POST_JSON', (bool)json_decode(file_get_contents('php://input'), true));
        }
    }

    /**
     * 此方法为兼容Yii组件，并无用处
     */
    public function init() {}

    /**
     * 根据参数生成签名
     *
     * @param $appId string AppId
     * @param $get array Get请求参数
     * @param $post array|string Post请求参数
     * @return string 签名
     */
    public function create($appId, $get, $post = '') {
        //参数校验
        if (empty($appId) || empty($get) || !isset($this->appSecrets[$appId])) {
            return false;
        }

        //填充追加数据
        $get['appid'] = $appId;

        return $this->makeSign($appId, $get, $post);
    }

    /**
     * 检查请求签名
     *
     * @return bool 检测结果
     */
    public function check() {
        // 获取GET请求参数
        $getData = $this->getQueryData();
        $appId = isset($getData['appid']) ? $getData['appid'] : '';
        $sign = isset($getData['sign']) ? $getData['sign'] : '';
        unset($getData['sign']);
        if(empty($appId) || empty($sign) || !isset($this->appSecrets[$appId])) {
            return false;
        }

        // 获取POST参数
        $postData = $this->getPostData();

        // 生成签名
        $makeSign = $this->makeSign($appId, $getData, $postData);
        return $sign === $makeSign;
    }

    /**
     * 获取AppId
     *
     * @return string
     */
    public function getAppId() {
        $getData = $this->getQueryData();

        return isset($getData['appid']) ? $getData['appid'] : '';
    }

    /**
     * 获取计算Sign的字符串
     *
     * @return string
     */
    public function getSignString() {
        return $this->_signString;
    }

    /**
     * 获取QueryData
     *
     * @return array
     */
    private function getQueryData() {
        if (isset($_SERVER['REQUEST_URI'])) {
            $get = array();
            if (false !== ($pos = strpos($_SERVER['REQUEST_URI'], '?'))) {
                parse_str(substr($_SERVER['REQUEST_URI'], $pos + 1), $get);
            }
        } else {
            $get = $_GET;
        }

        return $get;
    }

    /**
     * 获取PostData
     *
     * @return array|string
     */
    private function getPostData() {
        if (defined('IS_POST_JSON') && IS_POST_JSON) {
            $post = file_get_contents('php://input');
        } else {
            $post = $_POST;
        }

        return $post;
    }

    /**
     * 生成一个签名
     *
     * @param $appId string AppId
     * @param $get array Get请求参数
     * @param $post array|string Post请求参数
     * @return string
     */
    private function makeSign($appId, $get, $post) {
        //参数排序
        $getString = $this->getSortString($get);
        $postString = $this->getSortString($post);

        //依次拼接GetString、PostString、约定secrt，并取其md5值即为最终的sign
        $this->_signString = $getString.$postString.$this->appSecrets[$appId];
        return md5($this->_signString);
    }

    /**
     * 获取排序字符串
     *
     * @param $data array|string 参数
     * @return string 排序后参数
     */
    private function getSortString($data) {
        if (is_string($data)) {
            return $data;
        }

        if (empty($data)) {
            return '';
        }

        $str = '';

        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k;
            $str .= is_array($v) ? $this->getSortString($v) : $v;
        }

        return $str;
    }
}
