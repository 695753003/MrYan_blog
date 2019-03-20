<?php
namespace app\admin\controller;


use think\Controller;
use think\Db;
class  Gossip extends Controller

{

    public function gosList(){
      $listGos= Db::table("bg_gossip")->paginate(10);
      $this->assign("listGos",$listGos);
      return $this->fetch();

    }

     public function addOrEditGossip(){
        if($this->request->isAjax() ||$this->request->isPost())
        {
            if($_POST['id']=="")
            {
                //添加
              if( Db::table("bg_gossip")->insert(["content"=>trim($_POST['content']),"create_time"=>time()]))
                {
                    return json(["code"=>1,"msg"=>"添加成功"]);
                }
                else
                 {
                    return json(["code"=>0,"msg"=>"添加失败"]);
                 }
            }
            else{
                //更新
                if( Db::table("bg_gossip")->where(["id"=>$_POST['id']])->update(["content"=>trim($_POST['content'])]))
                {
                    return json(["code"=>1,"msg"=>"修改成功"]);
                }
                else
                {
                    return json(["code"=>0,"msg"=>"修改失败"]);
                }
            }
        }
        elseif ($this->request->isGet())
        {
            if($id=input("id"))
            {
              $gossiparr= Db::table("bg_gossip")->where(["id"=>$id])->find();
               $this->assign(["gossiparr"=>$gossiparr,"btn"=>"修改"]);
            }
            else
            {
                $this->assign(["btn"=>"增加"]);
            }
            return $this->fetch();
        }

     }

       public function del(){
        if ($this->request->isGet())
       {
            if($id=$this->request->get("id"))
          {
             if(Db::table("bg_gossip")->where(["id"=>$id])->delete())
             {
                 return json(["code"=>1,"msg"=>"删除成功"]);
             }
             else
             {
                 return json(["code"=>0,"msg"=>"删除失败"]);
             }
            }
            return $this->fetch();

        }

       }



}