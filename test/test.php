<?php
date_default_timezone_set("Asia/Shanghai");
require_once('../src/dingding.php');

$token = 'dingding token';
$content = "remind~\n";
$dingdingModel = new DingDing();

//如果为工作日，则发送一条提醒到钉钉群
$judgeHolidayRet = $dingdingModel::judgeWorkDay();
if($judgeHolidayRet == 'yes') {
    $dingdingModel->sendTxt($token, $content);
}
