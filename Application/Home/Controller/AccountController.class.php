<?php
namespace Home\Controller;
use Think\Controller;
class AccountController extends Controller {

	//家族结算
	public function Index(){
        set_time_limit(0);
		    //获取当前日期时间
        $now_date = date("Y-m-d",time());
        //获取当前的年月
        $now_year_month = date("Y-m",time());
        $now_year_month = str_replace("-","",$now_year_month); //去掉时间格式的横线
        //获取当前天日期
        $now_day = date("d",time());
        // $now_day = 16;

        if($now_day >= 16){
              //获取当前月的第一天凌晨时间
              $now_month_status_date = date('Y-m-01', strtotime(date("Y-m-d")));
              $now_month_status_time = strtotime($now_month_status_date);
              //获取当前月十五号11::59:59时间
              $now_month_centre_time = $now_month_status_time + 86400*15 -1;

              $where_prop_user['create_time'] = array(
                  'between',"$now_month_status_time,$now_month_centre_time"
              );
              //设置数据表单名称
              $table_name_num = $now_year_month;
              $table_name = "fanwe_video_prop_". $table_name_num;

              //设置期数
              ////获取当前月的16号凌晨时间
              $create_time = date('Y-m-16', strtotime(date("Y-m-d")));

              //设置查询上期数据时间
              $last_expect_date = $now_month_status_date;

        } elseif($now_day < 16){
          //获取上月的第一天凌晨时间
              $last_month_status_date = date('Y-m-01', strtotime('-1 month'));
              $last_month_status_time = strtotime($last_month_status_date);
              //获取上月十六号凌晨时间
              // $last_month_sixteen_time = $last_month_status_time + 86400*15;
              $now_month_status_time = $last_month_status_time + 86400*15;

              
              //获取上月的最后一天11:59:59时间
              $last_month_end_date = date('Y-m-t', strtotime('-1 month'));
              // $last_month_end_time = strtotime($last_month_end_date) + 86400 -1;
              $now_month_centre_time = strtotime($last_month_end_date) + 86400 -1;


              $where_prop_user['create_time'] = array(
                  'between',"$last_month_sixteen_time,$last_month_end_time"
              );

              //设置数据表单名称
              $table_name_num = $now_year_month - 1;
              $table_name = "fanwe_video_prop_". $table_name_num;

              //设置期数
              //获取当前月的第一天凌晨时间
              $create_time = date('Y-m-01', strtotime(date("Y-m-d")));

              //设置查询上期数据时间
              $last_expect_date = date("Y-m-d", $now_month_status_time);
        } 

        $m_family = M('family');
        $where = array(
            "status" => 1
        );

        $list = $m_family->where($where)->field('id,name,user_id,status')->select();
        $m_videoProp = D('videoProp');
        $m_user = D('user');
        foreach ($list as $key => $value) {
            $family_id = $value["id"];

            //获取一个家族下的所有主播的魔力总值
            $sql = "select family_id, to_user_id ,SUM(total_ticket) as total_ticket
                    from $table_name
                    where create_time >= $now_month_status_time AND create_time < $now_month_centre_time AND family_id = $family_id
                    GROUP BY family_id,to_user_id";

            $ticket_all_num = 0;
            $ticket_all_num_no = 0;
            $res = $m_videoProp->query($sql);

            foreach ($res as $key2 => $val2) {
                $ticket_num = 0;
                $ticket_num_no = 0;
                $total_ticket = 0;
                $total_ticket = $val2['total_ticket'];

                if($total_ticket >= 20000 ){ //判断主播魔力值是否大于20000
                  $ticket_num = $total_ticket; //一个主播可结算魔力值
                } else {
                    $ticket_num_no = $total_ticket; //一个主播未结算魔力值
                } 

                $ticket_all_num += $ticket_num;
                $ticket_all_num_no += $ticket_num_no;

            }

            $list[$key]['total_ticket'] = $ticket_all_num;
            $list[$key]['total_ticket_no'] = $ticket_all_num_no;
            $list[$key]['earnings'] = $ticket_all_num * 0.329/100;

            //获取家族长信息
            $where_family_user['id'] = $value['user_id'];
            $family_family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

            // $list[$key]['family_data'] = $family_user_data;
            // $list[$key]['user_data'] = $family_family_user_data;
            $list[$key]['create_time'] = $create_time;
            $list[$key]['nick_name'] = $family_family_user_data['nick_name'];
        }        


        $m_familySettlementHistory = D('familySettlementHistory');

        foreach ($list as $key => $value) {
        	//获取上期未结算魔力值
        	
        	$where_last_expect = array(
        		"expect_date" => $last_expect_date,
        		"user_id" => $value["user_id"]
        	); 


        	$last_expect_data = $m_familySettlementHistory->where($where_last_expect)->find();//获取该家族上期数据

        	$total_ticket_size = $last_expect_data["total_ticket_no"] + $value["total_ticket_no"];

        	if($total_ticket_size >= 20000) {
        		$total_ticket = $value["total_ticket"] + $total_ticket_size;
        		$total_ticket_no = 0;
        	} else {
        		$total_ticket = $value["total_ticket"];
        		$total_ticket_no = $last_expect_data["total_ticket_no"] + $value["total_ticket_no"];
        	}


        	$earnings = $total_ticket * 0.329/100;
        	$add_data = array(
        		"user_id" => $value["user_id"],
        		"nick_name" => $value["nick_name"],
        		"name" => $value["name"],
        		"total_ticket" => $total_ticket,
        		"total_ticket_no" => $total_ticket_no,
        		"earnings" => $earnings,
        		"coefficient" => "0.329",
        		"expect_date" => $value["create_time"],
        		"create_time" => time()
        	);
          
        	$result = $m_familySettlementHistory->add($add_data);
        }

        if($result){
        	echo "添加成功";
        } else {
        	echo "添加失败";
        }

	}


  // //删除历史记录
  // public function del_history(){
  //     $m_familySettlementHistory = D('familySettlementHistory');
  //     $where = array(
  //         // "expect_date" => "2017-07-16"
  //       "id" => array('gt',0)
  //     );

  //     $result = $m_familySettlementHistory->where($where)->delete();
  //     if($result){
  //         echo "删除成功";
  //     } else {
  //         echo "删除失败";
  //     }
  // }
	
}