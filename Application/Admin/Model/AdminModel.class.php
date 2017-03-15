<?php
namespace Admin\Model;
class AdminModel extends \Common\Model\PublicModel
{
    /**
     * $_validate 自动验证
     * @author kevin.liu
     * @time 2015-04-14
     **/
    protected $_validate = array(
        array('username', 'require', '帐号必须填写'),
        array('password', 'require', '密码必须填写'),
        array('name', 'require', '姓名必须填写'),
        array('email', 'require', '邮件必须填写'),
        array('email', 'email', '邮件格式错误'),
        array('phone', 'require', '电话必须填写'),
        array('sort', 'require', '排序方式必须填写'),
        array('sort', 'number', '排序只能是数字'),
        array('verify', 'checkcode', '验证码不正确', 0, 'function'),
    );

    protected $_auto = array(
        array('addtime', 'time', self::MODEL_INSERT, 'function'),
        array('add_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );


    /**
     * editUser 编辑用户
     * @author kevin.liu
     * @time 2015-08-13
     **/
    public function edit()
    {
        $this->startTrans();
        $data = $this->create();
        if(empty($data)){
            return false;
        }
        $group = I('post.group_id');
        if(empty($group)){
            $this -> error = '请选择所属分组';
            return false;
        }
        $groupArr = array();
        $char = randNum();
        if(empty($data['id'])){
            $data['password'] = md5Encrypt(trim($data['password']), $char);
            $where = array(
                'username' => trim($data['username']),
                'status'   => 1,
            );
            if($this->where($where)->getField('id')){
                $this->error = '该用户已经存在';
                return false;
            }
            $id = $this->add($data);
            if(!$id){
                $this->error = '添加失败';
                return false;
            }else{
                foreach ($group as $key => $value) {
                    $groupArr[$key]['uid'] = $id;
                    $groupArr[$key]['group_id'] = $value;
                }
                M('group_access')->addAll($groupArr);
                $charData = array(
                    'chars' => $char,
                    'id'    => $id,
                    'type'  => 0
                );
                $chars = M('char')->add($charData);
                if(!$chars){
                    $this->rollback();
                    $this->error = '添加失败';
                    return false;
                }
                $this->commit();
            }
        }else{
            $where = array(
                'uid' => $data['id']
            );
            M('group_access')->where($where)->delete();
            $groupArr = array();
            foreach ($group as $key => $value) {
                $groupArr[$key]['uid'] = $data['id'];
                $groupArr[$key]['group_id'] = $value;
            }

            M('group_access')->addAll($groupArr);
            $regpassword = trim(I('post.regpassword'));
            if($regpassword != ''){
                $data['password'] = md5Encrypt($regpassword, $char);
            }
            $res = $this->save($data);
            if($res === false){
                $this->rollback();
                $this->error = '更新失败';
                return false;
            }
            if(!empty($regpassword)){
                $where = array(
                    'id' => $data['id']
                );
                $char = M('char')->where($where)->setField('chars', $char);
                if($char === false){
                    $this->rollback();
                    $this->error = '更新失败';
                    return false;
                }
            }
            $this->commit();
        }

        return $data;
    }

    public function delUserInfo($id = 0)
    {
        $res = $this->del($id);
        if(!$res){
            $this->rollback();
            return false;
        }
        $res = $this->del($id, 'admin_info');
        if(!$res){
            $this->rollback();
            return false;
        }
        return true;
    }

    /**
     * alogin　登录操作
     * @reutrn string
     * @author kevin.liu
     * @time 2015-06-07
     **/
    public function login()
    {
        //6da8b75d3ffb1f70805d374f63252bc4
        $data = $this->create($_POST, 2);
        if(empty($data)){
            return false;
        }
        $userWhere = array(
            'a.username' => trim($data['username']),
            'a.status'   => 1,
            'c.type'     => 0
        );
        $chars = M()
            ->table('__ADMIN__ a')
            ->join('LEFT JOIN __CHAR__ c ON a.id=c.id')
            ->where($userWhere)
            ->getField('chars');
        if(!$chars){
            $this->error = '登陆出错,用户名或者密码错误';
            return false;
        }
        $where = array(
            'username' => $data['username'],
            'password' => md5Encrypt(trim($data['password']), $chars),
            'status'   => 1
        );
        $res = $this->field('id,username,status,name')->where($where)->find();
        if($res){
            $lastData = array(
                'last_time' => time(),
                'last_ip'   => get_client_ip()
            );
            $this->where($where)->save($lastData);
            session(C('ADMIN_UID'), $res['id']);
            session(C('USERNAME'), $res['name']);
            return $res;
        }else{
            $this->error = '用户名或者密码错误';
            return false;
        }
    }

}