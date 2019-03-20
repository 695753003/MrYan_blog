<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/13
 * Time: 14:26
 */
namespace  app\admin\controller;
use think\Controller;
use Think\Db;
use think\Request;
use app\admin\model\AuthRule;
class Settings extends Praent
{
    /**
     *
     * @return mixed|string|\think\response\Json
     *
     * 菜单列表
     *
     *
     */
    public  function menu()
    {
            if ($this->request->post() || $this->request->isAjax()) {
            $data = $this->deDataTablesJson();
            $draw = $data['draw'];
            $start = $data["start"];
            $length = $data["length"];
                $search="";
                if(!empty($_POST["search"])){
                    $search=trim($_POST["search"]);
             }
           $data=model("AuthRule")->menuList($start,$length,$search);
            if (!$data)
            {
            return
                json(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []]);
            } else
            {
            return json(["draw" => $draw, "recordsTotal" =>$data['count'] , "recordsFiltered" =>$data['count'] , "data" => $data['data']]);
            }
            }
            else{
           return $this->fetch();
            }
    }

        /**
         *添加菜单页面
         *
         */

    public  function addNav(){
      $add=0;
     if(!empty($_GET["id"]))
     {
        $add=1;
        $arr=  AuthRule::where(["pid"=>0])->select();
        $da= json_decode(json_encode($arr));
        $this->assign("da",$arr);
     }
       $this->assign("add",$add);

       return $this->fetch();

}

    /**
     *
     * @return string|\think\response\Json
     * 添加操作
     *
     */
public function nav()
{

    if(!isset($_POST['url']))
    {

     if(AuthRule::create(["pid"=>0,"ppid"=>0,"name"=>$_POST['title'],"type"=>1,"status"=>1]))
        {
            return json(["code"=>1,"msg"=>"添加成功"]);
        }
        else
        {
            return json(["code"=>1000,"msg"=>"添加失败"]);
        }
    }
    else{
        if(AuthRule::create(["pid"=>$_POST['pid'],"ppid"=>0,"name"=>$_POST['title'],"href"=>$_POST['url'],"type"=>1,"status"=>1]))
        {
            return json(["code"=>1,"msg"=>"添加成功"]);
        }
        else
        {
            return json(["code"=>1000,"msg"=>"添加失败"]);
        }
    }
}

    /**
     *
     * @return mixed
     *
     * 编辑页面
     *
     */
public  function  editNav(){
    if(isset($_GET) )
    {
        $id= $_GET["id"];
        $pid= $_GET["pid"];
        $arr=AuthRule::where(["id"=>$id])->find()->toArray();
      /*判断是否为顶级菜单*/
      if($pid!=0)
      {
        $navarr=AuthRule::where(["pid"=>0])->select();
        $this->assign("navarr",$navarr);
      }
        $this->assign("arr",$arr);
      return $this->fetch();
    }

}
    /**
     *
     * @return string|\think\response\Json
     * 执行编辑
     *
     */

public function  edit(){
    if(isset($_POST))
    {
        $id=$_POST['id'];
        unset($_POST['id']);  //剔除ID字段

        if(AuthRule::where(["id"=>$id])->Update($_POST))
        {
           return json(["code"=>1,"msg"=>"修改成功"]);

        }
        else
        {
          return json(["code"=>-1,"msg"=>"修改失败"]);
        }
    }
}

    /**
     * @return string|\think\response\Json
     * 删除操作
     *
     */
        public  function  del(){
        if(!empty($_GET))
        {

          if( AuthRule::where(["id"=>$_GET["id"]])->delete())
           {

               return json(["code"=>1,"msg"=>"删除成功"]);
           }
           else {

               return json(["code"=>1000,"msg"=>"删除失败"]);
           }
        }

}
/**
 * 权限列表
 *
 * */
  public   function  ruleList(){
      if ($this->request->post() || $this->request->isAjax()) {
          $data = $this->deDataTablesJson();
          $draw = $data['draw'];
          $start = $data["start"];
          $length = $data["length"];

          $search="";
          if(!empty($_POST["search"])){
              $search=trim($_POST["search"]);
          }
          $data=model("AuthRule")->permissionList($search,$start,$length);

          if (!$data)
          {
              return
                  json(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []]);
          } else
          {
              return json(["draw" => $draw, "recordsTotal" =>$data['count'] , "recordsFiltered" =>$data['count'] , "data" => $data['data']]);
          }
      }
      else{
          return $this->fetch();
      }

  }
    /**
     * 权限添加页面
     *
     */
     public function addPageRule()
     {
      $one= AuthRule::where(["pid"=>0])->select();
      $this->assign("one",$one);
      return $this->fetch();
     }
     /**
      * AJAX拉取option
      * */
    public function subMenu()
    {
      if(!empty($_GET))
      {
          $arr=  AuthRule::where(["pid"=>$_GET['id'],"ppid"=>0])->select();
          return json_encode($arr);
      }
    }


/*
 * 执行添加操作
 *
 * */
    public function addRule(){
         if(!empty($_POST)){
            if(AuthRule::create($_POST))
            {
                return json(["code"=>1,"msg"=>"添加成功"]);
            }
             else{
                 return json(["code"=>-1,"msg"=>"添加失败"]);
            }
         }
    }

    /*
     * 编辑页面
     * */
    public function editPageRule(){

        if(!empty($_GET['id']))
        {
          $rule= AuthRule::get($_GET['id']);
            $this->assign("rule",$rule);
        }
        $one= AuthRule::where(["pid"=>0])->select();
        $this->assign("one",$one);
        $two=  AuthRule::where(["pid"=>$rule['pid'],"ppid"=>0])->select();
        $this->assign("two",$two);
        return $this->fetch();
    }
    /*
     * 执行编辑
     * */
    public function editRule(){
           if(!empty($_POST))
           {
               $id=$_POST['id'];
               unset($_POST['id']);
               if(AuthRule::where(["id"=>$id])->Update($_POST))
               {
                   return json(["code"=>1,"msg"=>"修改成功"]);
               }
               else{
                   return json(["code"=>-1,"msg"=>"修改失败"]);
               }

           }
    }



}
