<?php
namespace app\admin\controller;
use think\Db;

class  Link extends Praent
{
       public  function  linkList()
       {
              $linkList= Db::table("bg_link")->paginate(5);
              $this->assign("linkList",$linkList);
              return $this->fetch();
       }




}