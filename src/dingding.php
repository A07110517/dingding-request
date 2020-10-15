<?php
/**
 * author:asif<1156210983@qq.com>
 * 钉钉相关接口和curl接口
 */
class DingDing
{
    const HOST = "https://oapi.dingtalk.com";

    public static function sendTxt($token, $content, $isAtAll = true)
    {
        $url  = self::HOST . "/robot/send?access_token=$token";
        $data = array(
            'msgtype' => 'text',
            'text'    => array( 'content' => $content ),
            'at'      => array(
                'atMobiles' => array(),
                'isAtAll'   => $isAtAll,
            ),
        );
        $ret  = self::reqDing($url, $data);

        return $ret;
    }

    /**
     * curl请求，post=true为POST请求方式，为false是GET请求方式
     */
    public static function reqDing($url, $postData, $post = true)
    {
        if (is_array($postData)) {
            $postData = json_encode($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json;charset=utf-8' ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = empty($data) ? array() : json_decode($data, true);

        return $data;
    }

    public static function getHoliday1($date = '')
    {
        $holidayUrl = 'http://api.k780.com/?app=life.workday&date=%s&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json';

        if($date === '') {
            $date = date('Ymd', time());
        }
        $url = sprintf($holidayUrl, $date);
        $judgeHolidayRet = self::reqDing($url, []);
        $worknm = $judgeHolidayRet['result']['worknm'];
        if(!isset($judgeHolidayRet['result']['worknm'])) {
            return false;
        }
        if($worknm == '工作日') {
            return 'yes';
        }
        return 'no';
    }

    public static function getHoliday2($date = '')
    {
        $holidayUrl = 'http://timor.tech/api/holiday/info/%s';

        if($date === '') {
            $date = date('Y-m-d', time());
        }
        $url = sprintf($holidayUrl, $date);
        $judgeHolidayRet = self::reqDing($url, [], false);
        $worknm = $judgeHolidayRet['type']['type'];
        if(!isset($judgeHolidayRet['type']['type'])) {
            return false;
        }
        if(($worknm == 0) || ($worknm == 3)) {
            return 'yes';
        }
        return 'no';
    }

    /**
     * 判断date是否为工作日
     * 采用两个开源网站，防止因为一个挂掉而影响功能
     * 是工作日，返回yes，不是工作日返回no，接口全部失败返回false
     */
    public static function judgeWorkDay($date)
    {
        $ret1 = self::getHoliday1(date('Ymd', strtotime($date)));
        if($ret1 == 'yes') {
            return 'yes';
        } elseif($ret1 == 'no') {
            return 'no';
        }
        return self::getHoliday2(date('Y-m-d', strtotime($date)));
    }
}
