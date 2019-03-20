<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/12
 * Time: 11:54
 */

namespace  app\admin\controller;

use think\Controller;
use  app\admin\model;
use  think\Db;
use  think\Request;



class  Index extends Praent {

    public  function  index(){

        return $this->fetch();
    }

    public function welcome(){

        return $this->fetch();
    }
    //无权访问
    public function Permission(){
        return view("/permission");
    }

    public function ajaxPermission(){
        return json(["code"=>-1,"msg"=>"您没权限"]);
    }

}