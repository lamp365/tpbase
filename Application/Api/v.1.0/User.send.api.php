<?php
$phone = I('post.phone');
if (!check_phone($phone)) {
    $this->error(401);
}
$type = I('type', 1, 'intval');
switch ($type) {
    case 1:
    case 2:
        break;
    default:
        $this->error(402);
        break;
}
$where = array(
    'phone'    => $phone,
    'use_time' => 0,
);
$code_model = M('codes');
$info = $code_model->where($where)->find();
$rand_code = mt_rand(00000, 99999);
//检测该手机号码是否存在并且尚未使用
if ($info) {
    $data = array(
        'code' => $rand_code,
        'num'  => $info['num']+1,
        'type' => $type
    );
    $res = $code_model->where($where)->save($data);
} else {
    $data = array(
        'code'    => $rand_code,
        'addtime' => time(),
        'num'     => 1,
        'phone'   => $phone,
        'type'    => $type
    );
    $res = $code_model->add($data);
}
if(!$res){
    $this -> error(403);
}
//此处调用短信发送方法
$res = array();
$field = C('API_FIELD_DESC');
$this->success($res='验证码发送成功', $field[1]);