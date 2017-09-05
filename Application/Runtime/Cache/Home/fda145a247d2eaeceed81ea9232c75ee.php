<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/caiwu/Public/css/style.css" rel="stylesheet" type="text/css" />
<link href="/caiwu/Public/js/webuploader/webuploader.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" src="/caiwu/Public/js/jquery.js"></script>
<script language="JavaScript" src="/caiwu/Public/js/webuploader/webuploader.js"></script>
<style>
    #user_id {width: 115px;}
</style>

</head>

<body>

	<div class="place">
    <span>位置：</span>
    <ul class="placeul">
    <li><a href="#">首页</a></li>
    <li><a href="#">家族添加</a></li>
    </ul>
    </div>
    <form id="data_form">
        <div class="formbody">
            <div class="formtitle"><span>家族添加</span></div>
            <ul class="forminfo">
                
                <li><label>家族长ID</label><input name="user_id" type="text"  value="" class="dfinput" id="user_id"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;家族长昵称&nbsp;&nbsp;&nbsp;&nbsp;<input name="nice_name" type="text" readonly="relation" value="" class="dfinput" id="user_id"/></li>
                <li><label>家族名称</label><input name="name" type="text" class="dfinput" /></li>
                <li><label>提成系数</label><input name="text" type="text" class="dfinput" /></li>
                <li><label>家族宣言</label><textarea name="manifesto" id="" cols="55" rows="10" style="background-color: #ffffff; border: 1px #D7D7D7 solid;font-size: 11px;"></textarea></li>
                <li><label>家族头像</label>
                    <div id="uploader-demo">
                        <!--用来存放item-->
                        <div id="fileList" class="uploader-list"></div>
                        <div id="filePicker">选择图片</div>
                    </div>
                </li>
                <li><label>备注</label><textarea name="memo" id="" cols="55" rows="10" style="background-color: #ffffff; border: 1px #D7D7D7 solid;font-size: 11px;"></textarea></li>
                <li><label>&nbsp;</label><input name="" type="button" class="btn" value="确认保存"/></li>
            </ul>
        </div>

    </form>
   
    
    <script type="text/javascript">
        $(".btn").click(function(){
            $.ajax({
                type : "POST",
                url : "<?php echo u('Index/do_update_password');?>",
                data : $("#data_form").serializeArray(),
                dataType : 'json',
                success : function(data){
                    if(data['status']){
                        alert("修改成功");

                    } else {
                        alert(data["message"]);
                    }
                }

            });
        });
    </script>
    
    <script type="text/javascript">
        // 初始化Web Uploader
        var uploader = WebUploader.create({

            // 选完文件后，是否自动上传。
            auto: true,

            // swf文件路径
            swf: BASE_URL + '/js/Uploader.swf',

            // 文件接收服务端。
            server: 'http://webuploader.duapp.com/server/fileupload.php',

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',

            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });

        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            var $li = $(
                    '<div id="' + file.id + '" class="file-item thumbnail">' +
                        '<img>' +
                        '<div class="info">' + file.name + '</div>' +
                    '</div>'
                    ),
                $img = $li.find('img');


            // $list为容器jQuery实例
            $list.append( $li );

            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            // thumbnailWidth x thumbnailHeight 为 100 x 100
            uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }

                $img.attr( 'src', src );
            }, thumbnailWidth, thumbnailHeight );
        });

        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress span');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<p class="progress"><span></span></p>')
                        .appendTo( $li )
                        .find('span');
            }

            $percent.css( 'width', percentage * 100 + '%' );
        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on( 'uploadSuccess', function( file ) {
            $( '#'+file.id ).addClass('upload-state-done');
        });

        // 文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            var $li = $( '#'+file.id ),
                $error = $li.find('div.error');

            // 避免重复创建
            if ( !$error.length ) {
                $error = $('<div class="error"></div>').appendTo( $li );
            }

            $error.text('上传失败');
        });

        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').remove();
        });
    </script>
</body>

</html>