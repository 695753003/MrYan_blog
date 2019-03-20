<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2018/4/24
 * Time: 14:58
 */
namespace  app\common\model;

use think\Model;
use think\Db;
use think\Exception;
use think\Log;
class AuthRule extends Model{


    /**
     * 菜单列表查询
     *
     * */

    public function menuList($start,$length,$search){
        try{
            $where=" a.ppid=0";
            if(!empty($search)){
                $where=$where." and a.name like '%$search%'";
            }
            $data=Db::table("bg_auth_rule")->alias("a")
                ->field("b.name f_name,a.*")
                ->join("bg_auth_rule b","b.id=a.pid","left")
                ->where($where)
                ->limit($start,$length)
                ->select();
            $count=Db::table("bg_auth_rule")->alias("a")->where($where)->count();
            return array("data"=>$data,"count"=>$count);
        }catch (Exception $e){
            Log::record($e->getMessage());
            return false;
        }
    }
/**
 * 权限列表查询
 *
 *
 */
    public function permissionList($search,$start,$length){
        try{
            $where=" a.ppid!=0";
           if(!empty($search)){
                $where=$where." and b.name like '%$search%' or a.name like '%$search%'";
            }
            $data=Db::table("bg_auth_rule")->alias("a")->field("a.id,b.`name` b_name ,a.`name` a_name ,a.href")
                ->join("bg_auth_rule b","a.ppid=b.id","left")
                ->where($where)->limit($start,$length)->select();

            $count=Db::table("bg_auth_rule")->alias("a")
                ->join("bg_auth_rule b","a.ppid=b.id","left")
                ->where($where)->count();

            return array("data"=>$data,"count"=>$count);
        }catch (Exception $e){
            Log::record($e->getMessage());
            return false;
        }
    }


}