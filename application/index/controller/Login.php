<?php
namespace app\index\controller;

use qqlogin\QC;
use qqlogin\Recorder;
use think\Controller;
use think\Db;
use qqlogin\Oauth;


class  Login extends  Controller
{

    //请求权限 跳转第三方QQ登陆页面
    public function  qqlogin(){
       $oauth=new Oauth();
       $oauth->qq_login();
    }

    //回调地址
    public function qqcallback()
    {
        //实例化Oauth
        $oauth=new QC();
        $userinfo['accesstoken']=$oauth->qq_callback();
        $userinfo['openid']=$oauth->get_openid();
        cookie("userinfo",$userinfo); //存入Cookie
        session('state',null);  //清空当前随机验证码

    $user=  Db::table("bg_user")->where(['appid'=>$userinfo['openid']])->find();
     //判断是否第一次登陆
    if(empty($user))
    {
        //获取用户信息
        $qc= new QC($userinfo['accesstoken'],$userinfo['openid']);
        $userarr=$qc->get_user_info();
        //判断是否获取信息成功
        if(!empty($userarr['msg']))
        {
            throw new \think\Exception($userarr['msg']);
        }
        $data=['nick_name'=>$userarr['nickname'],
            'sex'=>$userarr['gender'],
            'city'=>$userarr['city'],
            'year'=>$userarr['year'],
            'appid'=>$userinfo['openid'],
            'type'=>1,//qq登陆
            'frist_time'=>time(),
            'last_time'=>time(),
            'head_img'=>$userarr['figureurl_1'],
            'status'=>1,
            "login_num"=>1,
            'last_ip'=>$this->request->ip(),
        ];

      if( Db::table("bg_user")->insert($data))
      {
          //添加成功跳转
       $this->redirect("index/index/index");
      }
      else{
          throw new \think\Exception('服务器错误，请重新登陆');
      }
    }
    else{
        $data=['last_time'=>time(),'last_ip'=>$this->request->ip()];
        if(Db::table("bg_user")->where(['appid'=>$userinfo['openid']])->update($data))
        {
            Db::table("bg_user")->where(['appid'=>$userinfo['openid']])->setInc("login_num");
            //更新成功跳转
               $this->redirect("index/index/index");
        }
        else
        {
            throw new \think\Exception('服务器错误，请重新登陆');
        }
    }
    }

    //退出登陆
    public function loginOut()
    {
    cookie("userinfo",null);

   $this->redirect("index/index/index");

    }


}
