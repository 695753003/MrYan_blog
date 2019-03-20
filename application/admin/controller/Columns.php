<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23 0023
 * Time: 10:27
 */
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Column;
use think\Db;

//栏目管理
class Columns extends Praent{

    //栏目列表
    public  function columnList()
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
            $data=model("Column")->getPage($start,$length);

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


    /*
     *
     * 状态修改
     * */
    public function editStatus(){

        if($this->request->isAjax()|| $this->request->isPost())
        {
            if(!empty($_GET['id']))
            {
              $status=   Db::table("bg_column")->where(['id'=>$_GET['id']])->value('status');
               if($status==1){
                  $res= Db::table("bg_column")->where(['id'=>$_GET['id']])->update(['status'=>0]);
               }
               else{
                   $res=   Db::table("bg_column")->where(['id'=>$_GET['id']])->update(['status'=>1]);
               }

               if($res)
               {
                   return ["code"=>1,"msg"=>"修改成功"];
               }
               else
               {
                   return ["code"=>1,"msg"=>"修改失败"];
               }

            }

        }

    }


    public  function  del()
    {
        if($this->request->isAjax()|| $this->request->isPost())
        {
            if(!empty($_GET['id']))
            {

               $res= Db::table("bg_column")->where(['id'=>$_GET['id']])->delete();

                if($res)
                {
                    return ["code"=>1,"msg"=>"删除成功"];
                }
                else
                {
                    return ["code"=>1,"msg"=>"删除失败"];
                }

            }

        }

    }

            public function  addColumn()
            {
                if($this->request->isAjax()||$this->request->isPost())
                {
                   if($_POST['id']==0)
                   {
                       //添加
                       unset($_POST['id']);

                      if(Db::table("bg_column")->insert($_POST))
                      {
                          return ['code'=>1,"msg"=>"添加成功"];
                      }
                      else
                          {
                              return ['code'=>1,"msg"=>"添加失败"];
                          }
                   }
                   else
                       {
                           $id=$_POST['id'];
                           unset($_POST['id']);
                          if(Db::table("bg_column")->where(['id'=>$id])->update($_POST))
                          {
                              return ['code'=>1,"msg"=>"修改成功"];
                          }
                          else{
                              return ['code'=>1,"msg"=>"修改失败"];
                          }
                       }
                }
                else
                    {
                    if(!empty($_GET['id']))
                    {
                      $data=Db::table("bg_column")->where(['id'=>$_GET['id']])->find();
                       $this->assign("data",$data);
                    }
                    return  $this->fetch();
                   }
            }






}