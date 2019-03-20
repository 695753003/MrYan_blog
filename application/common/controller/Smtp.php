<?php
namespace  app\common\controller;


use Codeception\Module\REST;
use think\Config;
use phpmailer\PHPMailer;

class Smtp
{
    //发送评论邮件
        public static  function send_email($conent){

            //获取邮箱配置
            $arr=Config::get('smtp');

            // 实例化PHPMailer核心类
            $mail = new PHPMailer();
// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
            $mail->SMTPDebug = 0;
// 使用smtp鉴权方式发送邮件
            $mail->isSMTP();
// smtp需要鉴权 这个必须是true
            $mail->SMTPAuth = true;
// 链接qq域名邮箱的服务器地址
            $mail->Host = "{$arr['email_server']}";
// 设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure =  "{$arr['encryption']}";
// 设置ssl连接smtp服务器的远程服务器端口号
            $mail->Port ="{$arr['port']}";
// 设置发送的邮件的编码
            $mail->CharSet = 'UTF-8';
// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
            $mail->FromName = '博客评论';
// smtp登录的账号 QQ邮箱即可
            $mail->Username = "{$arr['send_user']}";
// smtp登录的密码 使用生成的授权码
            $mail->Password = "{$arr['smtp_pwd']}";
// 设置发件人邮箱地址 同登录账号
            $mail->From = "{$arr['send_user']}";
// 邮件正文是否为html编码 注意此处是一个方法
            $mail->isHTML(true);
// 设置收件人邮箱地址
            $mail->addAddress(Config::get("comment.admin_email"));
// 添加多个收件人 则多次调用方法即可
            //   $mail->addAddress('87654321@163.com');
// 添加该邮件的主题
            $mail->Subject = 'SMTP测试';
// 添加邮件正文
            $mail->Body = $conent;
// 为该邮件添加附件
//$mail->addAttachment('./123.jpg');
// 发送邮件 返回状态
            $request=  $status = $mail->send();
           $data =$request ? ["code"=>1,"发送成功"]:["code"=>0,"发送失败"];
            return $data;
        }





}