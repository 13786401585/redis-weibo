<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/16
 * Time: 13:54
 */

include 'lib.php';
$r = connredis();
$mysql = new mysqli('127.0.0.1', 'root', 'root', 'weibo');
// 检测连接
if (mysqli_connect_error()) {
    die("数据库连接失败: " . mysqli_connect_error());
}
//将冷数据存入mysql
while ($r->lLen('global:store') >= 1000) {
    $i = 0;
    $sql = 'insert into weibo.post(`postid`,`userid`,`username`,`time`,`content`) values ';
    while ($i++ < 1000) {
        $postid = $r->rPop('global:store');
        $post = $r->hMGet('post:postid:' . $postid, array('userid', 'username', 'time', 'content'));
        $sql .= "(" . $postid . "," . $post['userid'] . ",'" . $post['username'] . "'," . $post['time'] . ",'" . $post['content'] . "'),";
    }
    $sql = substr($sql, 0, -1);
    if ($mysql->query($sql) === TRUE) {
        echo '插入成功';
    } else {
        echo '插入失败';
    }
}
$mysql->close();

echo 'ok';
?>