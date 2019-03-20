<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23 0023
 * Time: 10:30
 */
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Category;

//类别管理
class Categorys extends Praent{

    //类别列表
    public  function  categoryList()
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
            $data=model("Category")->select($start,$length);
          //  dump($data);die;
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


    //添加
    public function  addCategory(){
        if(!empty($_POST['title']))
        {
           if(Category::create($_POST)){

                return json(['code'=>1,'msg'=>'添加成功']);
           }
           else{
               return json(['code'=>1,'msg'=>'添加失败']);
           }
        }
        else{
            return $this->fetch();
        }

    }

    //编辑
    public function  editCategory(){
        if(!empty($_POST['id']))
        {
           if( Category::where(['id'=>$_POST['id']])->Update(["title"=>$_POST['title']]))
           {
               return json(['code'=>1,'msg'=>'修改成功']);
           }
           else{
               return json(['code'=>1,'msg'=>'修改失败']);
           }
        }
        elseif(!empty($_GET['id']))
          {


            $data=  Category::where(['id'=>$_GET['id']])->find();

            $this->assign("data",$data);
            return $this->fetch();
          }
    }

//删除
public function del(){
        if(!empty($_GET['id']))
        {
            if(Category::where(['id'=>$_GET['id']])->delete())
            {
                return json(['code'=>1,'msg'=>'删除成功']);
            }
            else{
                return json(['code'=>1,'msg'=>'删除失败']);
            }
        }


}


}