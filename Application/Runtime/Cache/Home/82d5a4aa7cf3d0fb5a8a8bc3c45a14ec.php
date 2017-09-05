<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/caiwu/Public/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="/caiwu/Public/js/jquery.js"></script>
<script type="text/javascript">
$(function(){	
	//顶部导航切换
	$(".nav li a").click(function(){
		$(".nav li a.selected").removeClass("selected")
		$(this).addClass("selected");
	})	
})	
</script>


</head>

<body style="background:url(/caiwu/Public/images/topbg.gif) repeat-x;">

    <div class="topleft">
    <a href="main.html" target="_parent"><img src="/caiwu/Public/images/logo.png" title="系统首页" /></a>
    </div>
        
    <ul class="nav">
    <li><a href="default.html" target="rightFrame" class="selected"><img src="/caiwu/Public/images/icon01.png" title="工作台" /><h2>工作台</h2></a></li>
   <!--  <li><a href="imgtable.html" target="rightFrame"><img src="/caiwu/Public/images/icon02.png" title="模型管理" /><h2>模型管理</h2></a></li>
    <li><a href="imglist.html"  target="rightFrame"><img src="/caiwu/Public/images/icon03.png" title="模块设计" /><h2>模块设计</h2></a></li>
    <li><a href="tools.html"  target="rightFrame"><img src="/caiwu/Public/images/icon04.png" title="常用工具" /><h2>常用工具</h2></a></li>
    <li><a href="computer.html" target="rightFrame"><img src="/caiwu/Public/images/icon05.png" title="文件管理" /><h2>文件管理</h2></a></li>
    <li><a href="tab.html"  target="rightFrame"><img src="/caiwu/Public/images/icon06.png" title="系统设置" /><h2>系统设置</h2></a></li> -->
    </ul>
            
    <div class="topright">    
    <ul>
    <!-- <li><span><img src="/caiwu/Public/images/help.png" title="帮助"  class="helpimg"/></span><a href="#">帮助</a></li>
    <li><a href="#">关于</a></li> -->
    <li><a href="<?php echo u('logout');?>" target="_parent">退出</a></li>
    </ul>
     
    <div class="user">
    <?php if($user_data['user_type'] == 1): ?><span>管理员：<?php echo ($user_data['user_account']); ?></span>
    <?php else: ?>
        <span>家族长：<?php echo ($user_data['user_id']); ?></span><?php endif; ?>
    
    <!-- <i>消息</i>
    <b>5</b> -->
    </div>    
    
    </div>

</body>
</html>