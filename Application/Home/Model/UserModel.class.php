<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel {
	
	protected $_link = array(
		'VideoProp' => array(
			'mapping_type' => self::HAS_MANY,  //一对多
			'class_name' => 'VideoProp',	// 要关联的模型类名
			'mapping_name' => 'videoProp',	//关联映射名称
			'foreign_key' => 'to_user_id',	//关联的外键名称
		)

	);
	
}
?>