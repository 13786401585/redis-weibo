<?php
include('lib.php');
include('head.php');
if (($user = isLogin()) === false) {
    header('location:index.php');
    exit;
}
$r = connredis();
$follower = $r->sCard('follower:' . $user['userid']);
$following = $r->sCard('following:' . $user['userid']);


//拉取关注和自己的userid
$star=$r->sMembers('following:'.$user['userid']);
$star[]= $user['userid'];
$lastpull = $r->get('lastpull:userid:'.$user['userid']);
if(!$lastpull){
    $lastpull=0;
}
//拉取新的博文id
$newpost = array();
foreach ($star as $v) {
    $post = $r->zRangeByScore('starpost:userid:'.$v, $lastpull, '+inf');
    $newpost = array_merge($newpost,$post);
}
//判断是否有新的博文id
if(!empty($newpost)){
    sort($newpost,SORT_NUMERIC);
    $r->set('lastpull:userid:'.$user['userid'],end($newpost)+0.1);
    foreach ($newpost as $value){
        $r->lPush('recivepost:'.$user['userid'],$value);
    }
    $r->lTrim('recivepost:'.$user['userid'], 0, 999);
}
//获取关注与自己的1000条博文id
$newpostlist = $r->sort('recivepost:' . $user['userid'],array('sort'=>'desc'));

?>

<div id="postform">
    <form method="POST" action="post.php">
        <?php echo $user['username']; ?>, 有啥感想?
        <br>
        <table>
            <tr>
                <td><textarea cols="70" rows="3" name="status"></textarea></td>
            </tr>
            <tr>
                <td align="right"><input type="submit" name="doit" value="Update"></td>
            </tr>
        </table>
    </form>
    <div id="homeinfobox">
        <?= $follower ?> 粉丝<br>
        <?= $following ?> 关注<br>
    </div>
</div>
<?php
foreach ($newpostlist as $postid) {
    $v= $r->hMGet('post:postid:'.$postid,array('userid','username','time','content'));
    ?>
    <div class="post">
        <a class="username" href="profile.php?u=<?=$v['username']?>"><?=$v['username']?></a> <?= $v['content'] ?><br>
        <i><?= daterange(time(),$v['time'])?> 通过 web发布</i>
    </div>
    <?php
}
?>
<?php
include('footer.php');
?>
