<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/20
 * Time: 10:29 AM
 */
namespace Yxx\Kindeditor\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller {
    protected $file_move_path;     //上传文件移动服务器位置
    protected $file_back_path;     //上传文件返回文件地址
    protected $up_type;            //上传类型

    protected $root_path;          //根目录路径
    protected $root_url;           //根目录URL
    protected $order;              //文件排序

    protected $default_config = [
        "image_size"    => 1024*1024*3,                                                 // 上传图片大小
        "file_size"     => 1024*1024*100,                                               // 上传文件大小
        "media_size"    => 1024*1024*100,                                               // 上传视音频大小
        "flash_size"    => 1024*1024*100,                                               // 上传flash大小
        "image_format"  => "jpg,gif,jpeg,png,bmp,svg",                                  // 上传图片格式
        "file_format"   => "doc,docx,xls,xlsx,ppt,htm,html,txt,rar,zip,mp4,pdf,pptx",   // 上传文件格式
        "media_format"  => "mp3,mp4,avi",                                               // 上传视音频格式
        "flash_format"  => "swf,fla",                                                   // 上传flash格式
        "upload_path"   => "uploads",                                                   // 上传文件目录
    ];


    protected $config;


    public function __construct(Request $request)
    {
        $type = $request->input('dir','image');
        $this->setUpType($type);
        $this->setUpConfig((array)config('editor.up_config'));
        $this->setFileMovePath();
        $this->setOrder($request->input('order','name'));

    }

    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('imgFile') && $request->file('imgFile')->isValid()) {
                $imgfile   = $request->imgFile;
                $this->check($imgfile);
                $path = Storage::putFile($this->file_move_path, $imgfile);
                $path = Storage::url($path);
                return $this->ajaxReturn("",asset($path));
            } else {
                abort(401, '请选择文件');
            }

        } catch (\Exception $e) {
            return $this->ajaxReturn($e->getMessage());
        }
    }

    public function check($imgFile) {
        $size = $imgFile->getSize();
        $ext  = $imgFile->getClientOriginalExtension()?:$imgFile->extension();


        $size_name      = $this->up_type."_size";
        $ext_name       = $this->up_type."_format";
        $config         = $this->getUpConfig();
        $imgfile_size   = $config[$size_name];
        $imgFile_format = explode(",",$config[$ext_name]);


        if ($imgfile_size < $size)
            throw new \Exception("上传文件过大！");
        if (!in_array($ext,$imgFile_format))
            throw new \Exception("上传文件格式不正确！");
    }

    /**
     * @param $type
     * 获取上传的是图片还是文件还是视频
     * 根据类型创建不同文件目录
     */
    public function setUpType(string $type):void
    {
        $this->up_type = trim($type);
    }

    public function getUpType():string
    {
        return $this->up_type;
    }

    /**
     * @param array $config
     * 设置上传参数设置信息 没有用默认的 可以从文件 数据库读取
     */
    public function setUpConfig(array $config = []):void
    {
        $this->config =  $config ?: $this->default_config;
    }

    public function getUpConfig():array
    {
        return $this->config;
    }

    public function getDir()
    {
        return $this->up_type.DIRECTORY_SEPARATOR.date("Y").DIRECTORY_SEPARATOR.date("m").DIRECTORY_SEPARATOR.date("d");
    }

    public function setFileMovePath():void
    {
        $this->file_move_path = DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR.$this->config['upload_path'].DIRECTORY_SEPARATOR .$this->getDir();

    }

    public function getFileMovePath():string
    {
        return $this->file_move_path;
    }





    public function setOrder(string $order):void
    {
        $this->order = strtolower($order);
    }

    public function getOrder(): string
    {
        return $this->order;
    }



    /**
     * @Title: ajaxReturn
     * @Description: todo(ajax提交返回状态信息)
     * @param string $info
     * @param url $url
     * @param string $status
     * @author yxx
     * @date 2016-5-12
     */
    public function ajaxReturn($message='', $url='', $error='', $data = '')
    {
        if(!empty($url)){   //操作成功
            $result = array( 'message' => '操作成功', 'error' => 0, 'url' => $url, );
        }else{   //操作失败
            $result = array( 'message' => '操作失败', 'error' => 1, 'url' => '', );
        }
        if(!empty($message)){$result['message'] = $message;}
        if(!empty($error)){$result['error'] = $error;}
        if(!empty($data)){$result['data'] = $data;}
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit();
    }







}