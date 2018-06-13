<?php
namespace Common\Libs;
/**
*工具类 
*/
class Tool
{
    //生成指定个数的随机字符
    public static function get_id_code_ra(){
        $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '9', '-', '_');
        $sjzf = '';
        for($i = 0; $i < 20; $i++)
        {
            $sjzf .= $chars[mt_rand(0,count($chars)-1)];
        }
        return $sjzf;
    }

    //输出友好的时间
    public static function mydate($time = NULL) {
        $text = '';
        if($time === NULL) {
            $time = time();
        }

        $t = time() - $time; //时间差 （秒）

        switch($t) {
            case $t == 0 :
                $text = '刚刚'; // 一分钟内
                break;
            case $t < 60 :
                $text = $t . '秒前'; // 一分钟内
                break;
            case $t < 3600 :
                $text = floor($t / 60) . '分钟前'; //一小时内
                break;
            case $t < 3600 * 24 :
                $text = floor($t / (60 * 60)) . '小时前'; // 一天内
                break;
            case $t < 3600 * 24 * 3 :
                $text = floor($time/(60*60*24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
                break;
            case $t < 3600 * 24 * 30 :
                $text = date('m月d日 H:i', $time); //一个月内
                break;
            case $t < 3600 * 24 * 365 :
                $text = date('m月d日', $time); //一年内
                break;
            default:
                $text = date('Y年m月d日', $time); //一年以前
                break;
        }
        return $text;
    }


            /**
         * 验证输入的邮件地址是否合法
         *
         * @access  public
         * @param   string      $email      需要验证的邮件地址
         *
         * @return bool
         */
        function is_email($user_email)
        {
            $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
            if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
            {
                if (preg_match($chars, $user_email))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        /**
         * 检查是否为一个合法的时间格式
         *
         * @access  public
         * @param   string  $time
         * @return  void
         */
        function is_time($time)
        {
            $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

            return preg_match($pattern, $time);
        }
    
}
