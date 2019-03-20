<?php


namespace app\admin\controller;
use think\Config;

class ThirdPartyLogin extends  Praent
{
       public function TPlogin()
       {

         //qq第三方登陆配置
             $qq=Config::get("qq");
             $this->assign("qq",$qq);
            return $this->fetch();
       }

       //获取并编写配置文件
          public function addfileconfig()
          {
              if($this->request->isAjax() || $this->request->isPost()){
                  if(!empty($_POST))
                  {
                   $str=  self::qqconfig(trim($_POST['qq_appid']),trim($_POST['qq_appkey']),trim($_POST['qq_callback']));

                      if(file_put_contents("../application/extra/qq.php",$str))
                      {
                          return json(["code"=>1,'msg'=>"提交成功"]);
                      }
                      else{
                          return json(["code"=>0,'msg'=>"提交失败"]);
                      }
                  }
              }
          }





    public static function qqconfig($appid,$appkey,$callback){
           $str=<<<php
<?php
return [
    'appid' => "{$appid}",
    'appkey' => "{$appkey}",
    'callback' => "{$callback}",//回调地址：与qq互联创建应用时的地址一致
    'scope' => "get_user_info",//请求的权限
    'errorReport' => true,
    'storageType' => "file",
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'root',
    'database' => 'test',
];
php;
 return $str;
    }



}




















