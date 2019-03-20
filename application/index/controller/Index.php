<?php
namespace app\index\controller;
use app\common\controller\Smtp;
use app\index\model\Article;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;
use think\Model;
use think\Request;
use think\Controller;



class Index extends Base
{



protected  $child;

    public function  index()
    {

        try {
                if (Request::instance()->isGet()){
                //判断是类别ID
                if (input("co_id"))
                {
                    $data=["code"=>1,"co_id"=>input("co_id")];
                }
              //判断是标签ID
                elseif (input("ca_id"))
                {
                    $data=["code"=>2,"ca_id"=>input("ca_id")];
                }
                 //最新文章
                else
                {
                    $data=["code"=>3];
                }
            }
                $list=Article::getArticle($data);
                //判断分页查询的对象是否为空
                if($list->isEmpty())
                {
                    $res=["code"=>1,"message"=>"当前类别的文章还没有噢!"];
                }
                else{
                    $res=["code"=>2];
                }
              $this->assign("res",$res);
              $this->assign("list",$list);
              return $this->fetch();
        }
        catch (Exception $e)
        {
           Log::write($e->getMessage());
        }
    }

    //全局搜索
    public function searchComment(){

      dump(input("post."));
    }


  /*
   * 文章详情页
   * */
    public function mainBody() {
        if(Request::instance()->isGet())
            {
                if($id=input("id"))
                {
                    $text=Db::table("bg_article")->alias("a")->field("a.title `a_title`, a.* ,b.title `b_title` ")
                        ->join("bg_column b","a.co_id=b.id","left")
                        ->where(["a.id"=>$id])->find();

                    $category=Db::table("bg_category")->where("id","in",$text['ca_id'])->select();

                    //上一篇 下一篇
                    $last=  Db::table("bg_article")->field("id,title")->where(["co_id"=>$text['co_id']])
                        ->where("id <{$text['id']}")->order("addtime asc")->find();
                    $next=  Db::table("bg_article")->field("id,title")->where(["co_id"=>$text['co_id']])
                        ->where("id > {$text['id']}")->order("addtime desc")->find();

                    if(empty($last))
                    {
                        $last["id"]=0;
                        $last["title"]="没有了";
                    }
                    if(empty($next))
                    {
                        $next["id"]=0;
                        $next["title"]="没有了";
                    }

                    //自增阅读量
                    Db::table("bg_article")->where(["id"=>$text['id']])->setInc("click");

                     //获取文章评论
                      $commentTrre=self::getComment($text['id']);
                      $this->assign("last",$last);
                      $this->assign("next",$next);
                      $this->assign("category",$category);
                      $this->assign("text",$text);
                      $this->assign("commentTrre",$commentTrre);

                    //  dump($commentTrre);exit;
                      return $this->fetch();

                }
            }
    }

//添加评论
    public function comment(){
        if($this->request->isAjax()||$this->request->isPost())
        {
            if($post=$this->request->post())
            {

                if(mb_strlen($post["comment"],"UTF8")>=100)
                {
                    return json(["code"=>0,"msg"=>"您的评论太长了"]);
                }
                if(empty($post['comment']))
                {
                    return json(["code"=>0,"msg"=>"评论不能为空"]);
                }
                else{
                    //获取用户ID
                   $userinfo=cookie("userinfo");
                   if(empty($userinfo))
                   {
                       return json(["code"=>2,"msg"=>"请先登陆"]);
                   }
                   else{
                       $user=Db::table("bg_user")->field("id,status")->where(['appid'=>$userinfo['openid']])->find();
                        $uid=$user['id'];
                       if($user['status']==0)
                       {
                           return json(["code"=>0,"msg"=>"已被禁止评论"]);
                       }
                   }


                    //验证邮箱格式
                    $regex="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
                    if(preg_match($regex,$post["email"]))
                    {
                        Db::table("bg_user")->where(["id"=>$uid])->update(["email"=>$post["email"]]);
                    }

                    $time=time()-10;

                    //10秒内同一篇文章不能重复评论
                  $addtime=  Db::table("bg_comment")->where(["aid"=>$post['aid'],"uid"=>$uid])->order("addtime desc")->value("addtime");

                if($addtime<$time)
                {

                    //获取是否开启评论审核
                    $comment_config=Config::get("comment");
                    $status=$comment_config['is_comment_status']?0:1;
                    //判断是否为一级评论
                      if(!empty($post['pid']))
                      {

                          $reply_name= Db::table("bg_comment")->alias("a")
                              ->join("bg_user b" ,"a.uid=b.id","left")
                              ->where(["a.id"=>$post['pid']])
                              ->value("b.nick_name");


                          $data=[
                              "content"=>trim($post['comment']),
                              "uid"=>$uid,
                              "reply_name"=>$reply_name,
                              "pid"=>trim($post['pid']),
                              "aid"=>  trim($post['aid']),
                              "addtime"=>time(),
                              "status"=>$status
                          ];


                      }
                      else{
                          $data=[
                              "content"=>trim($post['comment']),
                              "uid"=>$uid,
                              "aid"=>  trim($post['aid']),
                              "addtime"=>time(),
                              "status"=>$status
                          ];
                      }

                 $res=   Db::table("bg_comment")->insert($data);
                      //判断是否开启发送评论邮件
                      if($comment_config['is_send_email_status']==1)
                      {
                        $data=   Smtp::send_email("博客评论：{$post['comment']}");
                         if($data['code']==0)
                         {
                             Log::write("评论邮件发送失败","notice");
                         }
                      }

                 $json= !empty($res) ? ["code"=>1,"msg"=>"评论成功"]:["code"=>0,"msg"=>"评论失败"];
                  return json($json);
                }
                else{
                    return json(["code"=>0,"msg"=>"评论太过频繁"]);
                }
                }
            }
        }
    }





//文章评论
   public  function getComment($aid)
   {

       //获取所有一级评论
      $data=  Db::table("bg_comment")->alias("a")->field("a.*,b.nick_name,b.head_img")
            ->join("bg_user b","a.uid=b.id","left")
            ->where(["aid"=>$aid,"a.status"=>1])
            ->where("pid is null")
            ->order("addtime asc")
            ->select();


       //递归获取二级评论
       foreach ($data as $key=>$val)
       {

           $this->tree($val);
           $data[$key]["child"]=$this->child;
          //清空child;
           $this->child="";
       }

      return $data;
   }


   //递归获取所有评论树
public  function tree($data)
{

    if(!empty($data)) {
        $comment = Db::table("bg_comment")->alias("a")->field("a.*,b.nick_name,head_img")
            ->join("bg_user b", "a.uid=b.id", "left")
            ->where(["aid" => $data['aid'], "a.status" => 1, "pid" => $data['id']])
            ->where("pid is not null")
            ->order("addtime asc")
            ->select();

        if (!empty($comment)) {
            foreach ($comment as $v) {
                $this->child[] = $v;
                $this->tree($v);
            }
        }
    }
 }


//点赞
   public  function like(){
            if($this->request->isAjax() || $this->request->isGet())
            {

                if($aid=$this->request->get("aid"))
                {
                    $ip= $this->request->ip();
                    $res=Db::table("bg_like")->where(["aid"=>$aid,"ip"=>$ip])->find();

                   if(empty($res))
                   {
                       Db::table("bg_article")->where("id={$aid}")->setInc("is_like");

                       Db::table("bg_like")->insert(["aid"=>$aid,"ip"=>$ip]);

                       return json(["code"=>1,"msg"=>"谢谢"]);
                   }
                   else
                  {
                        return json(["code"=>0,"msg"=>"已经赞过了哦"]);
                  }
                }
            }
    }









}
