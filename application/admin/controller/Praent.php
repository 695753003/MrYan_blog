<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/3/12
 * Time: 18:52
 */

namespace  app\admin\controller;

use think\Controller;

use  app\admin\model\Rule;
use  app\admin\model\Administrator;
use  app\admin\model\AdminNav;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;



class Praent extends Controller{

    public function __construct()
    {
        /*调用父级的构造函数*/
        parent::__construct();
        $admins=cookie("admins");
        if(empty($admins))
        {
            $this->redirect("login/landing");
        }
        else{
            $data=Db::table("bg_auth_rule")->where("FIND_IN_SET(id,(SELECT rules from bg_auth_group WHERE id={$admins["group_id"]} ))")->select();

            $menu=$this->menu_list($data);

            $this->assign("menu_data",$menu);
            $this->assign("nick_name",$admins['nick_name']);
            $request=Request::instance();
            $model=$request->module();  //模块名
            $controller=$request->controller();//控制器名
            $action=$request->action();//方法名
            $url=$model."/".$controller."/".$action;
            if($controller!="Index"){
                $res=self::auth($url,$admins["group_id"]);

                if($res["code"]==0){
                    if($this->request->isAjax()||$this->request->isPost() ){
                       $this->redirect('Index/ajaxPermission');
                    }else{
                        $this->redirect('Index/Permission');
                    }
                }
            }
        }
    }

    //分页插件调用
    public function deDataTablesJson()
    {
        try {
            $d=$_POST["data"];
            foreach($d as $key=>$value)
            {
                $data[$value["name"]]=$value["value"];
            }

            return $data;
        }
        catch (Exception $e)
        {
            Log::record($e->getMessage());
            return false;
        }

    }
    /**
     * 左侧菜单列表
     * @author wcy
     * @version 2017年4月18日09:47:26
     */
    public function menu_list($data){
        try{
            $menu_data=null;
            foreach($data as $key=>$value){

                if($value["pid"]==0){
                    $menu_data[$value["id"]]=$value;
                }
                elseif($value["pid"]!=0&&$value["ppid"]==0){
                    $menu_data[$value["pid"]]["data"][]=$value;
                }
            }
           $data=[];
              foreach ($menu_data as $key=>$value)
              {
                   if(!isset($value["data"]))
                   {
                       $value['data']="";
                   }
                  $data[$key]=$value;
              }

          return $data;

        }catch (Exception $e)
        {
            Log::record($e->getMessage());
        }
    }

/**
 * Auth验证
 *
 **/
public static function auth($controller,$id){
    try {
        $rule = Db::name("auth_rule")->where(["href" => $controller])->find();
        if (!empty($rule)) {
           $arr=  Db::table("bg_auth_group")->where(['id'=>$id])->where("FIND_IN_SET({$rule['id']},rules)")->find();

            if (!empty($arr)) {return ["code"=>1,"message"=>"权限通过"];}
            else  { return ["code"=>0,"message"=>"无权限"]; }
            }
            else { return ["code"=>1,"message"=>"权限通过"]; }
        }
    catch (Exception $e)
    {
        Log::record($e->getMessage());
        return false;
    }
}




}