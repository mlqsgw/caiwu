<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>欢迎登录皮蛋直播财务后台管理系统</title>
<link href="/caiwu/Public/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="/caiwu/Public/js/jquery.js"></script>
<!-- <script language="JavaScript" src="/caiwu/Public/js/jquery.min.js"></script> -->
<script src="/caiwu/Public/js/cloud.js" type="text/javascript"></script>
<script type="text/javascript" src="/caiwu/Public/js/layer/layer.js"></script>


<script language="javascript">
	$(function(){
    $('.loginbox').css({'position':'absolute','left':($(window).width()-692)/2});
	$(window).resize(function(){  
    $('.loginbox').css({'position':'absolute','left':($(window).width()-692)/2});
    })  
});  
</script> 

</head>

<body style="background-color:#1c77ac; background-image:url(/caiwu/Public/images/light.png); background-repeat:no-repeat; background-position:center top; overflow:hidden;">



    <div id="mainBody">
      <div id="cloud1" class="cloud"></div>
      <div id="cloud2" class="cloud"></div>
    </div>  


<div class="logintop">    
    <span>欢迎登录后台管理界面平台</span>    
    <ul>
    <li><a href="#">回首页</a></li>
    <li><a href="#">帮助</a></li>
    <li><a href="#">关于</a></li>
    </ul>    
    </div>
    
    <div class="loginbody">
    
    <span class="systemlogo"></span> 
       
    <div class="loginbox">
    
    <ul>
    <li><input name="account" type="text" class="loginuser" value=""  placeholder="账号" /></li>
    <li><input name="password" type="password" class="loginpwd" value="" placeholder="密码"/></li>
    <li><input name="" type="button" id="input1" class="loginbtn" value="登录" /></li>
    </ul>
    
    
    </div>
    
    </div>
    
    <div class="loginbm">皮蛋直播财务管理后台</div>


    <script>
        $(".loginbtn").click(function(){
            var account = $(".loginuser").val();
            var password = $(".loginpwd").val();

            if(account == '' || password == '') {
                // layer.open({
                //     title : "提示信息",
                //     content : "账号和密码不能为空！",
                //     btn : ['确定']
                // });
                // location.href = "<?php echo u('Index/login');?>";
                alert("账号和密码不能为空!");
                return false;
            } else {
                $.ajax({
                    type : "POST",
                    url : "<?php echo u('Index/do_login');?>",
                    data : {account:account,password:password},
                    dataType : 'json',
                    success : function(data){
                        if(data["status"]){
                            location.href = "<?php echo u('main');?>";
                        } else {
                            alert(data["message"]);
                        }
                    }
                });
            }
        });
    </script>    

    <script>
        $("body").keydown(function() {
          if (event.keyCode == "13") { //keyCode=13是回车键
            $("#input1").click();
          }
        });  
    </script>
</body>

</html>