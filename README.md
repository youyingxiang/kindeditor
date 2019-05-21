# kindeditor与fileupload上传组件
- 1: git clone https://github.com/laravel/laravel.git  (安装laravel)
- 2: composer require yxx/kindeditor (安装富文本编辑器拓展)
- 3: 如果没有安装laravel-admin,参考一下网址操作 -**[laravel-admin](https://laravel-admin.org/docs/zh/installation)**
- 4: 运行 php artisan vendor:publish --provider="Yxx\Kindeditor\EditorProvider"
- 5: php artisan storage:link  建立软连接

## 项目中使用kindeditor编辑器
  $form->kindeditor('content', '内容');
## 项目中使用fileupload单图片上传
  $form->fileupload('img', '图像'); 
  <p align="center"><img src="https://www.zkteco.com/en/uploads/image/20190521/c9dc8d9f4503c979d2f010e5d491135a.jpg"></p>
# 配置文件
config/editor.php



        'route' => [
    
            'prefix' => env('ADMIN_ROUTE_PREFIX', 'yxx'),
    
            'namespace' => 'Yxx\\Kindeditor\\Controllers',
    
            'middleware' => ['web'],
        ],
    
        'up_config' => [
            "image_size"    => 1024*1024*3,                                                 // 上传图片大小
            "file_size"     => 1024*1024*100,                                               // 上传文件大小
            "media_size"    => 1024*1024*100,                                               // 上传视音频大小
            "flash_size"    => 1024*1024*100,                                               // 上传flash大小
            "image_format"  => "jpg,gif,jpeg,png,bmp,svg",                                  // 上传图片格式
            "file_format"   => "doc,docx,xls,xlsx,ppt,htm,html,txt,rar,zip,mp4,pdf,pptx",   // 上传文件格式
            "media_format"  => "mp3,mp4,avi",                                               // 上传视音频格式
            "flash_format"  => "swf,fla",                                                   // 上传flash格式
            "upload_path"   => "uploads",                                                   // 上传文件目录
            "show_domain"   => 1,                                                           // 是否显示带域名的完整路径                                                  // 上传文件目录
        ],



