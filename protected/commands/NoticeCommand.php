<?php

use \application\service\Area;

class NoticeCommand extends CConsoleCommand
{
    public function actionRenqiPaihang(){
        $renqiList = UserAccountArea::model()->getSeePaihang(100);
        $areaService = new Area();
        if($renqiList){
            $message = "专区ID    专区名称    关注数<br>";
            foreach($renqiList as $list){
                //获取专区详情
                $areaInfo = $areaService->getAreaInfo($list['area_id']);
                if(empty($areaInfo)){
                    continue;
                }
                $message .= $list['area_id']."    ".$areaInfo['name']."    ".$list['seeCount']."<br>";
            }
        }
        $to = array(
            'leona@mofang.com',
            'gaofei@mofang.com',
            'qidianze@mofang.com',
            'fengtian@mofang.com',
            'chenmao@mofang.com',
            'elmerzhang@mofang.com',
            'guojia@mofang.com',
            'wangmanyu@mofang.com',
            'zhangjiayu@mofang.com',
        );
        $result = Message::email($to, '人气专区前100名', $message);
        if (!$result) {
            throw new ServiceException(ErrorCode::msg('email_push_failed'), ErrorCode::code('email_push_failed'));
        }
    }
}