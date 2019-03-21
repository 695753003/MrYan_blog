<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23 0023
 * Time: 10:09
 */
namespace  app\admin\controller;
use think\Controller;
use think\Db;



//文章管理
class Articles extends Praent{

    //文章列表
    public  function  articleList()
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
            $data=model("Article")->getPage($start,$length);

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

         return $this->fetch();
    }

     public function  addArticle()
     {
         if($this->request->isAjax() || $this->request->isPost()){
           if(!empty($_POST))
           {
              $yestitle= Db::table("bg_article")->where(["title"=>$_POST['title']])->find();
              //过滤所有的标签
               $contentes = preg_replace("/\<.+?\>/i", '', $_POST['editorValue']);
               $data['content']=$_POST['editorValue'];
                //获取src的路径
               $pattern='/<img((?!src).)*src[\s]*=[\s]*[\'"](?<src>[^\'"]*)[\'"]/i';
               preg_match_all($pattern,$_POST['editorValue'],$match);


               if(!empty($match['src']))
               {
                   $data['thumbnail']=$match['src'][0];
               }
               else{
                   $data['thumbnail']="";
               }
               $description=   mb_substr($contentes, 0, 100) ;
               $data['title']=$_POST['title'];             //标题
               $data['author']=$_POST['author'];           //作者
               $data['content']=$_POST['editorValue'];     //内容
               $data['keywords']=$_POST['keywords'];       //关键词
               $data['description']=$description;           //描述
               $data['is_show']=$_POST['is_show'];         //是否显示
               $data['is_top']=$_POST['is_top'];           //是否置顶
               $data['is_original']=$_POST['is_original'];   //是否原创
               $data['co_id']=$_POST['column_id'];          //所属栏目
               $data['ca_id']= substr($_POST['category'],0,strlen($_POST['category'])-1) ; //标签         //所属标签

                if(empty($_POST['id']))
                {
                    if(!empty($yestitle))
                    {
                        return ['code'=>0,"msg"=>"此标题已存在"];
                    }
                    $data['addtime']=time();        //创建时间
                    //添加
                    if(Db::table("bg_article")->insert($data))
                    {
                        return ['code'=>1,"msg"=>"发表成功"];
                    }
                    else
                   {
                        return ['code'=>0,"msg"=>"发表失败"];
                    }
                }
               else{
                   //编辑
                   if(Db::table("bg_article")->where(['id'=>$_POST['id']])->update($data))
                   {
                       return ['code'=>1,"msg"=>"修改成功"];
                   }
                   else
                   {
                       return ['code'=>0,"msg"=>"修改失败"];
                   }
               }
           }
         }
         if(!empty($_GET['id']))
         {
          $data=Db::table("bg_article")->where(['id'=>$_GET['id']])->find();
          $this->assign("data",$data);
         }

          $column=  Db::table("bg_column")->select();
          $category=  Db::table("bg_category")->select();
          $this->assign("column",$column);
          $this->assign("category",$category);
          return $this->fetch();
     }

    public function del()
    {

       if(!empty($_GET['id']))
       {
           if(Db::table("bg_article")->where(['id'=>$_GET['id']])->delete())
           {
               return json(['code'=>1,"msg"=>'删除成功']);
           }
           else{
               return json(['code'=>0,"msg"=>'删除失败']);
             }
       }
    }

    public function editShow()
    {
        if(!empty($_GET['id']))
        {
          if(Db::table("bg_article")->where(['id'=>$_GET['id']])->value("is_show"))
          {
             $res= Db::table("bg_article")->where(['id'=>$_GET['id']])->update(['is_show'=>0]);
          }
          else {
              $res= Db::table("bg_article")->where(['id'=>$_GET['id']])->update(['is_show'=>1]);
          }
          if($res)
          {

              return json(['code'=>1,"msg"=>"修改成功"]);

          }
          else{

              return json(['code'=>0,"msg"=>"修改失败"]);

          }
        }
    }



}