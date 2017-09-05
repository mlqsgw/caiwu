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
    <li><a href="#">结算记录表</a></li>
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
            <li class="click"><a href="<?php echo u('payExport_four');?>"><span></span>导出Excel</a></li>
        </ul>
        <form action="<?php echo u('Index/account_list');?>" method="get">
            
            <ul class="toolbar1" style="margin-right: 40px;">
                 <li>
                    <?php if($user_type == 1): ?><span style="margin-top: 10px;">
                            <select name="type">
                                <option value="0">选择搜索方式</option>
                                <option value="1">家族长ID</option>
                                <option value="2">家族长昵称</option>
                                <option value="3" selected="selected">期数</option>
                                <option value="4">期数+家族长昵称</option>
                                <option value="5">期数+家族长ID</option>
                            </select>
                        </span><?php endif; ?>
                    
                 <span style="margin-top: 2px;">
                 期数:<input type="text" id="st" name="st" onclick="return Calendar('st');" value="<?php echo ($where_search); ?>" class="text" style="width:85px;"/>&nbsp;&nbsp;&nbsp;
                    <?php if($user_type == 1): ?><input type="text" name="search_name" style="height:22px;background: #d4e7f0;" placeholder="家族长ID/家族长昵称"><?php endif; ?>
                 
                 <input type="submit" value="搜索" />
                 </span>
                 </li>
            </ul>
        </form>

    </div>
    
    
    <table class="tablelist">
    	<thead>
    	<tr>
        <th><input name="" type="checkbox" value="" checked="checked"/></th>
        <th>编号</th>
        <th>家族长id</th>
        <th>家族长昵称</th>
        <th>家族名称</th>
        <th>本期结算魔力值</th>
        <th>本期未结算魔力值</th>
        <th>本期收益/元</th>
        <th>提成系数</th>
        <th>期数</th>
        <th>创建时间</th>
        <!-- <th>提成系数</th> -->
        <!-- <th>期</th> -->
        <!-- <th>操作</th> -->
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($settlementHistory_data)): $i = 0; $__LIST__ = $settlementHistory_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        <td><input name="" type="checkbox" value="" /></td>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["user_id"]); ?></td>
        <td><?php echo ($vo["nick_name"]); ?></td>
        <td><?php echo ($vo["name"]); ?></td>
        <td><?php echo ($vo["total_ticket"]); ?></td>
        <td><?php echo ($vo["total_ticket_no"]); ?></td>
        <td><?php echo ($vo["earnings"]); ?></td>
        <td><?php echo ($vo["coefficient"]); ?></td>
        <td><?php echo ($vo["expect_date"]); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$vo["create_time"])); ?></td>

        <!-- <td><?php echo ($vo["coefficient"]); ?></td> -->
        <!-- <td>期</td> -->
        <!-- <td><a href="<?php echo u('user_data_list');?>?id=<?php echo ($vo["id"]); ?>" class="tablelink">成员直播明细</a></td> -->
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    
   
    <div class="pages">
        <?php echo ($page); ?>
    </div>
    
    
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
    
    <!-- <script type="text/javascript">
	$('.tablelist tbody tr:odd').addClass('odd');
	</script> -->

</body>

</html>