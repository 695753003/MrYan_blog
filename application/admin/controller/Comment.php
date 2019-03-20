<?php
namespace app\admin\controller;

use think\Db;
use think\Config;


class Comment extends  Praent
{

    public function  commentList()
    {

        $data= Db::table("bg_comment")->alias("a")->field("a.*,b.title,c.nick_name")
            ->join("bg_article b","a.aid=b.id")
            ->join("bg_user c","a.uid=c.id")
            ->order("addtime desc")
            ->paginate(10);

         $this->assign("comment",$data);
        return $this->fetch();
    }

    public function audit (){
        if($this->request->isAjax() || $this->request->isPost())
        {
            if($data= $this->request->post())
            {
               $status=$data["status"]?0:1;
               $res= Db::table("bg_comment")->where(["id"=>$data['id']])->update(["status"=>$status]);

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

    public function del(){
       if($this->request->isAjax() || $this->request->isGet())
       {
           if($id=$this->request->get("id"))
           {
              $res= Db::table("bg_comment")->where(["id"=>$id])->delete();
              if($res)
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


    public function addconfig()
    {
        if($this->request->isAjax() || $this->request->isPost()){
            if(!empty($_POST))
            {
                $str=  self::commentConfig($_POST);

                if(file_put_contents("../application/extra/comment.php",$str))
                {
                    return json(["code"=>1,'msg'=>"提交成功"]);
                }
                else{
                    return json(["code"=>0,'msg'=>"提交失败"]);
                }
            }
        }
        else{
          $comment_cfg=  Config::get("comment");
           $this->assign("comment_cfg",$comment_cfg);
        }

        return $this->fetch();
    }

    public static function  commentConfig($data)
    {
      $arr=[];
      foreach ($data as $key=>$v)
      {
          $arr[$key]=trim($v);
      }

        $str=<<<php
<?php
return [
  "is_comment_status"=>"{$arr['is_comment_status']}", //是否开启评论审核1：开启0：关闭
  "is_send_email_status"=>"{$arr['is_send_email_status']}", //是发生评论邮件1：开启0：关闭
  "admin_email"=>"{$arr['admin_email']}", //发送到邮箱地址
  ];
php;
        return $str;
    }



}