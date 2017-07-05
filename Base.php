<?php
//命名空间
//因为我们要在别的地方调用这里的类，所以我们要命名空间名
namespace houdunwang\model;
//使用空间命名
//我们要借调别的地方的类或者方法时，借助这个就可以调用的到了
use PDOException;
//使用空间命名
//我们要借调别的地方的类或者方法时，借助这个就可以调用的到了
use PDO;
class Base{
    //设置默认的静态属性
    //我们在静态调用的时候用着个变量来接收这个值。
    private static $pdo = NULL;
    //设置一个属性
    //用来在下面接收值
    private $table;
    //设置一个默认的空属性
    //在下面我们要用着个空属性接收where的值，这样我们就可以设置成mysql里面的where，之后我们就可以在PHP里面使用mysql里面where的功能了。
    private $where = '';
    //构造方法
    //在new一个新对象的时候，我们要先执行这个构造方法
    public function __construct($config,$table) {
        //调用connect里面的$config属性
        //我们要先输出这个属性
        $this->connect($config);
        //调用
        //接收的新数据和$table里面已存的值相比对
        $this->table = $table;
    }

    /**
     * 连接数据库
     */
    private function connect($config){
        //判断是否是连接到数据库
        //如果属性$pdo已经连接过数据库了，不需要重复连接了，如果没有连接数据库就之下面接收到的值
        if(!is_null(self::$pdo)) return;
        //执行里面的代码并接收错误
        //我们的数据库报错时，我们要接收到这个错误并输出出来，才可以看到。
        try{
            //
            $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'];
            $user = $config['db_user'];
            $password = $config['db_password'];
            $pdo = new PDO($dsn,$user,$password);
            //设置错误
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //设置字符集
            $pdo->query("SET NAMES " . $config['db_charset']);
            //存到静态属性中
            self::$pdo = $pdo;

        }catch (PDOException $e){
            exit($e->getMessage());
        }
    }

    public function where($where){
        $this->where = " WHERE {$where}";
        return $this;
    }

    /**
     * 获取全部数据
     */
    public function get(){

        $sql = "SELECT * FROM {$this->table} {$this->where}";
        return $this->q($sql);
    }

    public function find($pri){
        //获得主键字段，比如cid还是aid
        $priField = $this->getPri();
        $this->where("{$priField}={$pri}");
        $sql = "SELECT * FROM {$this->table} {$this->where}";
        $data = $this->q($sql);
        //把原来的二维数组变为一维数组
        $data = current($data);
        $this->data = $data;
        return $this;
    }

    public function findArray($pri){
        $obj = $this->find($pri);
        return $obj->data;
    }


    public function toArray(){
        return $this->data;
    }


    /**
     * 获得表的主键
     */
    public function getPri(){
        $desc = $this->q("DESC {$this->table}");
        //打印desc看结果调试
        //p($desc);
        $priField = '';
        foreach ($desc as $v){
            if($v['Key'] == 'PRI'){
                $priField = $v['Field'];
                break;
            }
        }
        return $priField;
    }

    public function count($field='*'){
        $sql = "SELECT count({$field}) as c FROM {$this->table} {$this->where}";
        $data = $this->q($sql);
        return $data[0]['c'];
    }


    public function q($sql){
        try{
            $result = self::$pdo->query($sql);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }catch (PDOException $e){
            exit($e->getMessage());
        }

    }
    /*
     * 执行无结果集操作
     */
    public function e($sql){
        try{
        return self::$pdo->exec($sql);

    }catch(PDOException $e){
        exit($e->getMessage());
    }
}

}