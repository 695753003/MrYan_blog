<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/13
 * Time: 13:03
 */
namespace  app\common\model;

use think\Model;
use think\Db;
class Link extends Model{

  public  function   select($start,$length){
     $data['data']= Db::table("bg_link")->alias("a")->field("a.*,b.nick_name")
          ->join("bg_admin b","a.admin_id=b.id","left" )
          ->limit($start,$length)
          ->select();
      $data['count']=count($data['data']);
     return $data ;
  }


}