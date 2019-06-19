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
    protected $ds = "/";
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
        // "show_domain"   => 1,                                                           // 是否显示带域名的完整路径
    ];
    protected $config;
    public function __construct(Request $request)
    {
        $type = $request->input('dir','image');
        $this->setUpType($type);
        $this->setUpConfig((array)config('editor.up_config'));
        $this->setFileMovePath();
        $this->setOrder($request->input('order','name'));
        $this->setRootPath("public");
    }
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('imgFile') && $request->file('imgFile')->isValid()) {
                $imgfile   = $request->imgFile;
                $this->check($imgfile);
                $path = Storage::disk('public')->putFile($this->file_move_path, $imgfile);
                $path = Storage::disk('public')->url($path);
                return $this->ajaxReturn("",$path);
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
     * @param string $path 目录名称
     */
    public function checkDirName(string $path = ''):void
    {
        switch (true) {
            case empty($path):
                if (!in_array($this->getUpType(), ['', 'image', 'flash', 'media', 'file'])) {
                    abort(404, "无效的目录名！");
                }
                break;
            case preg_match('/\.\./', $path):
                abort(403, "不允许访问！");
                break;
            case !preg_match('/[\/|\\\]$/', $path):
                abort(400, "目录参数不正确");
                break;
            case !file_exists($path) || !is_dir($path):
                abort(404, "目录不存在！");
                break;
            default:
                break;
        }
    }
    /**
     * @des 遍历目录取得文件信息
     * @param $path 目录名称
     * @return array
     */
    public function getDirFileList($path):array
    {
        $file_list = [];
        $ext_name = $this->getUpType()."_format";
        $ext_arr  = explode(',', $this->getUpConfig()[$ext_name]);
        if ($handle = opendir($path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') continue;
                $file = $path.$filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir']   = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir']   = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        return $file_list;
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
        return $this->up_type.$this->ds.date("Y").$this->ds.date("m").$this->ds.date("d");
    }
    public function setFileMovePath():void
    {
        $this->file_move_path = $this->ds.$this->config['upload_path'].$this->ds.$this->getDir();
    }
    public function getFileMovePath():string
    {
        return $this->file_move_path;
    }
    /**
     *上传图片文件管理
     */
    /**
     *上传图片文件管理
     */
    public function manager()
    {
        $up = $this->getUpType();
        try {
            $this->checkDirName();
            if ($up !== '') {
                if (!file_exists($this->getRootPath())) {
                    mkdir($this->root_path);
                }
                $path = request()->get('path');
                if (empty($path)) {
                    $data = [
                        'current_path'     => realpath($this->getRootPath()) . $this->ds,
                        'current_url'      => "",
                        'current_dir_path' => "",
                        'moveup_dir_path'  => "",
                    ];
                } else {
                    $data = [
                        'current_path'     => realpath($this->getRootPath()) . $this->ds . $path. $this->ds,
                        'current_url'      => Storage::disk('public')->url($this->config['upload_path'].$this->ds.$this->getUpType().$this->ds.$path),
                        'current_dir_path' => $path,
                        'moveup_dir_path'  => preg_replace('/(.*?)[^\/]+\/$/', '$1', $path),
                    ];
                }
                $this->checkDirName($data['current_path']);
                $file_list = $this->getDirFileList($data['current_path']);
                $file_list = $this->_order_func($file_list, $this->getOrder());
                $data['file_list'] = $file_list;    //文件列表数组
                return json_encode($data);          //输出JSON字符串
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
    public function _order_func(&$file_list, $sort_key, $sort = SORT_ASC)
    {
        if ($sort_key == 'type') {
            $sort_key = 'filetype';
        } else if ($sort_key == 'size') {
            $sort_key = 'filesize';
        } else {   //name
            $sort_key = 'filename';
        }
        if (is_array($file_list)) {
            foreach ($file_list as $key => $row_array) {
                $num[$key] = $row_array[$sort_key];
            }
        } else {
            return false;
        }
        //对多个数组或多维数组进行排序
        array_multisort($num, $sort, $file_list);
        return $file_list;
    }
    public function delete()
    {
        $data    = request()->post();
        $del_url = preg_replace("/[\/|\\\]storage/",'',$data['del_url']);
        if ($data['dir'] == 'dir') {
            $del_res = Storage::disk('public')->deleteDirectory($del_url);
        } else if ($data['dir'] == 'file') {
            $del_res = Storage::disk('public')->delete($del_url);
        }
        if ($del_res) {   //检测是否删除
            $res = [
                'msg'  => '文件删除成功',
                'code' => 200,
            ];
        } else {
            $res = [
                'msg'  => '文件删除失败',
                'code' => 400,
            ];
        }
        return json_encode($res);
    }
    public function setOrder(string $order):void
    {
        $this->order = strtolower($order);
    }
    public function getOrder(): string
    {
        return $this->order;
    }
    public function setRootPath(string $disk):void
    {
        $this->root_path = Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix().$this->config['upload_path'].$this->ds.$this->getUpType().$this->ds;
    }
    public function getRootPath():string
    {
        return $this->root_path;
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