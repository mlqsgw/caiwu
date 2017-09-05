<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/caiwu/Public/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/caiwu/Public/js/jquery.js"></script>

</head>


<body>

	<div class="place">
    <span>位置：</span>
    <ul class="placeul">
    <li><a href="#">首页</a></li>
    </ul>
    </div>
    
    <div class="mainindex">
    
    
    <div class="welinfo">
    <span><img src="/caiwu/Public/images/sun.png" alt="天气" /></span>
    <?php if($session_data['user_type'] == 1): ?><b>管理员<?php echo ($session_data['user_account']); ?> 你好，欢迎使用信息管理系统</b>
    <?php else: ?>
         <b>家族长<?php echo ($session_data['user_id']); ?> 你好，欢迎使用信息管理系统</b><?php endif; ?>
    <a href="<?php echo u('update_password');?>">修改登录密码</a>
    </div>
    
</body>

</html>