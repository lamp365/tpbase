<?php
namespace Common\Controller;

use Think\Controller;

class BaseController extends Controller
{

    public function getOption($key, $subKey=false)
    {
        static $_option = array();
        if(!isset($_option[$key])){
            $optionModel = M('kv');
            $where = array(
                'key'   => $key
            );
            $res = $optionModel->where($where)->find();

            if(!$res){
                return false;
            }
            $val    = $res['value'];                                                           
            $type   = $res['type'];
            switch($type){
                case 1 :
                    $_option[$key] = json_decode($val, true);
                    break;
                case 2 :
                    $_option[$key] = unserialize($val);
                    break;
                default :
                    $_option[$key] = $val;
                    break;
            }
        }
        $res = $_option[$key];
        if($subKey != false && is_string($subKey)){
            if(isset($res[$subKey])){
                return $res[$subKey];
            }else{
                return false;
            }
        }
        return $_option[$key];
    }

    public function setOption($key, $val='', $type=0, $name='')
    {
        $optionModel = M('kv');
        $res = $this->getOption($key);
        switch ($type) {
            case 1:
                $val = json_encode($val);
                break;
            case 2 :
                $val = serialize($val);
                break;
        }
        $data = array(
            'key'   => $key,
            'value' => $val,
            'type'  => $type,
            'name'  => $name
        );
        if($res == false){
            $res = $optionModel->add($data);
        }else{
            $where = array(
                'key'   => $key
            );
            $res = $optionModel->where($where)->save($data);
        }
        return $res;
    }

    public function removeOption($key)
    {
        $optionModel = M('kv');
        $where = array(
            'key'   => $key
        );
        return $optionModel->where($where)->delete();
    }

    public function getJumpId($url)
    {
        $key = md5($url);
        $where = array(
            'key' => $key
        );
        $id = M('jump')->where($where)->getField('id');
        if(!$id){
            $data = array(
                'key' => $key,
                'url' => urlencode($url)
            );
            $id = M('jump')->add($data);
        }
        return $id;
    }

    public function getJumpUrl($id=0)
    {
        if(intval($id) == 0){
            return '';
        }
        $where = array(
            'id'    => $id
        );
        $url = M('jump')->where($where)->getField("url");
        if($url){
            return urldecode($url);
        }else{
            return '';
        }
    }

    public function makeQrCode($data, $path=null)
    {
        import('Common.Lib.phpqrcode.phpqrcode', '', '.php');//引入工具包
        $path = $path ? $path : "./Uploads/qrcode/";
        $path = rtrim($path, '/'). '/';
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }
        $filename = $path.time().mt_rand(1000, 9999).'.png';
        if($data == false){
            return false;
        }
        \QRcode::png($data, $filename, 'L', 10, 2);
        $QR = imagecreatefromstring(file_get_contents($filename));
        imagepng($QR, $filename);
        return ltrim($filename, '.');
    }
    // 600*356  318*200
    public function makePic($res="", $width="", $height="")
    {
        $res = '.'.$res;
        if(empty($res) || empty($width) || empty($height)){
            return false;
        }

        $_tmp       = explode('/', $res);
        $fileName   = array_pop($_tmp);
        $__tmp      = explode('.', $fileName);
        $ext        = array_pop($__tmp);
        $pathName   = implode('/', $_tmp);

        if(strpos($width, ',')){
            $widthArr   = explode(',', trim($width, ','));
            $heightArr  = explode(',', trim($height, ','));
        }else{
            $widthArr   = array($width);
            $heightArr  = array($height);
        }
        if( empty($widthArr)
            || empty($heightArr)
            || (count($widthArr)!= count($heightArr)) ){
            return false;
        }
        foreach ($widthArr as $key => $value) {
            $w = intval($value);
            $h = intval($heightArr[$key]);
            $thumbFileName = "{$pathName}/thumb/{$w}x{$h}/{$fileName}";

            if(file_exists($thumbFileName)){
                @unlink($thumbFileName);
            }

            $image = new \Think\Image();
            $image->open($res);
            if(!is_dir(dirname($thumbFileName))){
                mkdir(dirname($thumbFileName), 0755, true);
            }

            $image->thumb($w, $h, 3)-> save($thumbFileName, $ext, 100);
            $waterMarkImg = C('WATER_MARK_IMG');
            $warerMarkPos = C('WATER_MARK_POS');
            if(!is_array($warerMarkPos)){
                $warerMarkPos = array(9);
            }
            foreach ($warerMarkPos as $value) {
                $r = $image->open($thumbFileName)->water($waterMarkImg, $value)->save($thumbFileName);
                if(!$r){
                    return false;
                }
            }
        }
        return true;
    }
}