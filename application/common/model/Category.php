<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23 0023
 * Time: 17:14
 */
namespace  app\common\model;
use think\Model;
use thinK\Db;

class Category extends Model{

    public  function   select($start,$length){
        $data['data']= Db::table("bg_category")
            ->limit($start,$length)
            ->select();
        $data['count']=count($data['data']);
        return $data ;
    }



}