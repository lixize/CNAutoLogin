<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8" />
    <title>天翼校园网</title>
    <link rel="stylesheet" href="view.css">
  </head>
  <body>
    <div id="main">
    <h2>天翼校园网登陆情况</h2>
    <table id="tbl">
      <tr>
        <td width="20%">账号</td>
        <td width="30%"><?php echo $user?></td>
        <td width="20%">密码</td>
        <td width="30%">********</td>
      </tr>
        <td>验证码</td>
        <td><img src="validatecode.jpeg"></td>
        <td>识别</td>
        <td><?php echo $randcode;?></td>
      <tr>
        <td>状态</td>
        <td colspan="3" style="color:red;"><?php echo $status;?></td>
      </tr>
      <tr>
        <td colspan="4"><a href="<?php echo $mloginurl; ?>">>手动登陆<</a></td>
      </tr>
    </table>
    <p style="color: #D8D8D8;">www.zeyes.org</p>
    </div>
  </body>
</html>