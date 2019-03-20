<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/17 0017
 * Time: 14:18
 */
namespace  app\common\model;
use think\Db;
use think\Model;

class Column extends Model{


  public function  getPage($start,$length)
  {

     $data['data']= Db::table("bg_column")->limit($start,$length)->select();
      $data['count']= Db::table("bg_column")->count();
     return $data;
  }



}
