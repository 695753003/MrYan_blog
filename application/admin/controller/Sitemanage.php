<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/3 0003
 * Time: 13:38
 */

namespace app\admin\controller;

use think\Controller;

use think\Config;


class Sitemanage extends Controller{

    public function emailList()
    {
      $smtp=   Config::get("smtp");
      $this->assign("smtp",$smtp);
        return $this->fetch();

    }



    public function addfielemali(){
        if($this->request->isAjax() || $this->request->isPost()){
            if(!empty($_POST))
            {
                $str=  self::emaliConfig($_POST);

                if(file_put_contents("../application/extra/smtp.php",$str))
                {
                    return json(["code"=>1,'msg'=>"提交成功"]);
                }
                else{
                    return json(["code"=>0,'msg'=>"提交失败"]);
                }
            }
        }

    }


    //组装配置文件
     public static function emaliConfig($arr)
     {
         $data=[];
         foreach ($arr as $key=> $v)
         {
             $data[$key]=trim($v);
         }
        $str=<<<php
<?php
return [
'email_server'=>"{$data['email_server']}", //SMTP服务器地址
'encryption'=>"{$data['encryption']}", //加密方式
'port'=>"{$data['port']}", //端口号
'send_user'=>"{$data['send_user']}", //邮箱账号
'smtp_pwd'=>"{$data['smtp_pwd']}", //SMTP授权密码
];
php;

         return $str;
     }






}