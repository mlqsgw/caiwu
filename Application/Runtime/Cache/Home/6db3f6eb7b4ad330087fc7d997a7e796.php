<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/caiwu/Public/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/caiwu/Public/js/jquery.js"></script>
<script type="text/javascript" src="/caiwu/Public/js/showdate.js"></script>

<style>  
    .pages a,  
    .pages span {  
        display: inline-block;  
        padding: 2px 5px;  
        margin: 0 1px;  
        border: 1px solid #f0f0f0;  
        -webkit-border-radius: 3px;  
        -moz-border-radius: 3px;  
        border-radius: 3px;  
    }  
      
    .pages a,  
    .pages li {  
        display: inline-block;  
        list-style: none;  
        text-decoration: none;  
        color: #58A0D3;  
    }  
      
    .pages a.first,  
    .pages a.prev,  
    .pages a.next,  
    .pages a.end {  
        margin: 0;  
    }  
      
    .pages a:hover {  
        border-color: #50A8E6;  
    }  
      
    .pages span.current {  
        background: #50A8E6;  
        color: #FFF;  
        font-weight: 700;  
        border-color: #50A8E6;  
    }  
</style>  
</head>


<body>

	<div class="place">
    <span>位置：</span>
    <ul class="placeul">
    <li><a href="#">首页</a></li>
    <li><a href="#">财务管理</a></li>
    <li><a href="#">家族长</a></li>
    <li><a href="#">本期直播记录</a></li>
    </ul>
    </div>
    
    <div class="rightinfo">
    
    <div class="tools">
    
    	<!-- <ul class="toolbar">
        <li class="click"><span><img src="/caiwu/Public/images/t01.png" /></span>添加</li>
        <li class="click"><span><img src="/caiwu/Public/images/t02.png" /></span>修改</li>
        <li><span><img src="/caiwu/Public/images/t03.png" /></span>删除</li>
        <li><span><img src="/caiwu/Public/images/t04.png" /></span>统计</li>
        </ul> -->
        
        <ul class="toolbar">
            <li class="click"><a href="<?php echo u('payExport_three');?>"><span></span>导出Excel</a></li>
        </ul>
        <form action="<?php echo u('Index/user_record');?>" method="get">
            <ul class="toolbar1" style="margin-right: 40px;">
             <li>
                <input type="hidden" name="id" value="<?php echo ($user_data["id"]); ?>" />
                <!-- <span>
                    <select name="type">
                        <option value="0">本月数据统计</option>
                        <option value="1">本月上半期数据统计</option>
                        <option value="2">本月下半期数据统计</option>
                    </select>
                </span> -->
                <span>
                开始时间:<input type="text" id="st" name="st" onclick="return Calendar('st');" value="<?php echo ($status_date); ?>" class="text" style="width:85px;"/>-结束时间<input type="text" id="et" onclick="return Calendar('et');" value="<?php echo ($end_date); ?>" name="et" class="text" style="width:85px;"/>
             &nbsp;&nbsp;&nbsp;
             <input type="submit" value="搜索" /></span></li>
            </ul>
        </form>

    </div><br/>
    <span>本期数据统计</span>
    <table class="tablelist">
        <thead>
        <tr>
        <th>主播ID</th>
        <th>昵称</th>
        <th>手机号</th>
        <th>家族昵称</th>
        <th>本期直播时长</th>
        <th>本期魔力值增量</th>
        <th>本期最高在线人数</th>
        <th>本期有效天数</th>
        <th>平均日增魔力</th>
        <th>平均日增粉丝</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td><?php echo ($user_data["id"]); ?></td>
        <td><?php echo ($user_data["nick_name"]); ?></td>
        <td><?php echo ($user_data["mobile"]); ?></td>
        <td><?php echo ($user_data["family"]["name"]); ?></td>
        <td><?php echo ($user_data["time_long_all"]); ?></td>
        <td><?php echo ($user_data["total_ticket"]); ?></td>
        <td><?php echo ($user_data["user_num_max"]); ?></td>
        <td><?php echo ($user_data["valid_day"]); ?></td>
        <td><?php echo ($user_data["magic_add_mean"]); ?></td>
        <td><?php echo ($user_data["fans_add_mean"]); ?></td>
        </tbody>
    </table>
    <br/><br/>
    <span>本期每日数据</span>
    <table class="tablelist">
    	<thead>
    	<tr>
        <th><input name="" type="checkbox" value="" checked="checked"/></th>
        <th>日期</th>
        <th>主播ID</th>
        <th>昵称</th>
        <th>手机号</th>
        <th>家族昵称</th>
        <th>日直播时长</th>
        <th>日魔力值增量</th>
        <th>日粉丝增量</th>
        <th>日最高在线人数</th>
        <th>是否为有效天</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($user_day_data)): $i = 0; $__LIST__ = $user_day_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        <td><input name="" type="checkbox" value="" /></td>
        <td><?php echo ($vo["now_date"]); ?></td>
        <td><?php echo ($vo["user_data"]["id"]); ?></td>
        <td><?php echo ($vo["user_data"]["nick_name"]); ?></td>
        <td><?php echo ($vo["user_data"]["mobile"]); ?></td>
        <td><?php echo ($vo["family_data"]); ?></td>
        <td><?php echo ($vo["time_long"]); ?></td>
        <td><?php echo ($vo["total_ticket"]); ?></td>
        <td><?php echo ($vo["fans_add_num"]); ?></td>
        <td><?php echo ($vo["max_watch_number"]); ?></td>
        <td><?php echo ($vo["valid_day_status"]); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    
   
    <!-- <div class="pages">
    	<?php echo ($page); ?>
    </div> -->
    
    
    <div class="tip">
    	<div class="tiptop"><span>提示信息</span><a></a></div>
        
      <div class="tipinfo">
        <span><img src="/caiwu/Public/images/ticon.png" /></span>
        <div class="tipright">
        <p>是否确认对信息的修改 ？</p>
        <cite>如果是请点击确定按钮 ，否则请点取消。</cite>
        </div>
        </div>
        
        <div class="tipbtn">
        <input name="" type="button"  class="sure" value="确定" />&nbsp;
        <input name="" type="button"  class="cancel" value="取消" />
        </div>
    
    </div>
    
    
    
    
    </div>
    
    <script type="text/javascript">
	$('.tablelist tbody tr:odd').addClass('odd');
	</script>

</body>

</html>