<?php
namespace  app\index\controller;

use app\admin\controller\Praent;
use Symfony\Component\HttpKernel\Tests\DataCollector\DataCollectorTest;
use think\Controller;
use think\Cookie;
use think\Db;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $column=  Model("column")->order("`order` asc")->select();//文章分类
    //    $this->assign("column",$column);

         //LOGO
        $about["src"]= Db::table("bg_image")->where(["use_id"=>2])->value("src");
        //简介
        $intro= Db::table("bg_about_me")->where(["id"=>1])->value("intro");
        $about["intro"] = preg_replace("/\<.+?\>/i", '', $intro);

       //热门标签
        $catearr= Db::table("bg_category")->select();
        $catelist=[];
        foreach ($catearr as  $key=>$cate)
        {
            $cate["count"]=  Db::table("bg_article")->where("FIND_IN_SET({$cate['id']},ca_id)")->count();
            $catelist[$key]=$cate;
        }

       //置顶推荐
       $link_article=Db::table("bg_article")->field("id, title")->where(["is_top"=>1])->order("addtime desc ")->select();

       //友链
       $linkarr=  Db::table("bg_link")->order("orders asc")->select();

       //最新评论
    $comment=   Db::table("bg_comment")->alias("a")->field("a.*,b.title,c.nick_name,c.head_img")
           ->join("bg_article b","a.aid=b.id","left")
           ->join("bg_user c","a.uid=c.id","left")
           ->order("addtime desc")
           ->limit(20)->select();
        $user=cookie('userinfo');
        if(!empty($user))
        {
          $userarr= Db::table("bg_user")->field("id,nick_name , head_img")->where(['appid'=>$user['openid']])->find();

        }
        else
        {
            $userarr=[];
        }

        $this->assign(
            ["column"=>$column,
                "about"=>$about,
                "catelist"=>$catelist,
                "link_article"=>$link_article,
                "linkarr"=>$linkarr,
                "list_comment"=>$comment,
                'userarr'=>$userarr,
            ]);

    }



}