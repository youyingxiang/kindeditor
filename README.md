# kindeditor
- 1: git clone https://github.com/laravel/laravel.git(安装laravel)
- 2: composer update (安装依赖包)
- 3: composer require yxx/kindeditor (安装kindeditor富文本编辑器拓展)
- 4: 如果没有安装laravel-admin,参考一下网址操作 -**[laravel-admin](https://laravel-admin.org/docs/zh/installation)**
- 5: 项目config/app.confg  
  - 'providers' 添加 Yxx\Kindeditor\EditorProvider::class
  - 'aliases'   添加 'Editor' => Yxx\Kindeditor\Facades\Editor::class
- 6: 运行 php artisan vendor:publish --provider="Yxx\Kindeditor\EditorProvider"
- 7: php artisan storage:link  建立软连接

# 项目中使用kindeditor编辑器
  $form->kindeditor('content', '内容');
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
        "image_format"  => "jpg,gif,jpeg,png,bmp,svg",                                  // 上传图片格式
        "file_format"   => "doc,docx,xls,xlsx,ppt,htm,html,txt,rar,zip,mp4,pdf,pptx",   // 上传文件格式
        "upload_path"   => "uploads",                                                   // 上传文件目录
    ],



