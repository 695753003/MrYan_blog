<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/12
 * Time: 11:55
 */

namespace  app\common\model;

use think\Db;
use think\Model;


class Admin extends Model{


    public function  adminList($start,$length,$search){
        $where="1=1";
        if(!empty($search)){
            $where=" a.name like '%$search%'";
        }
      $data=  Db::table("bg_admin")->alias("a")->field("a.id,a.uname,a.nick_name ,b.name")
            ->join("bg_auth_group b","a.group_id=b.id","left")->where($where)
            ->limit($start,$length)->select();
       $count= Db::table("bg_admin")->where($where)->count();

      return ["data"=>$data,"count"=>$count];
    }


}