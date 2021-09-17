<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/14
 * Time: 11:48
 */

include ('lib.php');
if (($user = isLogin()) === false ){
    header('location:index.php');
    exit;
}
$r = connredis();
$r->del ('user:userid:'.$user['userid'].':authsecret');
setcookie('username','',-1);
setcookie('userid','',-1);
setcookie('authsecret','',-1);
header('location:index.php');
?>