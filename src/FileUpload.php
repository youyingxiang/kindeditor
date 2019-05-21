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
class FileUpload extends Field{

    // 视图地址
    protected $view = "kindeditor::fileupload";


    // js文件
    protected static $js = [
        '/vendor/kindeditor/fileupload/jquery.ui.widget.js',
        '/vendor/kindeditor/fileupload/jquery.iframe-transport.js',
        '/vendor/kindeditor/fileupload/jquery.fileupload.js',
    ];


    public function render()
    {
        $name = $this->formatName($this->column);
        $csrf_token = csrf_token();

        $this->script = <<<EOT
$(".file_img_up").fileupload({
        dataType: 'json',
        done: function (e, data) {
            if (data.result.error === 0) {
                var up_url = data.result.url.trim();
                obj.parent().prev().val(up_url);
                if (obj.prev().children('img').length>0) {
                    obj.prev().attr('href',up_url );
                    obj.prev().find('img').attr('src',up_url );
                }
            } else {
                  alert(data.result.message);
            }
        }
    });
    $(".up_img").on('click',function(){
        obj = $(this);
        obj.next().trigger('click');
    })
EOT;


        return parent::render();
    }



}