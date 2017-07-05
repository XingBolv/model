<?php
//命名空间
//我们在这里写出的命名空间的地址和摸一个命名空间的名字一致时我们就可以调用到别的地方的方法或者类。
namespace houdunwang\model;
class Model{
    //设置静态属性
    //我们用这个属性在下面在下面接收值
    private static $config;
    //在我们调用不到摸个方法时就会触发这个类
    //为了可以使用new对象来调用方法
    public function __call( $name, $arguments ) {
        //获得当前命名空间的路径
        //为了能在么某个文件里接收到article这个类
        return self::parseAction($name,$arguments);
    }
    //在我们调用不到某个静态类或者方法时就会触发此类
    //为了方便我们调用的是后调空或者路径错误
    public static function __callStatic( $name, $arguments ) {
        //获得当前命名空间的路径
        //为了能在么某个文件里接收到article这个类
        return self::parseAction($name,$arguments);
    }
    //
    //
    private static function parseAction($name, $arguments ){
        //system\model\Article
        $table = get_called_class();
        $table = strtolower(ltrim(strrchr($table,'\\'),'\\'));
        return call_user_func_array([new Base(self::$config,$table),$name],$arguments);
    }


    public static function setConfig($config){
        //调用config属性并把传过来的值赋值给他
        //为了可以在别的地方可以调用的到
        self::$config = $config;
    }
}