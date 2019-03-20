<?php
namespace  app\admin\controller;

use think\Db;

class ItUser extends Praent
{

    //前台用户列表
    public function  ItUserList(){

        $userlist= Db::table("bg_user")->order("last_time desc")->paginate(10);

        $this->assign("userlist",$userlist);

        return $this->fetch();

    }

        //修改用户状态
        public function editStatus()
        {
            if($this->request->isAjax() || $this->request->isPost())
            {
                if($data= $this->request->post())
                {
                    $status=$data["status"]?0:1;

                    $res= Db::table("bg_user")->where(["id"=>$data['id']])->update(["status"=>$status]);
                    if($res)
                    {
                        return json(["code"=>1,"msg"=>"修改成功"]);
                    }
                    else
                    {
                        return json(["code"=>0,"msg"=>"修改失败"]);
                    }
                }
            }


        }


}