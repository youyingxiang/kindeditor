<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/20
 * Time: 10:52 AM
 */
namespace Yxx\Kindeditor;
use Illuminate\Support\ServiceProvider;
use Encore\Admin\Form;
use Encore\Admin\Admin;
class EditorProvider extends ServiceProvider
{
    public function  boot()
    {
        if ($this->app->runningInConsole()) {
            // 发布配置文件
            $this->publishes([
                __DIR__.'/config/editor.php' => config_path('editor.php'),
                __DIR__.'/../resources/assets' => public_path('vendor/kindeditor')
            ]);
        }
        app('editor')->registerAuthRoutes();
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'kindeditor');

        Admin::booting(function () {
            Form::extend('kindeditor', KindEditor::class);
            Form::extend('fileupload', FileUpload::class);
        });
    }



    public function register()
    {
        $this->app->bind('editor', function ($app) {
            return new Editor();
        });
    }


}