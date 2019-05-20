<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/20
 * Time: 10:55 AM
 */
namespace Yxx\Kindeditor\Facades;

use Illuminate\Support\Facades\Facade;
class Editor extends Facade
{
    protected static function getFacadeAccessor()
    {

        return 'editor';
    }
}