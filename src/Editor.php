<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/20
 * Time: 10:09 AM
 */
namespace Yxx\Kindeditor;
class Editor {
    /**
     * 注册路由
     */
    public function registerAuthRoutes()
    {

        $attributes = [
            'prefix'     => config('editor.route.prefix'),
            'middleware' => config('editor.route.middleware'),
        ];


        app('router')->group($attributes, function ($router) {

            /* @var \Illuminate\Routing\Router $router */
            $router->namespace('Yxx\Kindeditor\Controllers')->group(function ($router) {
                $router->any('kindeditor/upload','UploadController@upload')->name("kindeditor.upload");
            });
        });

    }

}