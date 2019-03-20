<?php
namespace app\admin\controller;

use think\Db;
class Image extends Praent
{

    public function  imgList(){

         $listImg= Db::table("bg_image")->where(["status"=>1])->paginate(10);

        $useList= Db::table("bg_use")->select();
        $this->assign("useList",$useList);
        $this->assign("listImg",$listImg);
         return $this->fetch();

    }


    public function edit()
    {
     if($this->request->isAjax()|| $this->request->isPost())
     {
         if($post=$this->request->post())
         {
             //限制数量
             $num=Db::table("bg_use")->where(["id"=>$post['use_id']])->value("num");
             //已经绑定的数量
             $count=Db::table("bg_image")->where(["use_id"=>$post['use_id']])->count();
             if($post['use_id'])
             {
                 if($num>$count)
                 {
                     if(Db::table("bg_image")->where(['id'=>$post['img_id']])->update(["use_id"=>$post['use_id']]))
                     {
                         return json(["code"=>1,"msg"=>"修改成功"]);
                     }
                     else
                     {
                         return json(["code"=>0,"msg"=>"修改失败"]);
                     }
                 }
                 else
                 {
                     return json(["code"=>0,"msg"=>"无法修改，请先修改您需要替换的图片状态"]);
                 }
             }
             else{
                 if(Db::table("bg_image")->where(['id'=>$post['img_id']])->update(["use_id"=>$post['use_id']]))
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

    public function del(){
        if($this->request->isGet() || $this->request->isAjax())
        {
            $id=$this->request->Get('id');
            if($id)
            {
                if(Db::table("bg_image")->where(["id"=>$id])->update(["status"=>99]))
                {
                    return json(["code"=>1,"msg"=>"删除成功"]);
                }
                else
                    {
                        return json(["code"=>0,"msg"=>"删除失败"]);
                    }
            }

        }
    }



    public function thumb()
    {

        return $this->fetch();
    }

    public function fileUploader()
    {

        $file = $this->request->file('file');
        //生成一个唯一的hash KEY 判断是否上传重复
        $hash = $file->hash();
        $res = Db::table("bg_image")->where(["hash" => $hash])->find();
        if (!empty($res)) {
            return json(["code" => 0, "msg" => "已经上传过了"]);
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(["ext" => "jpg,png,jpeg,gif"])->move(ROOT_PATH . 'public' . DS . 'static'. DS . 'about_img');
        if ($info) {
            // 成功上传后 获取上传信息
            $data['path'] = "__STATIC__" . str_replace('\\', "/", "/about_img/" . $info->getSaveName());
            $res = Db::table("bg_image")->insert([
                "src" => $data['path'],
                "hash" => $hash,
                "create_time" => time()
            ]);
            if ($res) {
                return json(["code" => 1, "msg" => "上传成功"]);
            } else {
                return json(["code" => 0, "msg" => "上传失败"]);
            }

            //  dump($data['path']);exit;


        } else {
            // 上传失败获取错误信息
            return json(["code" => 0, "msg" => "{$file->getError()}"]);

        }


    }

}