<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/17
 * Time: 8:44 AM
 */
namespace Yxx\Kindeditor;
use Encore\Admin\Form\Field;

/**
 * Class KindeEitor富文本编辑器
 * @package App\Extensions
 */
class KindEditor extends Field{

    // 视图地址
    protected $view = "kindeditor::kindeditor";

    protected static $css = [
        '/vendor/kindeditor/kindeditor/themes/default/default.css',
    ];

    // js文件
    protected static $js = [
        '/vendor/kindeditor/kindeditor/kindeditor-all.js',
        '/vendor/kindeditor/kindeditor/lang/zh-CN.js',
    ];


    public function render()
    {
        $name = $this->formatName($this->column);
        $upload_url = route("kindeditor.upload");
        $manage_url = route("kindeditor.upload");
        $delete_url = route("kindeditor.delete");
        $csrf_token = csrf_token();


        $this->script = <<<EOT
KindEditor.create('textarea[name="$name"]',{
        width : '100%',   //宽度
        height : '320px',   //高度
        resizeType : '0',   //禁止拖动
        allowFileManager : true,   //允许对上传图片进行管理
        uploadJson :   '$upload_url', //文件上传地址
        fileManagerJson : '$manage_url',   //文件管理地址
        deleteUrl  : '$delete_url', //文件删除地址
        //urlType : 'domain',   //带域名的路径
        extraFileUploadParams: {
                '_token':'$csrf_token'
        },
        formatUploadUrl: true,   //自动格式化上传后的URL
        autoHeightMode: false,   //开启自动高度模式
        afterBlur: function () { this.sync(); }   //同步编辑器数据
    });
EOT;


        return parent::render();
    }



}