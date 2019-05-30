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
            ]);
        }
        app('editor')->registerAuthRoutes();

    }



    public function register()
    {
        $this->app->bind('editor', function ($app) {
            return new Editor();
        });
    }


}