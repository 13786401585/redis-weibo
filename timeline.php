<?php
include ('lib.php');
include ('head.php');

if (!isLogin()){
    header('location:index.php');
    exit;
}
$r = connredis();
$newuserlist = $r->sort('newuserlink',array('sort'=>'desc','get'=>'user:userid:*:username'));


$newpostlist = $r->sort('newpostlink',array('sort'=>'desc'));
?>

<h2>热点</h2>
<i>最新注册用户(redis中的sort用法)</i><br>
<div>
    <?php
        foreach ($newuserlist as $value) {
    ?>
            <a class="username" href="profile.php?u=<?= $value ?>"><?= $value ?></a>
    <?php
        }
    ?>
</div>

<br><i>最新的50条微博!</i><br>
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
include ('footer.php');
?>
