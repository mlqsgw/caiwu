<?php  
/** 
 * TODO 基础分页的相同代码封装，使前台的代码更少 
 * @param $count 要分页的总记录数 
 * @param int $pagesize 每页查询条数 
 * @return \Think\Page 
 */  
function getpage($count, $pagesize = 10) {  
    $p = new Think\Page($count, $pagesize);  
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录 第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');  
    $p->setConfig('prev', '上一页');  
    $p->setConfig('next', '下一页');  
    $p->setConfig('last', '末页');  
    $p->setConfig('first', '首页');  
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');  
    $p->lastSuffix = false;//最后一页不显示为总页数  
    return $p;  
}  


/**
 * 上传文件类型控制 此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler   返回ajax的json格式数据
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               // 上传文件最大为50M
                'rootPath'  =>  './',                   // 文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         // 文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     // 上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   // 自动使用子目录保存上传文件 默认为true
                'exts'      =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                echo json_encode($data);
            }
        }
    }
}
?>  