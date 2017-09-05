<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    

    //判断是否登录
    public function if_login(){
        $u_id = session("user_id");
        if(empty($u_id)){
            // $this->error('请先登录',U('Index/login'));
            $this->redirect('Index/login');
        }
    }

     public function family(){
       $this->if_login();
       $this->display();
    }

    public function top(){
       $this->if_login();
       $session_data = array(
          "user_id" => session("user_id"),
          "user_type" => session("user_type"),
          "user_name" => session("user_name"),
          "user_account" => session("user_account")
       );

       $this->assign("user_data", $session_data);
       $this->display();
    }

    public function left(){
      $this->if_login();
      $session_data = array(
          "user_id" => session("user_id"),
          "user_type" => session("user_type"),
          "user_name" => session("user_name")
      );

      $this->assign("user_data", $session_data);
      $this->display();
    }

    //登录页面
    public function login(){

      $this->display();
    }

    //执行登录
    public function do_login(){
        session_unset();
        $m_family = D('family');
        $user_data = $_POST;

        if($user_data["account"] == '' || $user_data["password"] == ""){
            $result = array(
                "status" => false,
                "message" => "账号和密码不能为空"
            );
        } else {
            $where = array(
              "user_id" => $user_data["account"],
              "user_password" => md5($user_data["password"]),
              "status" => 1
            );

            $family_data = $m_family->where($where)->find();

            $where_caiwu = array(
                "user_account" => $user_data["account"],
                "user_password" => md5($user_data["password"])
            );
            $m_caiwu_admin = D('caiwuAdmin');
            $admin_data = $m_caiwu_admin->where($where_caiwu)->find();

            if($family_data){
                $result = array(
                    "status" => true,
                );
                session("user_id", $user_data["account"]);
                session("user_type", 2); //家族长
            } elseif(!$family_data && $admin_data) {
                $result = array(
                    "status" => true
                );
                session("user_id", $admin_data["id"]);
                session("user_account", $admin_data["user_account"]);
                session("user_type", 1); //管理员
            } else {
                $result = array(
                    "status" => false,
                    "message" => "账号或密码错误"
                );
            }
        }

        $this->ajaxReturn($result);
    }

    //退出登录
    public function logout(){
        session_unset(); //情况session
        $this->redirect('Index/login');
    }

    //修改登录密码
    public function update_password(){
        $session_data = array(
            "user_id" => session("user_id"),
            "user_type" => session("user_type"),
            "user_name" => session("user_name"),
            "user_account" =>session("user_account")
        ); 

        $this->assign("session_data", $session_data);
        $this->display();
    }

    //执行密码修改
    public function do_update_password(){
        $data = $_POST;
        if(!$data['password'] || !$data['new_password'] || !$data['r_password']){
            $result = array(
                "status" => false,
                "message" => "填写信息不完整"
            );
        } elseif($data["new_password"] != $data["r_password"]){
            $result = array(
                "status" => false,
                "message" => "新密码和确认密码不一致"
            );
        } else {
          $where = array(
              "user_id" => $data["user_id"],
              "user_password" => md5($data["password"])
          );

          $m_family = D('family');
          $user_data = $m_family->where($where)->find();

          $where_caiwu = array(
              "user_account" => $data["user_id"],
              "user_password" => md5($data["password"])
          );
          $m_caiwu_admin = D('caiwuAdmin');
          $admin_data = $m_caiwu_admin->where($where_caiwu)->find();

          if($user_data){
              $update_data = array(
                  "user_password" => md5($data["new_password"]),
              );
              
              $update_password = $m_family->where(array('user_id' => $data["user_id"]))->data($update_data)->save();
              
              if($update_password){
                  $result = array(
                      "status" => true,
                  );

              }
          } elseif(!$user_data && $admin_data){
              $update_data = array(
                  "user_password" => md5($data["new_password"]),
              );
              
              $update_password = $m_caiwu_admin->where(array('user_account' => $data["user_id"]))->data($update_data)->save();
              
              if($update_password){
                  $result = array(
                      "status" => true,
                  );
              }
          } else {
              $result = array(
                  "status" => false,
                  "message" => "原密码错误"
              );
          }
        }
        
        $this->ajaxReturn($result);
    }


    public function index(){
      $this->if_login();

      $session_data = array(
          "user_id" => session("user_id"),
          "user_type" => session("user_type"),
          "user_name" => session("user_name"),
          "user_account" =>session("user_account")
      ); 

      $this->assign("session_data", $session_data);
      $this->display();
    }

    public function main(){
       $this->if_login();
        
       $this->display();
    }


    //家族长类表
    public function right(){
       set_time_limit(0);
       $this->if_login();
       //获取家族长数据列表
       $m_user = D('user');
       $m_family = D('family');
       $m_videoProp = D('videoProp');

       //获取登录数据
       $session_data = array(
            "user_id" => session("user_id"),
            "user_type" => session("user_type"),
            "user_name" => session("user_name")
        );

       //获取搜索条件
       $search_data = $_GET;
       $search_name = isset($search_data['search_name']) ? $search_data['search_name'] : '';
       $status_date = isset($search_data['st']) ? $search_data['st'] : '';
       $status_time = strtotime($status_date);
       $end_date = isset($search_data['et']) ? $search_data['et'] : '';
       $end_time = strtotime($end_date);
       $end_time = strtotime($end_date) + 86400 -1;
       $type = $_GET['type'];

       $status_date = date("Y-m-d", $status_time);
       $end_date = date("Y-m-d", $end_time);


       //获取当前月的第一天和最后一天时间
       if($search_data['search_name'] == '' && $search_data['st'] == '' && $search_data['et'] == ''){
            $status_date_search = date('Y-m-01',time());

            $status_time = strtotime(date('Y-m-01',time()));
            $end_time = strtotime(date('Y-m-d',strtotime("$status_date_search + 1 month -1 day"))) + 86400 -1;
       }

       if($type == 1){ //家族id
          $where = array(
              "status" => 1,
              "user_id" => $search_data["search_name"]
          );
       } elseif($type == 2){ //家族长昵称
          $where_user = array(
              "nick_name" => $search_data["search_name"]
          );
          $user_data = $m_user->where($where_user)->find();  //获取家族长数据
          $user_id = $user_data['id'];
          $where = array(
              "status" => 1,
              "user_id" => $user_id
          );
       } elseif ($type == 3) { //开始时间+结束时间
          $where = array(
              "status" => 1,
          );

          $where_prop_user['create_time'] = array(
              'between', "$status_time,$end_time"
          );
       } elseif ($type == 4) { //开始时间+结束时间+家族长昵称
          $where_user = array(
              "nick_name" => $search_data["search_name"]
          );
          $user_data = $m_user->where($where_user)->find();   //获取家族长数据
          $user_id = $user_data['id'];
          $where = array(
              "status" => 1,
              "user_id" => $user_id
          );
          $where_prop_user['create_time'] = array(
            'between',"$status_time,$end_time"
          );
       } elseif ($type == 5) {  //开始时间+结束时间+家族长id
          $where = array(
              "status" => 1,
              "user_id" => $search_data['search_name']
          );
          $where_prop_user['create_time'] = array(
            'between',"$status_time,$end_time"
          );
       } else {
            $where = array(
                "status" => 1
            );

            //获取当前月1号时间
            $status_date = date("Y-m-01",strtotime(date("Y-m-d")));
            //获取当前月最后一天时间
            $end_date = date('Y-m-d', strtotime("$status_date +1 month -1 day"));
       }
     		

       	$count = $m_family->where($where)->field('id')->count();// 查询满足要求的总记录数
        $p = getpage($count,20);  

        //根据登录用户类型显示家族数据
        if($session_data['user_type'] == 1){
            $list = $m_family->order('id desc')->limit($p->firstRow, $p->listRows)->where($where)->field('id,name,user_id')->select(); //获取所有家族数据
        } else {
            $where_search = array(
                "status" => 1,
                "user_id" => $session_data["user_id"]
            );

            $list = $m_family->order('id desc')->limit($p->firstRow, $p->listRows)->where($where_search)->field('id,name,user_id')->select(); //获取一个家族数据
            // print_r($where_search);exit;
        }


        // 获取搜索时间的年月数据
        $search_years_month = date("Y-m",$status_time);
        $search_years_month = str_replace("-","",$search_years_month); //去掉时间格式的横线
        // 获取当前时间的年月数据
        $now_years_month = date("Y-m",time());
        $now_years_month = str_replace("-","",$now_years_month); //去掉时间格式的横线



        foreach ($list as $key => $val) {

              $family_id = $val['id'];
              if ($status_time && $end_time && $search_years_month >= 201706) {
                  // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->sum('total_ticket');
                  //设置查询表
                  $table_name = "fanwe_video_prop_". $search_years_month;

              } else {
                  //设置查询表
                  $table_name = "fanwe_video_prop_". $now_years_month;
 
              }
               //获取对应家族下的所有主播的魔力总值
              $sql = "select family_id, to_user_id ,SUM(total_ticket) as total_ticket
                      from $table_name
                      where create_time >= $status_time AND create_time < $end_time AND family_id = $family_id
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


              // $ticket_all_num_no = 0;
              // $ticket_all_num = 0;
              // $ticket_all = 0;
              // $where['family_id'] = $val['id'];
              // // $family_user_data = $m_user->where($where)->relation(true)->field('id,nick_name,family_id,mobile')->select(); //获取一个家族下的所有主播数据
              // $family_user_data = $m_user->where($where)->field('id,nick_name,family_id,mobile')->select(); //获取一个家族下的所有主播数据

              // foreach ($family_user_data as $key2 => $value) { //获取一个主播的所有礼物的数据
              //   $ticket_num_yes = 0;
              //   $ticket_num_no = 0;
              //   $total_ticket = 0;
              //   $where_prop_user['to_user_id'] = $value['id'];

              //   if ($status_time && $end_time && $search_years_month >= 201706) {
              //       $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->sum('total_ticket');
              //   } else {
              //       $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->sum('total_ticket');
              //   }
                
              //   if($video_prop_user_data >= 20000 ){ //判断主播魔力值是否大于20000
              //     $ticket_num = $video_prop_user_data; //一个主播可结算魔力值
              //   } else {
              //       $ticket_num_no = $video_prop_user_data; //一个主播未结算魔力值
              //   } 

              //   $ticket_all_num += $ticket_num;
              //   $ticket_all_num_no += $ticket_num_no;
              // }


              $list[$key]['total_ticket'] = $ticket_all_num;
              $list[$key]['total_ticket_no'] = $ticket_all_num_no;
              $list[$key]['earnings'] = $ticket_all_num * 0.329/100;

              //获取家族长信息
              $where_family_user['id'] = $val['user_id'];
              $family_family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

              // $list[$key]['family_data'] = $family_user_data;
              $list[$key]['user_data'] = $family_family_user_data;
        }

       

       session('list', $list);  //存储查询条件

       $this->assign('select', $list); // 赋值数据集  
       $this->assign('page', $p->show()); // 赋值分页输出  
       $this->assign('status_time', $status_time);
       $this->assign('end_time', $end_time);

       $this->assign('status_date', $status_date);
       $this->assign('end_date', $end_date);

       $this->assign('user_type',$session_data['user_type']); //用户类型
       $this->assign("family_data", $list);
       $this->display();
    }


    //成员直播明细
    public function user_data_list(){
      set_time_limit(0);
      $this->if_login();
    	$family_id = $_GET['id'];
    	$m_user = D('user');
      $m_family = D('family');
      $m_videoProp = D('videoProp');
     
     //获取搜索条件
       $search_data = $_GET;
       $search_name = isset($search_data['search_name']) ? $search_data['search_name'] : '';
       $status_date = isset($search_data['st']) ? $search_data['st'] : '';
       $status_time = strtotime($status_date);
       $end_date = isset($search_data['et']) ? $search_data['et'] : '';
       $end_time = strtotime($end_date) + 86400;
       $type = $_GET['type'];

       if($search_data['search_name'] == '' && $search_data['st'] == '' && $search_data['et'] == ''){
            $status_time = $search_data['status_time'];
            $end_time = $search_data['end_time'];
       }


        // 获取搜索时间的年月数据
        $search_years_month = date("Y-m",$status_time);
        $search_years_month = str_replace("-","",$search_years_month); //去掉时间格式的横线
        // 获取当前时间的年月数据
        $now_years_month = date("Y-m",time());
        $now_years_month = str_replace("-","",$now_years_month); //去掉时间格式的横线


        if($type == 1){ //主播id
          $where = array(
            'id' => $search_name,
            'family_id' => $family_id
          );

          $count = $m_user->where($where)->count();// 查询满足要求的总记录数
          $p = getpage($count,10);

          $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个主播的用户数据

          //获取家族信息
          $family_id = $list[0]['family_id'];  //家族id
          $where_family = array(
            "id" => $family_id
          );
          $family_data = $m_family->where($where_family)->find();

          //获取家族长信息
          $user_id = $family_data['user_id']; //家族长id
          $where_family_user = array(
            'id' => $user_id
          );
          $family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

          foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
            $total_ticket = 0;
            $where_prop_user['to_user_id'] = $value['id'];
            $where_prop_user['status'] = 0;

            if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
                
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            }

            // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();

            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
              $ticket_all = $vo['total_ticket'];
              $total_ticket += $ticket_all;

            }

            $valid_day_num = $this->valid_day_num($value['id']);
            $list[$key2]['total_ticket'] = $total_ticket;
            $list[$key2]['family'] = $family_data;
            $list[$key2]['family_user'] = $family_user_data;
            $list[$key2]['valid_day_num'] = $valid_day_num;
          }

      } elseif($type == 2){ //主播昵称
          $where = array(
            'nick_name' => $search_name,
            'family_id' => $family_id
          );

          $count = $m_user->where($where)->count();// 查询满足要求的总记录数
          $p = getpage($count,10);

          $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个主播的用户数据

          //获取家族信息
          $family_id = $list[0]['family_id'];  //家族id
          $where_family = array(
            "id" => $family_id
          );
          $family_data = $m_family->where($where_family)->find();

          //获取家族长信息
          $user_id = $family_data['user_id']; //家族长id
          $where_family_user = array(
            'id' => $user_id
          );
          $family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

          foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
            $total_ticket = 0;
            $where_prop_user['to_user_id'] = $value['id'];
            $where_prop_user['status'] = 0;

            if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            }
            // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();

            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
              $ticket_all = $vo['total_ticket'];
              $total_ticket += $ticket_all;

            }

            $valid_day_num = $this->valid_day_num($value['id']);
            $list[$key2]['total_ticket'] = $total_ticket;
            $list[$key2]['family'] = $family_data;
            $list[$key2]['family_user'] = $family_user_data;
            $list[$key2]['valid_day_num'] = $valid_day_num;
          }
      } elseif($type == 3){ //开始时间+结束时间
          $where_family = array(
            'id' => $family_id,
          );

          //家族数据
          $family_data = $m_family->where($where_family)->find();

          //获取家族长信息
          $where_family_user = array(
            'id' => $family_data['user_id']
          );

          
          $family_family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

          // print_r($family_family_user_data);exit;
          $where = array(
            'family_id' => $family_id
          );

          $count = $m_user->where($where)->count();// 查询满足要求的总记录数
          $p = getpage($count,10);

          $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个家族所有直播的用户数据

          foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
            $total_ticket = 0;
            $where_prop_user['to_user_id'] = $value['id'];
            $where_prop_user['create_time'] = array(
              'between',"$status_time,$end_time"
            );
            $where_prop_user['status'] = 0;

            if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            }

            // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            
            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
                  $ticket_all = $vo['total_ticket'];
                  $total_ticket += $ticket_all;
            }

            $valid_day_num = $this->valid_day_num($value['id'], $status_time, $end_time);
            $list[$key2]['total_ticket'] = $total_ticket;
            $list[$key2]['family'] = $family_data;
            $list[$key2]['family_user'] = $family_family_user_data;
            $list[$key2]['valid_day_num'] = $valid_day_num;

          }


      } elseif($type == 4){ //开始时间+结束时间+主播昵称
          $where = array(
            'nick_name' => $search_name,
            'family_id' => $family_id
          );

          $count = $m_user->where($where)->count();// 查询满足要求的总记录数
          $p = getpage($count,10);

          $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个主播的用户数据

          //获取家族信息
          $family_id = $list[0]['family_id'];  //家族id
          $where_family = array(
            "id" => $family_id
          );
          $family_data = $m_family->where($where_family)->find();

          //获取家族长信息
          $user_id = $family_data['user_id']; //家族长id
          $where_family_user = array(
            'id' => $user_id
          );
          $family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

          foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
            $total_ticket = 0;
            $where_prop_user['to_user_id'] = $value['id'];
            $where_prop_user['create_time'] = array(
              'between',"$status_time,$end_time"
            );
            $where_prop_user['status'] = 0;

            if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            }

            // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();

            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
                $ticket_all = $vo['total_ticket'];
                $total_ticket += $ticket_all;
            }

            $valid_day_num = $this->valid_day_num($value['id'], $status_time, $end_time);
            $list[$key2]['total_ticket'] = $total_ticket;
            $list[$key2]['family'] = $family_data;
            $list[$key2]['family_user'] = $family_user_data;
            $list[$key2]['valid_day_num'] = $valid_day_num;
          }
      } elseif($type == 5){ //开始时间+结束时间+主播id
          $where = array(
            'id' => $search_name,
            'family_id' => $family_id
          );

          $count = $m_user->where($where)->count();// 查询满足要求的总记录数
          $p = getpage($count,10);

          $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个主播的用户数据

          //获取家族信息
          $family_id = $list[0]['family_id'];  //家族id
          $where_family = array(
            "id" => $family_id
          );
          $family_data = $m_family->where($where_family)->find();

          //获取家族长信息
          $user_id = $family_data['user_id']; //家族长id
          $where_family_user = array(
            'id' => $user_id
          );
          $family_user_data = $m_user->where($where_family_user)->field('id,nick_name,mobile')->find();

          foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
            $total_ticket = 0;
            $where_prop_user['to_user_id'] = $value['id'];
            $where_prop_user['create_time'] = array(
              'between',"$status_time,$end_time"
            );
            $where_prop_user['status'] = 0;

            if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
            }

            // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();

            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
                $ticket_all = $vo['total_ticket'];
                $total_ticket += $ticket_all;
            }

             //获取有效天数
            $valid_day_num = $this->valid_day_num($value['id'], $status_time, $end_time);
            $list[$key2]['total_ticket'] = $total_ticket;
            $list[$key2]['family'] = $family_data;
            $list[$key2]['family_user'] = $family_user_data;
            $list[$key2]['valid_day_num'] = $valid_day_num;

          }
      } else {
        $where_family = array(
          'id' => $family_id
        );

        //家族数据
        $family_data = $m_family->where($where_family)->find();

        //获取家族长信息
        $where_family_user = array(
          'id' => $family_data['user_id']
        );
        $family_family_user_data = $m_user->where($where_family_user)->find();

        $where = array(
          'family_id' => $family_id
        );

        $count = $m_user->where($where)->count();// 查询满足要求的总记录数
        $p = getpage($count,10);

        $list = $m_user->where($where)->limit($p->firstRow, $p->listRows)->relation(true)->field('id,nick_name,mobile')->select(); //获取一个家族所有直播的用户数据

        foreach ($list as $key2 => $value) { //获取一个主播的所有礼物的数据
          $total_ticket = 0;
          $where_prop_user['to_user_id'] = $value['id'];
          $where_prop_user['status'] = 0;

          if ($status_time && $end_time && $search_years_month >= 201706) {
              $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
          } else {
              $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();
          }

          // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_201707')->where($where_prop_user)->field('id,num,total_ticket,to_user_id')->select();

          foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值

            $ticket_all = $vo['total_ticket'];
            $total_ticket += $ticket_all;

          }

          //获取有效天数
          $valid_day_num = $this->valid_day_num($value['id']);
          $list[$key2]['total_ticket'] = $total_ticket;
          $list[$key2]['family'] = $family_data;
          $list[$key2]['family_user'] = $family_family_user_data;
          $list[$key2]['valid_day_num'] = $valid_day_num;
        }
      }

      $status_date = date("Y-m-d", $status_time);
      $end_date = date("Y-m-d", $end_time);

      // print_r($list);exit;
      session('list', $list);  //存储查询条件
      $this->assign('page', $p->show()); // 赋值分页输出 
      $this->assign('user_data',$list); //主播主播明细
      $this->assign('family_id', $family_id); //家族id
      $this->assign('status_time', $status_time);
      $this->assign('end_time', $end_time);

      $this->assign('status_date', $status_date);
      $this->assign('end_date', $end_date);

      $this->display();
    }




    //成员本期直播记录
    public function user_record(){
      set_time_limit(0);
      $this->if_login();
      $m_user = D('user');
      $m_family = D('family');
      $m_videoProp = D('videoProp');
      $m_videoHistory = D('VideoHistory');
      $user_id = $_GET['id']; //用户id
      //获取搜索条件
      $status_date = isset($_GET['st']) ? $_GET['st'] : '';
      $status_time = strtotime($status_date);
      $end_date = isset($_GET['et']) ? $_GET['et'] : '';
      $end_time = strtotime($end_date) + 86400-1;



      if($_GET['search_name'] == '' && $_GET['st'] == '' && $_GET['et'] == ''){
            $status_time = $_GET['status_time'];
            $end_time = $_GET['end_time'];
      }

      // 获取搜索时间的年月数据
      $search_years_month = date("Y-m",$status_time);
      $search_years_month = str_replace("-","",$search_years_month); //去掉时间格式的横线
      // 获取当前时间的年月数据
      $now_years_month = date("Y-m",time());
      $now_years_month = str_replace("-","",$now_years_month); //去掉时间格式的横线

       if($status_time && $end_time){
            $where_prop_user = array(
                "create_time" => array(
                    'between',"$status_time,$end_time"
                )
            );
       }

      //获取用户信息
      $where['id'] = $user_id;
      // print_r($user_id);exit;
      $user_data = $m_user->where($where)->relation(true)->field('id,nick_name,mobile,family_id')->find();
      //获取家族信息
      $family_id = $user_data['family_id'];
      $where_family['id'] = $family_id;
      $family_data = $m_family->where($where_family)->find();

      $total_ticket = 0;
      $where_prop_user['to_user_id'] = $user_id;

      if ($status_time && $end_time && $search_years_month >= 201706) {
          // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->select();
          $total_ticket =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_prop_user)->sum('total_ticket');

      } else {
          // $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->select();
          $total_ticket =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_prop_user)->sum('total_ticket');
      }


      $video_prop_user_data = $this->every_data($user_id,$status_time,$end_time);

      foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值

        $video_prop_user_data[$k]['user_data'] = $user_data;
        $video_prop_user_data[$k]['family_data'] = $family_data['name'];

      }

      $return_data = $this->live_recording($user_id,$status_time,$end_time);
      // print_r($return_data);exit;

      $num = $total_ticket/31;
      $magic_add_mean = sprintf("%.2f", $num); //保留两位小数

      $user_data['valid_day'] = $return_data['valid_day']; //有效天数
      $user_data['user_num_max'] = $return_data['user_num_max']; //最高在线人数
      $user_data['fans_add_mean'] = $return_data['fans_add_mean']; //平均粉丝增量
      $user_data['time_long_all'] = $return_data['time_long_all']; //本期直播时长
      $user_data['magic_add_mean'] = $magic_add_mean; //平均日增魔力
      $user_data['total_ticket'] = $total_ticket; 
      $user_data['family'] = $family_data;

      session('list', $video_prop_user_data);  //存储查询条件
      // $this->assign('page', $p->show()); // 赋值分页输出 
      

      $status_date = date("Y-m-d", $status_time);
      $end_date = date("Y-m-d", $end_time);

      $this->assign('user_data', $user_data);
      $this->assign('user_day_data', $video_prop_user_data);

      $this->assign('status_date', $status_date);
      $this->assign('end_date', $end_date);
      $this->display();
    }



    //结算历史表
    public function account_list(){
        $this->if_login();
        //获取用户类型
        $user_type = session("user_type");

        //获取搜索条件
        $search_data = $_GET;
        $search_name = isset($search_data['search_name']) ? $search_data['search_name'] : '';
        $status_date = isset($search_data['st']) ? $search_data['st'] : '';

        $status_time = strtotime($status_date);
        $type = $_GET['type'];

        

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
              $expect_date = date('Y-m-16', strtotime(date("Y-m-d")));

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
              $expect_date = date('Y-m-01', strtotime(date("Y-m-d")));
        } 

        $m_familySettlementHistory = D('familySettlementHistory');

        if($type == 1){
            $where = array(
                "user_id" => $search_name,
                // "expect_date" => $expect_date
            );
        } elseif($type == 2){
            $where = array(
                "nick_name" => $search_name,
                // "expect_date" => $expect_date
            );
        } elseif($type == 3){
            $where = array(
                "expect_date" => $status_date
            );
        } elseif($type == 4){
            $where = array(
                "nick_name" => $search_name,
                "expect_date" => $status_date
            );
        } elseif($type == 5){
            $where = array(
                "user_id" => $search_name,
                "expect_date" => $status_date
            );
        } else{
            $where = array(
                "expect_date" => $expect_date
            );
        }

        $count = $m_familySettlementHistory->where($where)->count();
        $p = getpage($count,20);
        
        if($user_type == 2){
            if($status_date){
                $where = array(
                    "user_id" => session("user_id"),
                    "expect_date" => $status_date
                );
            } else {
                $where = array(
                    "user_id" => session("user_id")
                );
            }
            
            $count = $m_familySettlementHistory->where($where)->count();
            $p = getpage($count,20);

            $list = $m_familySettlementHistory->where($where)->limit($p->firstRow, $p->listRows)->order('id desc')->select();
        } else {
            $list = $m_familySettlementHistory->where($where)->limit($p->firstRow, $p->listRows)->order('id desc')->select();
        }

        //设置搜索时间
        if($status_date){
            $where_search = $status_date;
        } else {
            $where_search = $expect_date;
        }

        session("where",$where);

        $this->assign("settlementHistory_data",$list);
        $this->assign("page",$p->show());
        $this->assign("user_type",$user_type);
        $this->assign("where_search", $where_search);

        $this->display();
    }


    //统计有效天数
    public function valid_day_num($user_id = '',$status_time = '', $end_time = ''){
        $m_videoProp = D('videoProp');
        $m_videoHistory = D('videoHistory');
        $m_user = D('user');

        // $user_id = '61244790';
        // $time_type = 2;

        //获取当前月日期天数
        $now_month_day = date("d");

        //获取上月的第一天凌晨时间
        $last_month_status_date = date('Y-m-01', strtotime('-1 month'));
        $last_month_status_time = strtotime($last_month_status_date);

        //获取当前月的第一天凌晨时间
        $now_month_status_date = date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_status_time = strtotime($now_month_status_date);

        //获取上月十五号11::59:59时间
        $last_month_centre_time = $last_month_status_time + 86400*15 -1;

        //获取当前月十五号11::59:59时间
        $now_month_centre_time = $now_month_status_time + 86400*15 -1;

        //获取上月十六号凌晨时间
        $last_month_sixteen_time = $last_month_status_time + 86400*15;

        //获取当前月十六号凌晨时间
        $now_month_sixteen_time = $now_month_status_time + 86400*15;

        //获取上月的最后一天11:59:59时间
        $last_month_end_date = date('Y-m-t', strtotime('-1 month'));
        $last_month_end_time = strtotime($last_month_end_date) + 86400 -1;

        //获取当前月的最后一天11:59:59时间
        $now_m_date =  date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_end_date = date('Y-m-d', strtotime("$now_m_date +1 month -1 day"));
        $now_month_end_time = strtotime($now_month_end_date) + 86400 -1;

        if($status_time && $end_time){
            $days = ($end_time-$status_time)/86400;
            $start_day = $status_time;
            $arr = array();
            for ($i = 0;$i< $days;$i++) {
                $arr[] = array(
                    "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                    "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
                );
            }
        } else {
            $days= ($now_month_end_time-$now_month_status_time+86400)/86400;
            $start_day = $now_month_status_time;
            $arr = array();
            for ($i = 0;$i< $days;$i++) {
              $arr[] = array(
                  "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                  "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
              );

            }
        }
        

        $time_long = 0; //每天直播时长
        $valid_day = 0; //有效天数

        foreach ($arr as $key => $value) {  //循环查询每天的直播时长
            $time_long = 0; //每天直播时长
            $status_time_type1 = $value['status_time'];
            $end_time_type1 = $value['end_time'];

            
            $where_time = array(
                "user_id" => $user_id,
                "create_time" => array(
                    'between', "$status_time_type1,$end_time_type1"
                )
            );
            $video_history_date = $m_videoHistory->where($where_time)->select();  //查出当前时间天的所有直播记录
            // print_r($video_history_date);echo "1";
            if($video_history_date[0]){
                foreach ($video_history_date as $k => $val) {
                    $time_long_size = ($val['end_time'] - $val['create_time']) / 3600;

                    $time_long += $time_long_size; //计算当前时间天的直播时长
                }
            } 

            if($time_long >= 3){
                $valid_day +=1;  //直播有效天数+1
            }
        }
        

        // print_r($valid_day);exit;
        return $valid_day;

        
        


}


    
    //成员本期直播记录
    public function live_recording($user_id = '',$status_time = '',$end_time = ''){
        $this->if_login();
        $m_videoProp = D('videoProp');
        $m_videoHistory = D('videoHistory');
        $m_user = D('user');

        // $user_id = '61247324';
        // $time_type = 2;

        //获取当前月日期天数
        $now_month_day = date("d");

        //获取上月的第一天凌晨时间
        $last_month_status_date = date('Y-m-01', strtotime('-1 month'));
        $last_month_status_time = strtotime($last_month_status_date);

        //获取当前月的第一天凌晨时间
        $now_month_status_date = date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_status_time = strtotime($now_month_status_date);

        //获取上月十五号11::59:59时间
        $last_month_centre_time = $last_month_status_time + 86400*15 -1;

        //获取当前月十五号11::59:59时间
        $now_month_centre_time = $now_month_status_time + 86400*15 -1;

        //获取上月十六号凌晨时间
        $last_month_sixteen_time = $last_month_status_time + 86400*15;

        //获取当前月十六号凌晨时间
        $now_month_sixteen_time = $now_month_status_time + 86400*15;

        //获取上月的最后一天11:59:59时间
        $last_month_end_date = date('Y-m-t', strtotime('-1 month'));
        $last_month_end_time = strtotime($last_month_end_date) + 86400 -1;

        //获取当前月的最后一天11:59:59时间
        $now_m_date =  date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_end_date = date('Y-m-d', strtotime("$now_m_date +1 month -1 day"));
        $now_month_end_time = strtotime($now_month_end_date) + 86400 -1;

        if($status_time && $end_time){
            $days = ($end_time-$status_time)/86400;
            $start_day = $status_time;
            $arr = array();
            for ($i = 0;$i< $days;$i++) {
                $arr[] = array(
                    "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                    "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
                );
            }
        } else {

              $days=($now_month_end_time-$now_month_status_time+86400)/86400;
              $start_day = $now_month_status_time;
              $arr = array();
              for ($i = 0;$i< $days;$i++) {
                $arr[] = array(
                    "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                    "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
                );

              }
        }
        
       

        $time_long_all = 0; //本期直播时长
        $time_long = 0; //每天直播时长
        $valid_day = 0; //有效天数
        $user_num_max = 0; //本期最高在线人数
        $magic_add_mean = 0;  //平均日增魔力
        $fans_add_mean = 0;  //平均日增粉丝
        $fans_add_num = 0; //本期粉丝增加量



        foreach ($arr as $key => $value) {  //循环查询每天的直播时长
            $time_long = 0; //每天直播时长
            $status_time_type1 = $value['status_time'];
            $end_time_type1 = $value['end_time'];

            
            $where_time = array(
                "user_id" => $user_id,
                "create_time" => array(
                    'between', "$status_time_type1,$end_time_type1"
                )
            );
            $video_history_date = $m_videoHistory->where($where_time)->select();  //查出当前时间天的所有直播记录
            // print_r($video_history_date);echo "1";
            if($video_history_date[0]){
                foreach ($video_history_date as $k => $val) {
                    $time_long_size = ($val['end_time'] - $val['create_time']) / 3600;

                    $time_long += $time_long_size; //计算当前时间天的直播时长
                    $max_watch_number = $val['max_watch_number'];  //本条直播记录最大观看人数

                    if($max_watch_number > $user_num_max){
                        $user_num_max = $max_watch_number; //本期最大观看人数
                    }

                    $fans_count = $val['fans_count']; //本条直播粉丝增量
                    $fans_add_num += $fans_count;  //本期粉丝增量
                }
            } 

            if($time_long >= 3){
                $valid_day +=1;  //直播有效天数+1
            }

            $time_long_num += $time_long; //本期直播时长
        }
        $time_long_all = sprintf("%.2f", $time_long_num); //本期直播时长

        

        $num = $fans_add_num/31;  //平均日增粉丝
        $fans_add_mean = sprintf("%.2f", $num);

        $return_data = array(
            "valid_day" => $valid_day,
            "user_num_max" => $user_num_max,
            "fans_add_mean" => $fans_add_mean,
            "time_long_all" => $time_long_all
        );

        // print_r($return_data);exit;

        return $return_data;

    }


    //本期每日数据
    public function every_data($user_id = '',$status_time='',$end_time=''){
        $this->if_login();
        $m_videoProp = D('videoProp');
        $m_videoHistory = D('videoHistory');
        $m_user = D('user');

        // 获取搜索时间的年月数据
        $search_years_month = date("Y-m",$status_time);
        $search_years_month = str_replace("-","",$search_years_month); //去掉时间格式的横线
        // 获取当前时间的年月数据
        $now_years_month = date("Y-m",time());
        $now_years_month = str_replace("-","",$now_years_month); //去掉时间格式的横线

        // $user_id = '61244790';
        // $time_type = 2;

        //获取当前月日期天数
        $now_month_day = date("d");

        //获取上月的第一天凌晨时间
        $last_month_status_date = date('Y-m-01', strtotime('-1 month'));
        $last_month_status_time = strtotime($last_month_status_date);

        //获取当前月的第一天凌晨时间
        $now_month_status_date = date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_status_time = strtotime($now_month_status_date);

        //获取上月十五号11::59:59时间
        $last_month_centre_time = $last_month_status_time + 86400*15 -1;

        //获取当前月十五号11::59:59时间
        $now_month_centre_time = $now_month_status_time + 86400*15 -1;

        //获取上月十六号凌晨时间
        $last_month_sixteen_time = $last_month_status_time + 86400*15;

        //获取当前月十六号凌晨时间
        $now_month_sixteen_time = $now_month_status_time + 86400*15;

        //获取上月的最后一天11:59:59时间
        $last_month_end_date = date('Y-m-t', strtotime('-1 month'));
        $last_month_end_time = strtotime($last_month_end_date) + 86400 -1;

        //获取当前月的最后一天11:59:59时间
        $now_m_date =  date('Y-m-01', strtotime(date("Y-m-d")));
        $now_month_end_date = date('Y-m-d', strtotime("$now_m_date +1 month -1 day"));
        $now_month_end_time = strtotime($now_month_end_date) + 86400 -1;


        if($status_time && $end_time){
            $days = ($end_time-$status_time)/86400;
            $start_day = $status_time;
            $arr = array();
            for ($i = 0;$i< $days;$i++) {
                $arr[] = array(
                    "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                    "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
                );
            }
        } else {
            $now_date_time = strtotime(date('Y-m-d H:i:s',time()));  //当前时间时间戳
            $days=($now_date_time-$now_month_status_time)/86400;
            $start_day = $now_month_status_time;
            $arr = array();
            for ($i = 0;$i< $days;$i++) {
                $arr[] = array(
                    "status_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60)),
                    "end_time" => strtotime(date('Y-m-d H:i:s',$start_day + $i*24*60*60 + 86400-1))
                );
            }
        }
        
        

        $time_long_all = 0; //本期直播时长
        $time_long = 0; //每天直播时长
        $valid_day = 0; //有效天数
        $user_num_max = 0; //本期最高在线人数
        $magic_add_mean = 0;  //平均日增魔力
        $fans_add_mean = 0;  //平均日增粉丝
        $fans_add_num = 0; //本期粉丝增加量


        $return_data_list = array();

        foreach ($arr as $key => $value) {  //循环查询每天的直播记录
            $status_time_type1 = $value['status_time'];
            $end_time_type1 = $value['end_time'];

            $now_date = date('Y-m-d',$status_time_type1);
            $where_time = array(
                "user_id" => $user_id,
                "create_time" => array(
                    'between', "$status_time_type1,$end_time_type1"
                )
            );
            $video_history_date = $m_videoHistory->where($where_time)->select();  //查出当前时间天的所有直播记录
            // print_r($video_history_date);echo "1";
            $max_watch_number = 0;
            $time_long = 0;
            $fans_add_num = 0;
            if($video_history_date[0]){
                foreach ($video_history_date as $k => $val) {
                    $time_long_size = ($val['end_time'] - $val['create_time']) / 3600;

                    $time_long += $time_long_size; //计算当前时间天的直播时长
                    $max_watch_number = $val['max_watch_number'];  //本条直播记录最大观看人数

                    if($max_watch_number > $user_num_max){
                        $user_num_max = $max_watch_number; //本期最大观看人数
                    }

                    $fans_count = $val['fans_count']; //本条直播粉丝增量
                    $fans_add_num += $fans_count;  //日粉丝增量
                }

            } 

            if($time_long >= 3){
                $valid_day_status = "是";  //是否是有效天
            } else {
                $valid_day_status = "否";  //是否是有效天
            }

            $time_long = sprintf("%.2f", $time_long);


            //获取日增魔力值
            $where_video_prop = array(
                "to_user_id" => $user_id,
                "create_time" => array(
                    'between', "$status_time_type1,$end_time_type1"
                )
            );
            $total_ticket = 0;

             if ($status_time && $end_time && $search_years_month >= 201706) {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_video_prop)->field('id,num,total_ticket,to_user_id')->select();
                // $total_ticket =$m_videoProp->table('fanwe_video_prop_'.$search_years_month)->where($where_video_prop)->sum('total_ticket');
            } else {
                $video_prop_user_data =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_video_prop)->field('id,num,total_ticket,to_user_id')->select();
                // $total_ticket =$m_videoProp->table('fanwe_video_prop_'.$now_years_month)->where($where_video_prop)->sum('total_ticket');
            }

            foreach ($video_prop_user_data as $k => $vo) { //获取每个礼物的对应的魔力值
              $ticket_all = $vo['total_ticket'];
              $total_ticket += $ticket_all;
            }

            $return_data_list[] = array(
                "now_date" => $now_date, //当天日期
                "time_long" => $time_long,  //日直播时长
                "total_ticket" => $total_ticket, //日魔力值增量
                "fans_add_num" => $fans_add_num, //日增粉丝量
                "max_watch_number" => $max_watch_number, //日最高在线人数
                "valid_day_status" => $valid_day_status, //是否是有效天
            );


            
        }


        return $return_data_list;
    }






    //家族长列表导出
    public function payExport(){
        $this->if_login();
        // $name =trim(date("Y/m/d",time()));
        // $name = date("Y/m/d",time());
        //获取要导出的数据
        
        $data = session('list');

          // vendor('PHPExcel.PHPExcel');
          import("Org.Util.PHPExcel");

          error_reporting(E_ALL);
          date_default_timezone_set('Europe/London');
          $a=count($data);
          $objPHPExcel = new \PHPExcel();
          /*以下是一些设置 ，什么作者  标题啊之类的*/
          $objPHPExcel->getProperties()->setCreator("firefly")
              ->setLastModifiedBy("firefly")
              ->setTitle("数据EXCEL导出")
              ->setSubject("数据EXCEL导出")
              ->setDescription("数据查看")
              ->setKeywords("excel")
              ->setCategory("result file");
          /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
          $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
          ->setCellValue('A1',"家族长管理表")
              ->setCellValue('A2',"家族长id")
              ->setCellValue('B2', "家族长昵称")
              ->setCellValue('C2', "家族名称")
              ->setCellValue('D2', "可结算魔力值")
              ->setCellValue('E2', "未结算魔力值")
              ->setCellValue('F2', "本期收益/元")
              ->setCellValue('G2', "提成系数")
              ;
          foreach($data as $k => $v){
            // $create_time = date("Y-m-d H:i",$v["create_time"]); 
              $num=$k+1;
              $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
              ->setCellValue('A'.($num+2),$v['user_id'])
                  ->setCellValue('B'.($num+2), $v['name'])
                  ->setCellValue('C'.($num+2), $v['user_data']['nick_name'])
                  ->setCellValue('D'.($num+2), $v['total_ticket'])
                  ->setCellValue('E'.($num+2), $v['total_ticket_no'])
                  ->setCellValue('F'.($num+2), $v['earnings'])
                  ->setCellValue('G'.($num+2), 0.329)

                  ;
  //设置格式
              /*     $objPHPExcel->getActiveSheet()->getStyle('A'.($num+1))->getNumberFormat()
                       ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH);*/

          }
  //设置单元格宽度
          $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

  //合并单元格
          $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
  //设置字体样式
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(10);
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
  //设置居中
          $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A3:E3'.($a+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->setTitle('User');
          $objPHPExcel->setActiveSheetIndex(0);
  //         
         
          $name=date('Y-m-d');//设置文件名
          // $title="家族长管理表";
          ob_end_clean();
          ob_start();
          header('Content-Type: applicationnd.ms-excel');
          header('Content-Disposition: attachment;filename="家族直播管理表'.$name.'.xlsx"');
          header('Cache-Control: max-age=0');
          $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit;
    }


    //成员直播明细列表导出
    public function payExport_two(){
        $this->if_login();
        // $name =trim(date("Y/m/d",time()));
        // $name = date("Y/m/d",time());
        //获取要导出的数据
        
        $data = session('list');

          // vendor('PHPExcel.PHPExcel');
          import("Org.Util.PHPExcel");

          error_reporting(E_ALL);
          date_default_timezone_set('Europe/London');
          $a=count($data);
          $objPHPExcel = new \PHPExcel();
          /*以下是一些设置 ，什么作者  标题啊之类的*/
          $objPHPExcel->getProperties()->setCreator("firefly")
              ->setLastModifiedBy("firefly")
              ->setTitle("数据EXCEL导出")
              ->setSubject("数据EXCEL导出")
              ->setDescription("数据查看")
              ->setKeywords("excel")
              ->setCategory("result file");
          /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
          $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
          ->setCellValue('A1',"成员直播明细表")
              ->setCellValue('A2',"用户id")
              ->setCellValue('B2', "昵称")
              ->setCellValue('C2', "家族昵称")
              ->setCellValue('D2', "家族长")
              ->setCellValue('E2', "新增魔力值")
              ->setCellValue('F2', "有效天数")

              ;
          foreach($data as $k => $v){
            // $create_time = date("Y-m-d H:i",$v["create_time"]); 
              $num=$k+1;
              $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
              ->setCellValue('A'.($num+2),$v['id'])
                  ->setCellValue('B'.($num+2), $v['nick_name'])
                  ->setCellValue('C'.($num+2), $v['family']['name'])
                  ->setCellValue('D'.($num+2), $v['family_user']['nick_name'])
                  ->setCellValue('E'.($num+2), $v['total_ticket'])
                  ->setCellValue('F'.($num+2), $v['valid_day_num'])
                  ;
  //设置格式
              /*     $objPHPExcel->getActiveSheet()->getStyle('A'.($num+1))->getNumberFormat()
                       ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH);*/

          }
  //设置单元格宽度
          $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

  //合并单元格
          $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
  //设置字体样式
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(10);
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
  //设置居中
          $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A3:E3'.($a+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->setTitle('User');
          $objPHPExcel->setActiveSheetIndex(0);
  //         
         
          $name=date('Y-m-d');//设置文件名
          // $title="家族长管理表";
          ob_end_clean();
          ob_start();
          header('Content-Type: applicationnd.ms-excel');
          header('Content-Disposition: attachment;filename="成员直播明细表'.$name.'.xlsx"');
          header('Cache-Control: max-age=0');
          $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit;
    }


    //本期直播记录列表导出
    public function payExport_three(){
          $this->if_login();
        // $name =trim(date("Y/m/d",time()));
        // $name = date("Y/m/d",time());
        //获取要导出的数据
        
          $data = session('list');

          // vendor('PHPExcel.PHPExcel');
          import("Org.Util.PHPExcel");

          error_reporting(E_ALL);
          date_default_timezone_set('Europe/London');
          $a=count($data);
          $objPHPExcel = new \PHPExcel();
          /*以下是一些设置 ，什么作者  标题啊之类的*/
          $objPHPExcel->getProperties()->setCreator("firefly")
              ->setLastModifiedBy("firefly")
              ->setTitle("数据EXCEL导出")
              ->setSubject("数据EXCEL导出")
              ->setDescription("数据查看")
              ->setKeywords("excel")
              ->setCategory("result file");
          /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
          $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
          ->setCellValue('A1',"本期直播记录列表")
              ->setCellValue('A2',"日期")
              ->setCellValue('B2', "主播ID")
              ->setCellValue('C2', "昵称")
              ->setCellValue('D2', "手机号")
              ->setCellValue('E2', "家族昵称")
              ->setCellValue('F2', "日直播时长")
              ->setCellValue('G2', "日魔力值增量")
              ->setCellValue('H2', "日粉丝增量")
              ->setCellValue('I2', "日最高在线人数")
              ->setCellValue('J2', "是否为有效天")
              ;
          foreach($data as $k => $v){
            // $create_time = date("Y-m-d H:i",$v["create_time"]); 
              $num=$k+1;
              $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
              ->setCellValue('A'.($num+2),$v['now_date'])
                  ->setCellValue('B'.($num+2), $v['user_data']['id'])
                  ->setCellValue('C'.($num+2), $v['user_data']['nick_name'])
                  ->setCellValue('D'.($num+2), $v['user_data']['mobile'])
                  ->setCellValue('E'.($num+2), $v['family_data'])
                  ->setCellValue('F'.($num+2), $v['time_long'])
                  ->setCellValue('G'.($num+2), $v['total_ticket'])
                  ->setCellValue('H'.($num+2), $v['fans_add_num'])
                  ->setCellValue('I'.($num+2), $v['max_watch_number'])
                  ->setCellValue('J'.($num+2), $v['valid_day_status'])
                  ;
  //设置格式
              /*     $objPHPExcel->getActiveSheet()->getStyle('A'.($num+1))->getNumberFormat()
                       ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH);*/

          }
  //设置单元格宽度
          $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

  //合并单元格
          $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
  //设置字体样式
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(10);
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
  //设置居中
          $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A3:E3'.($a+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->setTitle('User');
          $objPHPExcel->setActiveSheetIndex(0);
  //         
         
          $name=date('Y-m-d');//设置文件名
          // $title="家族长管理表";
          ob_end_clean();
          ob_start();
          header('Content-Type: applicationnd.ms-excel');
          header('Content-Disposition: attachment;filename="本期直播记录列表'.$name.'.xlsx"');
          header('Cache-Control: max-age=0');
          $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit;
    }


    //本期直播记录列表导出
    public function payExport_four(){
          $this->if_login();
        // $name =trim(date("Y/m/d",time()));
        // $name = date("Y/m/d",time());
        //获取要导出的数据
        

        $where = session("where");
        $m_familySettlementHistory = D('familySettlementHistory');
        $data = $m_familySettlementHistory->where($where)->order('id desc')->select();

          // vendor('PHPExcel.PHPExcel');
          import("Org.Util.PHPExcel");

          error_reporting(E_ALL);
          date_default_timezone_set('Europe/London');
          $a=count($data);
          $objPHPExcel = new \PHPExcel();
          /*以下是一些设置 ，什么作者  标题啊之类的*/
          $objPHPExcel->getProperties()->setCreator("firefly")
              ->setLastModifiedBy("firefly")
              ->setTitle("数据EXCEL导出")
              ->setSubject("数据EXCEL导出")
              ->setDescription("数据查看")
              ->setKeywords("excel")
              ->setCategory("result file");
          /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
          $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
          ->setCellValue('A1',"结算记录列表")
              ->setCellValue('A2',"家族长id")
              ->setCellValue('B2', "家族长昵称")
              ->setCellValue('C2', "家族名称")
              ->setCellValue('D2', "本期结算魔力值")
              ->setCellValue('E2', "本期未结算魔力值")
              ->setCellValue('F2', "本期收益/元")
              ->setCellValue('G2', "提成系数")
              ->setCellValue('H2', "期数")
              ->setCellValue('I2', "创建时间")
              ;
          foreach($data as $k => $v){
            $create_time = date("Y-m-d H:i:s",$v["create_time"]); 
              $num=$k+1;
              $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
              ->setCellValue('A'.($num+2),$v['user_id'])
                  ->setCellValue('B'.($num+2), $v['nick_name'])
                  ->setCellValue('C'.($num+2), $v['name'])
                  ->setCellValue('D'.($num+2), $v['total_ticket'])
                  ->setCellValue('E'.($num+2), $v['total_ticket_no'])
                  ->setCellValue('F'.($num+2), $v['earnings'])
                  ->setCellValue('G'.($num+2), $v['coefficient'])
                  ->setCellValue('H'.($num+2), $v['expect_date'])
                  ->setCellValue('I'.($num+2), $create_time)
                  ;
  //设置格式
              /*     $objPHPExcel->getActiveSheet()->getStyle('A'.($num+1))->getNumberFormat()
                       ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH);*/

          }
  //设置单元格宽度
          $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
          $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

  //合并单元格
          $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
  //设置字体样式
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(10);
          $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
  //设置居中
          $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->getStyle('A3:E3'.($a+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $objPHPExcel->getActiveSheet()->setTitle('User');
          $objPHPExcel->setActiveSheetIndex(0);
  //         
         
          $name=date('Y-m-d');//设置文件名
          // $title="家族长管理表";
          ob_end_clean();
          ob_start();
          header('Content-Type: applicationnd.ms-excel');
          header('Content-Disposition: attachment;filename="结算记录表'.$name.'.xlsx"');
          header('Cache-Control: max-age=0');
          $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit;
    }



    /**
     * webuploader 上传文件
     */
    public function ajax_upload(){
        // 根据自己的业务调整上传路径、允许的格式、文件大小
        ajax_upload('__ROOT__/Public/Upload/image');
    }
    /**
     * webuploader 上传demo
     */
    public function webuploader(){
        // 如果是post提交则显示上传的文件 否则显示上传页面
        if(IS_POST){
            p($_POST);die;
        }else{
            $this->display();
        }

   }

}