<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/14
 * Time: 11:33
 */
//发微博

include ('lib.php');
include ('head.php');

if (($user = isLogin()) === false ){
    header('location:index.php');
    exit;
}

$content = P('status');
if(!$content ){
    error('请输入发布信息');
}

$r = connredis();
$postid = $r->incr('global:postid');


$r->hMSet('post:postid:'.$postid,array('userid'=>$user['userid'],'username'=>$user['username'],'time'=>time(),'content'=>$content));

//维护个人供粉丝浏览最新20条微博的id
$r->zAdd('starpost:userid:'.$user['userid'],$postid,$postid);
if($r->zCard('starpost:userid:'.$user['userid']) >20){
    $r->zRemRangeByRank('starpost:userid:'.$user['userid'],0,0);
}

//最新50条微博
$r->lPush('newpostlink',$postid);
$r->lTrim('newpostlink',0,49);

//个人微博id 1000
$r->lPush('mypost:userid:'.$user['userid'],$postid);
if($r->lLen('mypost:userid:'.$user['userid']) > 1000){
    $r->rpoplpush('mypost:userid:'.$user['userid'],'global:store');
}



header('location:home.php');
exit;
include ('footer.php');
?>