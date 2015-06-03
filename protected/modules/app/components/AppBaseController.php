<?php
use application\service\exception\ServiceException;
use \application\service\Profile;
use \application\service\Account;
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AppBaseController extends BBDController
{
    //返回成功code
    const COMPATY_SUCCESS = 0;

    //用户id
    protected  $uid;
    //sid
    protected $sid;
    //request类
    protected $request;

    public function init() {

        $this->myInit();
        parent::init();
    }

    public function myInit() {
        //助手原子封装解密
        try{
            if(isset($_GET['atom'])) {
                // 解析原子封装信息
                $atom = base64_decode(urldecode($_GET['atom']));
                $atomArray = array();
                parse_str($atom, $atomArray);
                if(empty($atomArray)){
                    throw new ServiceException(ErrorCode::msg('param_error'), ErrorCode::code('param_error'));
                }
                // 设置用户id
                $this->uid = $atomArray['uid'];
                //sid（session_id）
                $this->sid = $atomArray['sid'];
                //设置请求来源
                $this->request = Yii::app()->request;

            } else {
                throw new ServiceException(ErrorCode::msg('param_error'), ErrorCode::code('param_error'));
            }
        }catch (ServiceException $e){
            Response::send($e->getCode(), $e->getMessage());
            die;
        }
    }

    /**
     * 异常处理
     *
     * @param $exception
     */
    public function handleException($exception) {
        $category = 'exception.' . get_class($exception);
        $message = $exception->__toString();
        if (isset($_SERVER['REQUEST_URI']))
            $message .= "\nREQUEST_URI=" . $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['HTTP_REFERER']))
            $message .= "\nHTTP_REFERER=" . $_SERVER['HTTP_REFERER'];
        $message .= "\n---";
        Yii::log($message, CLogger::LEVEL_ERROR, $category);
        Response::send(ErrorCode::code('system_error'), ErrorCode::msg('system_error'));
        Yii::app()->end(1);
    }

    /**
     * 默认对所有操作检查是否登陆
     *
     * @return array
     */
    public function filters()
    {
        return array(
            'CheckSign',
        );
    }

    /**
     * 过滤器，检查签名
     *
     * @param CFilterChain $filterChain
     */
    public function filterCheckSign(CFilterChain $filterChain)
    {
        if (!isset($_GET['no_auth']) && !Yii::app()->apiSign->check()) {
            Response::send(ErrorCode::code('authentication_failed'),ErrorCode::msg('authentication_failed'));die;
        }

        $filterChain->run();
    }

}

