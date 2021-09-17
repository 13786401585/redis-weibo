<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/14
 * Time: 10:34
 */

//基础函数库

function P($key){
    return $_POST[$key];
}
function G($key){
    return $_GET[$key];
}
function error($msg){
    echo '<div>';
    echo $msg;
    echo '</div>';
    include ('footer.php');
    exit;
}
function connredis(){
    static $r = null;
    if($r !== null ){
        return $r;
    }
    $r = new redis();
    $r->connect('localhost');
    return $r;
}

function isLogin(){
    if(!$_COOKIE['userid'] || !$_COOKIE['username'] ||!$_COOKIE['authsecret'] ){
        return false;
    }
    $r = connredis();
    $authsecret = $r->get('user:userid:'.$_COOKIE['userid'].':authsecret');
    if($_COOKIE['authsecret'] != $authsecret && $authsecret){
        return false;
    }
    return array('userid'=>$_COOKIE['userid'],'username'=>$_COOKIE['username']);
}

function randsecret(){
    $str = 'qwertyuiopasdfghjjklzxcvbnm1234567890!@#$%^&*()';
    return substr(str_shuffle($str),0,16);
}

/**
 * 时间格式变换
 */
function daterange($endday, $staday, $format = 'Y-m-d', $color = '', $range = 3) {
    $value = $endday - $staday;
    if ($value < 0) {
        return '';
    } elseif ($value >= 0 && $value < 59) {
        $return = $value . "秒前";
    } elseif ($value >= 60 && $value < 3600) {
        $min = intval($value / 60);
        $return = $min . "分钟前";
    } elseif ($value >= 3600 && $value < 86400) {
        $h = intval($value / 3600);
        $return = $h . "小时前";
    } elseif ($value >= 86400) {
        $d = intval($value / 86400);
        if ($d > $range) {
            return date($format, $staday);
        } else {
            $return = $d . "天前";
        }
    }
    if ($color) {
        $return = "<span id=\"r_time\" style=\"color:{$color}\">" . $return . "</span>";
    }
    return $return;
}
?>