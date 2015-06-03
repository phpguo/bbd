<?php

class AccountCommand extends CConsoleCommand
{

    const START_UID = 110000;
    //繁中海外起始ID
    //const START_UID = 2000000000;

    /**
     * 用户数据迁移
     *
     * @param int $start 开始用户ID
     * @param int $end 结束用户ID
     * @param int $needNum 需要导入的用户数, 若为负数则导入全部数据
     * @param int $perTurn 每次导入用户数
     */
    public function actionMigrateAccount($start = -1, $end = -1, $perTurn = 3000, $needNum = -1) {
        $start = (int)$start;
        $end = (int)$end;
        $startSql = -1 == $start ? '' : ' AND `uacc_id` > ' . $start;
        $endSql = -1 == $end ? '' : ' AND `uacc_id` <= ' . $end;
        echo "开始批量迁移用户\n";
        $olddb = Yii::app()->olddb;
        //数据库所有数据数
        $total = $olddb->createCommand("SELECT COUNT(*) AS `total` FROM `user_accounts` WHERE 1 {$startSql} {$endSql}")->queryRow();
        //需要导入数据数
        $total = $needNum < 0 ? $total['total'] : min($needNum, $total['total']);
        //共需循环导入次数
        $totalTurn = (int)ceil($total / $perTurn);
        $startTime = microtime(true);
        echo "共{$total}个用户待导入，每次导入{$perTurn}个，共需要导入{$totalTurn}次\n";
        //开始循环导入
        for ($currentTurn = 0; $currentTurn < $totalTurn; $currentTurn++) {
            echo "第" . ($currentTurn + 1) . "次导入\n";
            $this->migrate($startSql, $endSql, $currentTurn * $perTurn, min($perTurn, $total));
        }
        $endTime = microtime(true);
        echo "总共用时：" . ($endTime - $startTime) . "\n";
    }

    private function migrate($startSql, $endSql, $offset, $size) {
        $db = Yii::app()->db;
        $olddb = Yii::app()->olddb;
        $sql = "SELECT * FROM `user_accounts` as `a` ";
        $sql .= " LEFT JOIN `user_relation_weibo` as `b` ON `a`.`uacc_id` = `b`.`uarw_uid` ";
        $sql .= " LEFT JOIN `user_relation_qq` as `qq` ON `a`.`uacc_id` = `qq`.`uarq_uid` ";
        //$sql .= " LEFT JOIN `user_relation_facebook` as `fb` ON `a`.`uacc_id` = `fb`.`uarf_uid`";
        $sql .= " WHERE 1 {$startSql} {$endSql} ";
        $sql .= " LIMIT {$offset}, {$size} ";
        $accounts = $olddb->createCommand($sql)->queryAll();
        $timestamp = (int)microtime(true) * 1000;
        $fail = array();
        $count = 0;
        foreach ($accounts as $key => $account) {
            echo "同步用户,ID:" . $account['uacc_id'] . "。。。。。。。。。。。。";
            $temp = array();
            $id = self::START_UID + $account['uacc_id'];
            //若已经导入过则跳过
            if ($db->createCommand('SELECT `id` FROM `user_account` WHERE `id` = ' . $id)->queryRow()) {
                echo '用户ID' . $id . " 已导入过\n";
                continue;
            }
            $temp[] = "'" . $id . "'";
            $temp[] = "'" . $account['uacc_email'] . "'";
            $temp[] = "'" . $account['uacc_username'] . "'";
            //用户昵称，若不存在，取用户名
            $temp[] = "'" . ($account['uacc_nickname'] ? $account['uacc_nickname'] : $account['uacc_username']) . "'";
            $temp[] = "'" . $account['uacc_wanyou_id'] . "'";
            $temp[] = "'" . $account['uacc_ucid'] . "'";
            $temp[] = "'" . $account['uacc_password'] . "'";
            $temp[] = "'" . $account['uacc_salt'] . "'";
            //转换性别
            $sex = str_replace(array('1', '0'), array('1', '2'), $account['uacc_sex']);
            $temp[] = "'" . ($sex ? : '2') . "'";
            //邮箱绑定状态
            $temp[] = "'" . (int)!$account['uacc_email_state'] . "'";
            $temp[] = "'" . $account['uacc_ip_address'] . "'";
            $temp[] = "'" . ($account['uacc_pwd_state'] + 1) . "'";
            //注册时间
            $add_timestamp = strtotime($account['uacc_date_added']) * 1000;
            $temp[] = "'" . $add_timestamp . "'";
            $temp[] = "'" . $add_timestamp . "'";
            $temp[] = "'" . $account['uacc_portrait'] . "'";
            $temp[] = "'" . $account['uacc_password_wanyou'] . "'";
            try {
                $db->createCommand("INSERT INTO `user_id_auto_increment` (`id`) VALUES ('{$id}');")->execute();
                $db->createCommand("INSERT INTO `user_account` 
                    (`id`,`email`,`username`,`nickname`,`wanyou_id`,`ucid`,`password_old`,`salt_old`,`sex`,`email_verified`,`ip_address`,`password_state`,`create_timestamp`,`register_timestamp`,`avatar`,`password_wanyou`) VALUES (" . implode(',', $temp) . ");")->execute();
                unset($temp);
                //生成Session_id
                $session_id = substr(md5(microtime(true) . $id), -16);
                $db->createCommand("INSERT INTO `user_account_session` (`uid`, `session_id`,`create_timestamp`) VALUES ('{$id}', '$session_id', '$timestamp');")->execute();
                //微博绑定状态
                if ($account['uarw_id'] && ($tid = $account['uarw_wid']) && ($token = $account['uarw_token'])) {
                    $db->createCommand("INSERT INTO `user_account_thirdparty` (`uid`, `type`,`tid`,`token`,`create_timestamp`) VALUES ('{$id}', '2', '$tid','$token', '$timestamp');")->execute();
                }
                //QQ绑定状态
                if ($account['uarq_id'] && ($tid = $account['uarq_qid']) && ($token = $account['uarq_token'])) {
                    $db->createCommand("INSERT INTO `user_account_thirdparty` (`uid`, `type`,`tid`,`token`,`create_timestamp`) VALUES ('{$id}', '1', '$tid','$token', '$timestamp');")->execute();
                }
                //Facebook绑定状态
//                if ($account['uarf_id'] && ($tid = $account['uarf_fid']) && ($token = $account['uarf_token'])) {
//                    $db->createCommand("INSERT INTO `user_account_thirdparty` (`uid`, `type`,`tid`,`token`,`create_timestamp`) VALUES ('{$id}', '3', '$tid','$token', '$timestamp');")->execute();
//                }
                $from = 0;
                switch ($account['uacc_from']) {
                    case 'web.mofang':
                        $from = 6;
                        break;
                    case 'app.mofang':
                        $from = 3;
                        break;
                    case 'bbs.mofang':
                        $from = 2;
                        break;
                    case 'wanyou':
                        $from = 4;
                        break;
                }
                $db->createCommand("INSERT INTO `user_account_more` (`uid`,`from`) VALUES ('{$id}', '{$from}');")->execute();
                $count++;
                echo "同步用户成功\n";
            } catch (CDbException $e) {
                $fail[] = $account['uacc_id'];
                echo "同步用户" . $account['uacc_id'] . "失败，" . $e->getMessage() . "\n";
            }
            unset($account);
            usleep(5000);
        }
        echo "同步用户结束，共需同步用户" . count($accounts) . "，成功同步用户." . $count . "\n";
    }

    /**
     * 添加用户信息到Solr
     */
    public function actionAccount_solr($start = -1, $end = -1, $num = -1, $per = 30) {
        $start = (int)$start;
        $end = (int)$end;
        //每次导入的用户数
        $per = (int)$per;
        $perTurn = $per ? $per : 30;
        //需要导入的用户数, 若为负数则导入全部数据
        $needNum = (int)$num;
        $startSql = -1 == $start ? '' : ' AND `id` > ' . $start;
        $endSql = -1 == $end ? '' : ' AND `id` <= ' . $end;
        //数据库所有数据数
        $total = Yii::app()->db->createCommand('SELECT COUNT(*) AS `total` FROM `user_account` WHERE `status` = 0 ' . $startSql . $endSql . ';')->queryRow();
        //需要导入数据数
        $total = $needNum < 0 ? $total['total'] : min($needNum, $total['total']);
        //共需循环导入次数
        $totalTurn = (int)ceil($total / $perTurn);
        $startTime = microtime(true);
        //每次倒入用户数
        $perTurn = min($perTurn, $total);
        //开始循环导入
        for ($currentTurn = 0; $currentTurn < $totalTurn; $currentTurn++) {
            $this->addSolr($startSql, $endSql, $currentTurn * $perTurn, $perTurn);
        }
        $endTime = microtime(true);
        echo "总共用时：" . ($endTime - $startTime) . "\n";
    }

    private function addSolr($startSql, $endSql, $offset, $pageSize) {
        $count = 0;
        $documentObjs = array();
        $accounts = Yii::app()->db->createCommand(
            "SELECT `nickname`,`avatar`,`id` FROM `user_account` WHERE `status` = 0 {$startSql} {$endSql} LIMIT {$offset},{$pageSize};"
        )->queryAll();
        $solr = Yii::app()->solrUsercenter;
        foreach ($accounts as $account) {
            $documentObj = new Apache_Solr_Document();
            $documentObj->id = $account['id'];
            $documentObj->nickname = $account['nickname'];
            $avatar = new Avatar('', $account['avatar']);
            $documentObj->avatar = $avatar->getAvatarUrl();
            try {
                echo "添加用户信息至Solr id ---->" . $account['id'] . "\n";
//                $solr->addDocument($documentObj);
                $documentObjs[] = $documentObj;
                $count += 1;
            } catch (Exception $e) {
                echo 'Solr error: ' . $e->getMessage() . ' id:' . $account->id . "\n";
            }
        }
        //全部提交
        try {
            $solr->addDocuments($documentObjs);
            $solr->commit();
            $solr->optimize();
            echo "总共待提交" . count($accounts) . ",成功添加{$count}个用户至solr, \n";
        } catch (Exception $e) {
            echo 'Solr 提交出现错误: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * @method 同步用户至Discuz
     * @param string $start 开始日期，相对时间格式
     * @param string $end 结束日期
     * @param        int num 需要导入的用户数目
     * @param        int per 每次导入的用户数目
     */
    public function actionSync_to_discuz($start = -1, $end = -1, $num = -1, $per = 30) {
        $msg = "开始同步用户至UCenter\n";
        //每次导入的用户数
        $per = (int)$per;
        $perTurn = $per ? $per : 30;
        //需要导入的用户数, 若为负数则导入全部数据
        $num = (int)$num;
        $needNum = -1 == $num ? $num : $num;
        $startSql = ' AND `register_timestamp` > ' . (-1 == $start || !strtotime($start) ? strtotime('-1 day') : strtotime($start)) * 1000;
        $endSql = ' AND `register_timestamp` <= ' . (-1 == $end || !strtotime($end) ? time() : '' . strtotime($end)) * 1000;
        //数据库所有数据数
        $total = Yii::app()->db->createCommand("SELECT COUNT(*) AS `total` FROM `user_account` WHERE `ucid`=0 AND `status` = 0 {$startSql} {$endSql};")->queryRow();
        if (!$total['total']) {
            $msg .= "没有用户需要同步\n";
            echo $msg;

            return;
        }
        //需要导入数据数
        $total = $needNum < 0 ? $total['total'] : min($needNum, $total['total']);
        //共需循环导入次数
        $totalTurn = (int)ceil($total / $perTurn);
        //每次倒入用户数
        $perTurn = min($perTurn, $total);
        $msg .= "\n需要同步{$total}个用户，每次同步{$perTurn}个用户，共同步{$totalTurn}次\n\n";
        echo $msg;
        $startTime = microtime(true);
        //开始循环导入
        for ($currentTurn = 0; $currentTurn < $totalTurn; $currentTurn++) {
            $this->syncToDiscuz($startSql, $endSql, $currentTurn * $perTurn, $perTurn);
        }
        $endTime = microtime(true);
        $msg = "总共用时：" . ($endTime - $startTime) . " 秒\n";
        echo $msg;
    }

    private function syncToDiscuz($startSql, $endSql, $offset, $pageSize) {
        $count = 0;
        $accounts = Yii::app()->db->createCommand("SELECT `nickname`,`avatar`,`id`,`username`,`email` FROM `user_account` WHERE `ucid`=0 AND `status` = 0 {$startSql} {$endSql} LIMIT {$offset},{$pageSize};")->queryAll();
        foreach ($accounts as $account) {
            file_put_contents('/tmp/nucoin.uid',$account['id'],FILE_APPEND);
            $obj = new UserAccount;
            $obj->nickname = $account['nickname'];
            $obj->avatar = $account['avatar'];
            if (!$account['email']) {
                $obj->email = "mf_" . $account['id'] . "@mofang.com";
            } else {
                $obj->email = $account['email'];
            }
            $obj->username = $account['username'];
            $obj->rawPassword = uniqid();
            try {
                $discuz = new \application\service\DiscuzAccount($obj);
                $discuz->save();
                if ($discuz->ucid) {
                    Yii::app()->db->createCommand("UPDATE `user_account` SET `ucid`=" . $discuz->ucid . " WHERE `id`=" . $account['id'] . ";")->execute();
                    $count++;
                    $msg = "同步用户 " . $account['id'] . " 至UCenter成功 ,用户UCID " . $discuz->ucid . "\n";
                    echo $msg;
                }
            } catch (Exception $e) {
                //若为三方账户，则处理超时
                $remail = $account['email'] ? $account['email'] : "mf_" . $account['id'] . "@mofang.com";
                if ($account['email'] && $user = uc_get_user_by_email($account['email'])) {
                    @list($ucid, $username, $email) = $user;
                    if ($email == $remail) {
                        $msg = $account['id'] . "三方账户，超时导致未记录Ucid，现在补Ucid\n";
                        Yii::app()->db->createCommand("UPDATE `user_account` SET `ucid`=" . $ucid . " WHERE `id`=" . $account['id'] . ";")->execute();
                        $count++;
                        echo $msg;
                        continue;
                    }
                } else if ($user = uc_get_user($account['username'])) {
                    @list($ucid, $username, $email) = $user;
                    if ($email == $remail) {
                        $msg = $account['id'] . "三方账户，超时导致未记录Ucid，现在补Ucid\n";
                        Yii::app()->db->createCommand("UPDATE `user_account` SET `ucid`=" . $ucid . " WHERE `id`=" . $account['id'] . ";")->execute();
                        $count++;
                        echo $msg;
                        continue;
                    }
                }
                $msg = "同步用户 " . $account['id'] . " 至UCenter失败 " . $e->getMessage() . "\n";
                echo $msg;
            }
        }
        $msg = "\n待同步用户数目 " . count($accounts) . "，成功同步用户数目 {$count}\n";
        echo $msg;
    }

    /**
     * 同步用户万游密码
     */
    public function actionWanyouPassword() {
        $db = Yii::app()->db;
        $olddb = Yii::app()->olddb;
        $oldusers = $olddb->createCommand('select uacc_id, uacc_password_wanyou from user_accounts where uacc_password_wanyou != \'\' order by uacc_id')->queryAll();
        foreach ($oldusers as $key => $olduser) {
            try {
                $id = self::START_UID + $olduser['uacc_id'];
                echo '更新用户万游密码，用户ID ' . $id . '......';
                if ($db->createCommand('UPDATE `user_account` SET `password_wanyou` = \'' . $olduser['uacc_password_wanyou'] . '\' WHERE `id` = ' . $id)->execute()) {
                    echo "更新成功\n";
                } else {
                    echo "用户不存在\n";
                }
            } catch (CDbException $e) {
                echo '同步用户失败' . $e->getMessage() . "\n" . $olduser['uacc_id'] . "\n";
            }
        }
    }

    /**
     * 同步用户三方信息
     */
    public function actionThirdparty() {
        $db = Yii::app()->db;
        $olddb = Yii::app()->olddb;
        $thirdWeibo = $olddb->createCommand('select * from user_relation_weibo where uarw_uid in (select uarw_uid from user_relation_weibo group by uarw_uid having count(uarw_id) > 1) order by uarw_uid;')->queryAll();
        $count = array('weibo' => 0, 'qq' => 0);
        foreach ($thirdWeibo as $key => $weibo) {
            try {
                $id = self::START_UID + $weibo['uarw_uid'];
                echo '更新用户微博帐号，用户ID' . $id . '......';
                if (isset($thirdWeibo[$key + 1]) && $weibo['uarw_uid'] == $thirdWeibo[$key + 1]['uarw_uid']) {
                    echo "不是最后一个三方帐号\n";
                    continue;
                }
                if ($db->createCommand('SELECT `id` FROM `user_account` WHERE `id` = ' . $id)->queryRow()) {
                    $count['weibo']++;
                    echo "更新成功\n";
                    $db->createCommand("UPDATE `user_account_thirdparty` SET `tid` = '" . $weibo['uarw_wid'] . "', `token` = '" . $weibo['uarw_token'] . "' WHERE `uid` = {$id} AND `type` = 2")->execute();
                } else {
                    echo "用户不存在\n";
                }
            } catch (CDbException $e) {
                echo '同步用户失败' . $e->getMessage() . $weibo['uarw_uid'] . "\n";
            }
        }
        $thirdQq = $olddb->createCommand('select * from user_relation_qq where uarq_uid in (select uarq_uid from user_relation_qq group by uarq_uid having count(uarq_id) > 1) order by uarq_uid;')->queryAll();
        foreach ($thirdQq as $key => $qq) {
            try {
                $id = self::START_UID + $qq['uarq_uid'];
                echo '更新用户QQ帐号，用户ID' . $id . '......';
                if (isset($thirdQq[$key + 1]) && $qq['uarq_uid'] == $thirdQq[$key + 1]['uarq_uid']) {
                    echo "不是最后一个三方帐号\n";
                    continue;
                }
                if ($db->createCommand('SELECT `id` FROM `user_account` WHERE `id` = ' . $id)->queryRow()) {
                    $count['qq']++;
                    echo "更新成功\n";
                    $db->createCommand("UPDATE `user_account_thirdparty` SET `tid` = '" . $qq['uarq_qid'] . "', `token` = '" . $qq['uarq_token'] . "' WHERE `uid` = {$id} AND `type` = 1")->execute();
                } else {
                    echo "用户不存在\n";
                }
            } catch (CDbException $e) {
                echo "同步用户失败" . $e->getMessage() . $qq['uarq_uid'] . "\n";
            }
        }
//        $thirdFB = $olddb->createCommand('select * from user_relation_facebook where uarf_uid in (select uarf_uid from user_relation_facebook group by uarf_uid having count(uarf_id) > 1) order by uarq_uid;')->queryAll();
//        foreach ($thirdFB as $key => $fb) {
//            try {
//                $id = self::START_UID + $fb['uarf_uid'];
//                echo '更新用户Facebook帐号，用户ID' . $id . '......';
//                if (isset($thirdFB[$key + 1]) && $fb['uarf_uid'] == $thirdFB[$key + 1]['uarf_uid']) {
//                    echo "不是最后一个三方帐号\n";
//                    continue;
//                }
//                if ($db->createCommand('SELECT `id` FROM `user_account` WHERE `id` = ' . $id)->queryRow()) {
//                    $count['facebook']++;
//                    echo "更新成功\n";
//                    $db->createCommand("UPDATE `user_account_thirdparty` SET `tid` = '" . $fb['uarf_fid'] . "', `token` = '" . $fb['uarf_token'] . "' WHERE `uid` = {$id} AND `type` = 1")->execute();
//                } else {
//                    echo "用户不存在\n";
//                }
//            } catch (CDbException $e) {
//                echo "同步用户失败" . $e->getMessage() . $fb['uarf_uid'] . "\n";
//            }
//        }
        echo '成功更新用户三方，微博 ' . $count['weibo'] . ' 个， QQ ' . $count['qq'] . " 个\n";
    }

    public function actionHelp() {
        echo "比较懒，先看源码吧";
    }

    public function actionDelPreAccount(){
        $db = Yii::app()->db;
        $maxId = $db->createCommand('SELECT max(`id`) as maxid FROM `user_account`')->queryRow();
        $maxId = $maxId['maxid'];
        $preCount = $db->createCommand('select count(id) as count from user_account where id<'.$maxId.' and register_timestamp=0 order by id')->queryRow();
        $limit = 1000;
        if($preCount){
            $forCount = ceil($preCount['count']/$limit);
        }
        for($i=1;$i<=$forCount;$i++){
            $offset = ($i-1)*$limit;
            $ids = $db->createCommand("select id from user_account where id<".$maxId." and register_timestamp=0 order by id limit {$offset},{$limit}")->queryAll();
            if($ids){
                foreach($ids as $id){
                    $idsArr[] = $id['id'];
                }
            }
            if($idsArr){
                $implodeIds = implode(',',$idsArr);
                $delUserAccount = "delete from user_account where id in ({$implodeIds})";
                $delUserAccountSession = "delete from user_account_session where uid in ({$implodeIds})";
                $db->createCommand($delUserAccount)->query();
                $db->createCommand($delUserAccountSession)->query();
            }
        }
    }
}

