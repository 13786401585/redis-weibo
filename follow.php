<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/14
 * Time: 11:49
 */

include ('lib.php');
include ('head.php');
if (($user = isLogin()) === false ){
    header('location:index.php');
    exit;
}
$r = connredis();

$uid = G('uid');
$status = G('f');

$username = $r->get('user:userid:'.$uid.':username');

if($uid == '' || $status =='' || !$username ||  $uid == $user['userid']){
    error('非法参数');
}

if($status){
    $r->sAdd('following:'.$user['userid'],$uid);
    $r->sAdd('follower:'.$uid,$user['userid']);
}else{
    $r->sRem('following:'.$user['userid'],$uid);
    $r->sRem('follower:'.$uid,$user['userid']);
}

header('location:profile.php?u='.$username);
include ('footer.php');
?>