<?php
namespace app\index\model;

use Egulias\EmailValidator\EmailLexer;
use think\Model;

class Article extends Model
{
    public static function getArticle($data=null){
        if(!empty($data))
        {
            switch ($data['code']){
                //分类查询
                case 1;$where="co_id={$data['co_id']}";  break;
                //标签查询
                case 2;$where="FIND_IN_SET({$data['ca_id']},ca_id)";break;
                //默认最新
                case 3;$where="1=1"; break;
            }

            $list = Model("article")->alias("a")->field("a.*,b.title `b_title`")
                ->join("bg_column b", "a.co_id=b.id", "left")
                ->where($where)
                ->where(['a.is_show'=>1])
                ->order("addtime desc")->paginate(6);
          return $list;
        }
    }



}