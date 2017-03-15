<?php
namespace Api\Model;
use Think\Model;
class UserModel extends model
{
    /**
     * 登录操作
     * @author 普修米洛斯
     **/
    public function login($name,$password,$os,$osversion,$machinecode)
    {
        $where = array(
            'email' => $name,
            'password' => $password,
            'status' => 1
        );
        $res = $this->where($where)->find();
        if($res['status']){
            $where = array(
                'id'=>$res['id'],
                'status' => 1
            );
            $data = array(
                'lastip'            => get_client_ip(),
                'lasttime'      => time(),
                'os'            => $os,
                'osversion'     => $osversion,
                'machinecode'   => $machinecode
            );
            $this->where($where)->save($data);
        }
        return $res;
    }

    /**
     * 检测用户是否登录
     * @author 普修米洛斯
     **/
    public function isReg($user)
    {
        $where = array(
            'email'    => $user,
            'status' => 1,
        );
        $res = $this->where($where)->getField('id');
        return $res;
    }

}