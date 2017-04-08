<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17-4-6
 * Time: 下午10:37
 */
namespace Home\Service;

use Think\Model;
//阿里大与
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;


class SmsService extends Model
{
    protected $autoCheckFields = False;

    public function __construct()
    {
        parent::__construct();
    }

    //注册的时候验证码
    public function register($mobile)
    {
        $ali_open = C('ali_open_start');
        if(!$ali_open) {
            //如果短信开关已经关闭状态 则不用短信直接可以注册
            return true;
        }

        $number = rand(100000, 999999);
        // 配置信息
        $config = [
            'app_key'    => C('ali_open_key'),
            'app_secret' => C('ali_open_secret'),
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        ];
        // 使用方法一
        $client = new Client(new App($config));
        $req    = new AlibabaAliqinFcSmsNumSend;

        $req->setRecNum($mobile)
            ->setSmsParam(['number' => $number])
            ->setSmsFreeSignName('天天博客')
            ->setSmsTemplateCode('SMS_60320244');

        $resp = $client->execute($req);

        // 返回结果
        if($resp->result->success == 1){
            //以 手机号 为key,验证码为value 进行缓存2分钟
            S($mobile,$number,125);
            return true;
        }else{
            \Think\Log::write('短信发送失败:'.$mobile,'error','','regist_sms');
            $this->error = '发送失败，稍后再试';
            return false;
        }


    }
}