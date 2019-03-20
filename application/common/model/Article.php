<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/13
 * Time: 13:02
 */
namespace  app\common\model;

use think\Model;
use think\Db;
class Article extends Model{

    public function  getPage($start,$length){
    $data['data']= Db::table("bg_article")->alias("a")->field("a.* ,b.title `c_title`,c.title `l_title` ")->join("bg_category b","a.ca_id=b.id","left")
         ->join("bg_column c","a.co_id=c.id","left")->limit($start,$length)->select();
        $data['count']= Db::table("bg_article")->count();
       return $data;
    }


}