<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/10
 * Time: 16:26
 */
/**
    注册用户
 * set user:userid:1:username zhangsan
 * set user:userid:1:password 11111
 * set user:uername:zhangsan:userid 1
 *
 *
 * 具体步骤
 * 0.接受post参数
 * 1.连接redis
 * 2.写入redis
 * 3.登录
 */
include ('lib.php');
include ('head.php');
if (isLogin() !=false){
    header('location:home.php');
    exit;
}
$username = P('username');
$password = P('password');
$password2 = P('password2');

if(!$username || !$password || !$password2){
    error('请输入完整注册信息');
}

if ($password !==$password2){
    error('密码不一致');
}

$r = connredis();

if($r->get('user:username:'.$username.':userid')){
    error('用户已存在');
}
$userid = $r->incr('global:userid');
$r->set('user:userid:'.$userid.':username',$username);
$r->set('user:userid:'.$userid.':password',$password);

$r->set('user:username:'.$username.':userid',$userid);

$r->lPush('newuserlink',$userid);
$r->lTrim('newuserlink',0,49);


$authsecret = randsecret();
$r->set('user:userid:'.$userid.':authsecret',$authsecret);
setcookie('username',$username);
setcookie('userid',$userid);
setcookie('authsecret',$authsecret);
header('location:home.php');
include ('footer.php');
?>
