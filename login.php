<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/14
 * Time: 11:11
 */

//登录

include ('lib.php');
include ('head.php');

if (isLogin() !=false){
    header('location:home.php');
    exit;
}

$username = P('username');
$password = P('password');

if(!$username || !$password ){
    error('请输入完整登录信息');
}

$r = connredis();
$userid=$r->get('user:username:'.$username.':userid');
if(!$userid){
    error('用户不存在');
}
$realpass = $r->get('user:userid:'.$userid.':password');
if($realpass != $password){
    error('用户名或密码错误');
}

$authsecret = randsecret();
$r->set('user:userid:'.$userid.':authsecret',$authsecret);
setcookie('username',$username);
setcookie('userid',$userid);
setcookie('authsecret',$authsecret);
header('location:home.php');
include ('footer.php');
?>