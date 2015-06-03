<?php


class UserCommand extends CConsoleCommand
{
    public function actionIncreaseCredits(){
        echo date('Y-m-d')."\n";
        $filename = '/tmp/game_forum_ref.txt';
        $file = fopen($filename, 'r');
        $money = 100;
        while($uid = fgets($file)) {
            $uid = intval($uid);
            $http = "http://u.mofang.com/api/increase_credits?user={$uid}&user_type=2&money={$money}";
            $result = json_decode(Yii::app()->http->get($http),true);
            if($result && $result['code'] == 1){
                echo "uid:".$uid."money:".$money."\n".var_export($result,true)."\n----------------succ----------------\n";
            }else{
                echo "uid:".$uid."money:".$money."\n".var_export($result,true)."\n----------------error----------------\n";
            }
        }
    }
}