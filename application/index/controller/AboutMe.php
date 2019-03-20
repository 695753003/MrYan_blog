<?php

namespace  app\index\controller;


use think\Db;

class AboutMe extends  Base
{

    //
     public function myIndex()
     {
       $data=  Db::table("bg_about_me")->where(['id'=>1])->find();
       $this->assign("data",$data);
       return $this->fetch();
     }


     public function gossips(){
         $gossiparr=   Db::table("bg_gossip")->order("create_time desc")->select();
         $this->assign("gossiparr",$gossiparr);
         return $this->fetch();
     }





}