<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model
{
    //self::MODEL_UPDATE
    //self::MODEL_INSERT
    //self::MODEL_BOTH
    //self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
    //self::MUST_VALIDATE 或者1 必须验证
    //self::VALUE_VALIDATE或者2 值不为空的时候验证
    /**
     * $_validate 自动验证
     * @author kevin.liu
     **/
    protected $_validate = array(
        array('username', 'require', '帐号必须填写'),
        array('password', 'require', '密码必须填写'),
        array('name', 'require', '姓名必须填写'),
        array('email', 'require', '邮件必须填写'),
        array('email', 'email', '邮件格式错误'),
        array('phone', 'require', '电话必须填写'),
        array('sort', 'number', '排序只能是数字'),
        array('verify', 'checkcode', '验证码不正确', 0, 'function'),

//        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
//        array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    );

    protected $_auto = array(
        array('addtime', 'time', self::MODEL_INSERT, 'function'),
        array('last_time', 'time', self::MODEL_INSERT, 'function'),
        array('last_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );


    /**
     * alogin　登录操作
     * @reutrn string
     * @author kevin.liu
     **/
    public function login()
    {
        $data = $this->create($_POST, 2);
        if(empty($data)){
            return false;
        }
        $userWhere = array(
            'username' => trim($data['username']),
            'status'   => 1
        );

        $res = M('admin') ->where($userWhere)->find();
        if($res){
            $password = md5Encrypt(trim($data['password']), $res['id']);
            if($password != $res['password']){
                $this->error = "密码输入有误！";
                return false;
            }
            $lastData = array(
                'last_time' => time(),
                'last_ip'   => get_client_ip(),
                'login_num' => $res['login_num']+1
            );
            $this->where($userWhere)->save($lastData);
            session(C('ADMIN_UID'), $res['id']);
            session(C('USERNAME'), $res['name']);
            return $res;
        }else{
            $this->error = '用户不存在！';
            return false;
        }
    }

}