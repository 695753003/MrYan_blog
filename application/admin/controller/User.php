<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/15
 * Time: 16:18
 */

namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin;

use think\Db;

class User  extends  Praent{

    /*
     * 管理员列表
     *
     * */
 public  function  listUser(){
     if ($this->request->post() || $this->request->isAjax()) {
         $data = $this->deDataTablesJson();
         $draw = $data['draw'];
         $start = $data["start"];
         $length = $data["length"];
         $search="";
         if(!empty($_POST["search"])){
             $search=trim($_POST["search"]);
         }
        $data= model("Admin")->adminList($start,$length,$search);
         if (!$data)
         {
             return
                 json(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []]);
         } else
         {
             return json(["draw" => $draw, "recordsTotal" =>$data['count'] , "recordsFiltered" =>$data['count'] , "data" => $data['data']]);
         }
     }
     return $this->fetch();

 }
    /**
     *
     * 新增用户页面 And 操作
     *
     *
     */
 public function  addUser(){


     if(!empty($_POST)){
         $_POST['password']=md5($_POST['password']);
        $res= Db::table("bg_admin")->insert($_POST);
        if($res){
            return json(["code"=>1,"msg"=>"添加成功"]);
        }
        else{
            return json(["code"=>-1,"msg"=>"添加失败"]);
        }
     }
     else{
         $data=Db::table("bg_auth_group")->select();
         $this->assign("data",$data);
         return $this->fetch();
     }
 }
    /**
     *
     * @return string|\think\response\Json
     *
     * 执行删除操作
     *
     */
 public  function del(){
     if(!empty($_GET)){
        $res =Db::table("bg_admin")->where(["id"=>$_GET['id']])->delete();
         if($res){
             return json(["code"=>1,"msg"=>"删除成功"]);
         }
         else
        {
             return json(["code"=>-1,"msg"=>"删除失败"]);
         }
     }
 }

    /**
     *
     * @return mixed|string|\think\response\Json
     *
     * 用户编辑页面 And 操作
     */
   public  function  editUser()
   {
     if(!empty($_GET)){
         $data=Db::table("bg_auth_group")->select();
         $admin=  Db::table("bg_admin")->where(['id'=>$_GET['id']])->find();
         $this->assign("data",$data);
         $this->assign("admin",$admin);
         return $this->fetch();
     }
     elseif(!empty($_POST))
     {
         $id=$_POST['id'];
         $group_id=$_POST['group_id'];
         $uname=$_POST['uname'];
         $nick_name=$_POST['nick_name'];
         if(!empty($_POST["password"]))
         {
             $password=md5($_POST['password']);
             $res= Db::table("bg_admin")->where(["id"=>$id])->update(["uname"=>$uname,"password"=>$password,"group_id"=>$group_id,"nick_name"=>$nick_name]);
         }
        else
        {
            $res= Db::table("bg_admin")->where(["id"=>$id])->update(["uname"=>$uname,"group_id"=>$group_id,"nick_name"=>$nick_name]);
        }
         if($res)
         {
             return json(["code"=>1,"msg"=>"修改成功"]);
         }
         else
         {
             return json(["code"=>-1,"msg"=>"修改失败"]);
         }
     }
  }

    public  function  listUserGroup()
   {

      if($this->request->post() || $this->request->isAjax()) {
          $data = $this->deDataTablesJson();
          $draw = $data['draw'];
          $start = $data["start"];
          $length = $data["length"];
          $where="1=1";
          $search="";
          if(!empty($_POST["search"])){
              $search=trim($_POST["search"]);
          }
         $data['data']= Db::table("bg_auth_group")->where($where)->limit($start,$length)->select();
          $data['count']= Db::table("bg_auth_group")->where($where)->count();
          if (!$data)
          {
              return json(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []]);
          }
          else
          {
              return json(["draw" => $draw, "recordsTotal" =>$data['count'] , "recordsFiltered" =>$data['count'] , "data" => $data['data']]);
          }
      }
      return $this->fetch();
  }
/*
 * 用户组删除
 *
 * */

   public function  groupDel(){

       if(!empty($_GET["id"])){
           $res=Db::table("bg_auth_group")->where(["id"=>$_GET['id']])->delete();
           if($res)
           {
               return json(["code"=>1,"msg"=>"删除成功"]);
           }
           else
           {
               return json(["code"=>-1,"msg"=>"删除失败"]);
           }

       }
   }

   /*
    * 用户组修改
    *
    * */
   public function groupEdit(){
       //弹出编辑框
      if(!empty($_GET["id"]))
      {
        $grouparr=Db::table("bg_auth_group")->where(["id"=>$_GET['id']])->find();
        $this->assign("grouparr",$grouparr);
        return $this->fetch();
      }
      //执行修改操作
      if(!empty($_POST["name"]))
      {
         $res= Db::table("bg_auth_group")->where(["id"=>$_POST['id']])->update(['name'=>$_POST['name']]);
          if($res)
          {
              return json(["code"=>1,"msg"=>"修改成功"]);
          }
          else
          {
              return json(["code"=>-1,"msg"=>"修改失败"]);
          }
      }
   }
/*
 * 添加用户组
 *
 * */

    public function  addGroup(){
        if(!empty($_POST['name'])){
          $res= Db::table('bg_auth_group')->insert(['name'=>$_POST['name']]);
            if($res)
            {
                return json(["code"=>1,"msg"=>"添加成功"]);
            }
            else
            {
                return json(["code"=>-1,"msg"=>"添加失败"]);
            }

        }
        else{
            return $this->fetch();
        }

    }
/*
 * 权限分配
 * */

    public function  assingPermissions()
    {
        if(!empty($_GET['id'])) {
            $rulearr = Db::table("bg_auth_rule")->select();
            $grouparr=Db::table("bg_auth_group")->where(['id'=>$_GET['id']])->find();
            $this->assign("grouparr", $grouparr);
            $data = self::dataList($rulearr);
            $this->assign("data", $data);
            return $this->fetch();
        }
        if(!empty($_POST['id'])){
            $rules=substr($_POST['rules'],0,strlen($_POST['rules'])-1);
            $res=  Db::table("bg_auth_group")->where(["id"=>$_POST['id']])->update(["rules"=>$rules]);
            if($res)
            {
                return json(["code"=>1,"msg"=>"修改成功"]);
            }
            else
            {
                return json(["code"=>-1,"msg"=>"修改失败"]);
            }
        }
    }

    public static function  dataList($rulearr)
    {
      $data=[];
       foreach ($rulearr as $key=>$val)
       {
           if($val['pid']==0)
           {
               $data[$val['id']]=$val;
           }
           if($val['ppid']==0 && $val['pid']!=0)
           {
               $data[$val['pid']]['data'][$val['id']]=$val;
           }
          if($val['ppid']!=0 && $val['pid']!=0)
          {
            $data[$val['pid']]['data'][$val['ppid']]['data'][$val['id']]=$val;
          }
       }
          return $data;

    }




}


