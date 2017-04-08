<?php
namespace Home\Model;

class UserModel extends PrivateModel
{
    //self::MODEL_UPDATE
    //self::MODEL_INSERT
    /**
     * $_validate 自动验证
     * @author kevin.liu
     **/
    protected $_validate = array(
        array('mobile', 'require', '手机号不能为空！'),
        array('mobile', 'number', '手机号必须是数字'),
        array('mobile','','手机号已经注册过！',0,'unique',self::MODEL_INSERT),   //两种写法都可以
//        array('mobile','checkUnique','手机号已经注册过！',0,'unique',self::MODEL_INSERT,'callback'),


        array('password', 'require', '密码必须填写'),
        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致

        array('name', 'require', '用户名必须填写'),

        array('email', 'require', '邮件必须填写'),
        array('email', 'email', '邮件格式错误'),

        array('code', 'require', '验证码不能为空'),
        array('code', 'check_register_code', '验证码不正确', 0, 'callback'),


//        array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    );
    //self::MODEL_BOTH
    //self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
    //self::MUST_VALIDATE 或者1 必须验证
    //self::VALUE_VALIDATE或者2 值不为空的时候验证

    protected $_auto = array(
        array('addtime', 'time', self::MODEL_INSERT, 'function'),
        array('last_time', 'time', self::MODEL_INSERT, 'function'),
        array('last_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );

    /**
     * 检查用户名唯一性
     * @return bool
     */
    function checkUnique()
    {
        $map = array();
        $map['mobile'] = array('eq', trim($_REQUEST['mobile']));
        $res = $this->where($map)->find();
        if(empty($res)){
            return true;
        }else{
            return false;
        }
    }

    function check_register_code()
    {
        $ali_open = C('ali_open_start');
        if(!$ali_open){
            //直接注册用户  无需验证
            return true;
        }else{
            $mobile    = $_REQUEST['mobile'];
            $checkCode = S($mobile);
            if($checkCode == I('post.code'))
                return true;
            else
                return false;
        }
    }

    public function register()
    {
        $user = $this->_modelAdd(1);
        if($user){
            $last_id = $user['last_id'];
            $password = md5Encrypt(trim(I('post.password')), $last_id);
            $this->where("id={$last_id}")->save(array('password'=>$password));
            $user  = $this->_modelFind("id={$last_id}");
            //手动登陆 并设置缓存
            $this->setUserCache($user);
            return true;
        }else{
            return false;
        }
    }

    public function setUserCache($user,$is_forever=0)
    {
        $token = 'login_'.md5($user['mobile']);
        if($is_forever){
            cookie('USER_INFO_TICKET',$token,3600*24*60);
            S($token,$user,3600*24*61);
        }else{
            //将用户信息缓存一天
            cookie('USER_INFO_TICKET',$token,3600*24);
            S($token,$user,3600*24);
        }

        /* 设置记录行为 */
        set_history($user['id'],1,"用户登陆了");
    }



    /**
     * 注销当前用户
     */
    public function  logout()
    {
        $token = cookie('USER_INFO_TICKET');
        cookie('USER_INFO_TICKET',null);
        S($token,null);
        //删除购物车 或者其他等等
        #to do what
    }

    /**
     * alogin　登录操作
     * @reutrn string
     * @author kevin.liu
     **/
    public function login()
    {
        $userWhere = array('mobile' => trim(I('post.mobile')),);
        $user  = $this->_modelFind($userWhere);
        if($user){
            $password = md5Encrypt(trim(I('post.password')), $user['id']);
            if($password != $user['password']){
                $this->error = "密码输入有误！";
                return false;
            }
            if($user['status'] != 1){
                $this->error = "该用户已被禁止！";
                return false;
            }
            $lastData = array(
                'last_time' => time(),
                'last_ip'   => get_client_ip(),
                'login_num' => $user['login_num']+1
            );
            $this->where("id={$user['id']}")->save($lastData);
            $frever = empty(I('post.frever')) ? 0 : I('post.frever');
            //先去除目前的缓存
            $this->logout();
            //设置新用户缓存
            $this->setUserCache($user,$frever);
            return true;
        }else{
            if(empty($this->getError())){
                $this->error = '该用户不存在！';
            }
            return false;
        }
    }

}