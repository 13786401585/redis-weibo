<?php
include('lib.php');
include('head.php');

if (($user = isLogin()) === false) {
    header('location:index.php');
    exit;
}
$username = G('u');

$r = connredis();

$prouid = $r->get('user:username:' . $username . ':userid');

if (!$prouid) {
    error('非法用户');
}

$isf = $r->sIsMember('following:' . $user['userid'], $prouid);
$isfstatus = $isf ? '0' : '1';
$isfword = $isf ? '取消关注' : '关注ta';


//获取个人1000条微博id
$newpost = $r->lRange('mypost:userid:' . $prouid, 0, -1);

?>

<h2 class="username"><?= $username ?></h2>
<a href="follow.php?uid=<?= $prouid ?>&f=<?= $isfstatus ?>" class="button"><?= $isfword ?></a>

<?php
foreach ($newpost as $postid) {
    $v = $r->hMGet('post:postid:' . $postid, array('userid', 'username', 'time', 'content'));
    ?>
    <div class="post">
        <a class="username"
           href="profile.php?u=<?= $v['username'] ?>"><?= $v['username'] ?></a> <?= $v['content'] ?><br>
        <i><?= daterange(time(), $v['time']) ?> 通过 web发布</i>
    </div>
    <?php
}
?>

<?php
include('footer.php');
?>
