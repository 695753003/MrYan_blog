<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23 0023
 * Time: 10:53
 */

namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Link;

//友情链接管理管理
class Friendship extends Praent{

    //链接列表
    public  function  friendshipList()
    {

        if($this->request->isAjax()|| $this->request->isPost())
        {

            $data = $this->deDataTablesJson();
            $draw = $data['draw'];
            $start = $data["start"];
            $length = $data["length"];
            $search="";
            if(!empty($_POST["search"])){
                $search=trim($_POST["search"]);
            }
            $data=model("Link")->select($start,$length);
            if (!$data)
            {
                return
                    json(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []]);
            }
            else
            {

                return json(["draw" => $draw, "recordsTotal" =>$data['count'] , "recordsFiltered" =>$data['count'] , "data" => $data['data']]);

            }

        }
        else
        {
            return $this->fetch();
        }


    }

//添加链接
public  function addLink(){
        if(!empty($_POST))
        {

            $_POST['addtime']=time();

            if(Link::create($_POST))
            {
                return json(["code"=>1,"msg"=>"添加成功"]);
            }
            else{
                return json(["code"=>-1,"msg"=>"添加失败"]);
            }

        }
        else{
            return $this->fetch();
        }
}

//修改
    public  function editLink(){
        if(!empty($_POST))
        {

            $id=$_POST['id'];
            $_POST['addtime']=time();

             unset($_POST['id']);

            if(Link::where(["id"=>$id])->Update($_POST))
            {
                return json(["code"=>1,"msg"=>"修改成功"]);
            }
            else{
                return json(["code"=>-1,"msg"=>"修改失败"]);
            }

        }
        elseif(!empty($_GET)){
            $id=$_GET['id'];
            $data=link::where(['id'=>$id])->find();
            $this->assign("data",$data);
            return $this->fetch();
        }
    }


        //删除链接
        public function del(){
        if(!empty($_GET)){

            if(Link::where(["id"=>$_GET['id']])->delete())
            {
                return json(["code"=>1,"msg"=>"删除成功"]);

            }
            else{
                return json(["code"=>-1,"msg"=>"删除失败"]);
            }
        }
}




}