<?php

namespace app\admin\controller;


use think\Db;
use think\Request;

class AboutMe extends Praent
{
    public function myInfo()
    {
        if (!empty($_FILES['file'])){
            dump($_FILES);
            exit;
        }
        $data = Db::table("bg_about_me")->where(['id' => 1])->find();
        $this->assign("data", $data);
        return $this->fetch();

    }


    public function editAbout()
    {
        if (!empty($_POST)) {
            $res = Db::table("bg_about_me")->where(["id" => 1])->update($_POST);
            if ($res) {
                return json(["code" => 1, "msg" => "修改成功"]);
            } else {
                return json(["code" => 0, "msg" => "修改失败"]);
            }
        }
    }



}