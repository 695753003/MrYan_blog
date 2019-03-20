<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/13
 * Time: 13:02
 */
namespace  app\admin\model;

use think\Model;

class Comment extends Model{

    public function getPage($start,$length){

      $data['data']= Db::table("bg_comment")->alias("a")->field("a.*,b.title,c.name")
            ->join("bg_article b","a.aid=b.id")
            ->join("bg_user c","a.uid=c.id")
            ->order("addtime desc")
            ->limit($start,$length)
            ->select();
        $data['count'] = Db::table("bg_comment")->conut();

       return $data;

    }


}