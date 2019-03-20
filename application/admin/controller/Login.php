<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/12
 * Time: 14:58
 */
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin;

class  Login extends  Controller{

/*登陆
 缓存COOKIE
*/
    public  function landing (){
        if(!empty($_POST))
        {
          $uname=$_POST["uname"];
          $password=$_POST["password"];
          $adminarr= Admin::where(["uname"=>$uname,"password"=>md5($password)])->find();

           if(!empty($adminarr))
           {
               $arr=  $adminarr->toArray();
               cookie("admins",$arr);   //存入cookie
               $this->redirect("index/index");
           }
           else
           {
               echo "<script> alert('账号或密码错误')</script>";
               return  $this->fetch();
           }
        }
        else
       {
           return  $this->fetch();
        }

    }

    public function  destruction()
    {
        cookie("admins",null);
        $this->redirect("login/landing");
    }




}