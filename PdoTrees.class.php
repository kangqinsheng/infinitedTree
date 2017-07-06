<?php
class PdoTrees
{
    private $pdo;
    private $dbname="treedata";
    private $table="tree";
    public function __construct($dsn,$user,$pwd,$options=array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION))
    {
        try {
            $this->pdo = new PDO($dsn, $user, $pwd, $options);
            $this->pdo->query("SET NAMES UTF8");
            //创建初始数据库
            $creatDatabaseSql = "CREATE DATABASE IF NOT EXISTS {$this->dbname}";
            if(!$this->pdo->query($creatDatabaseSql)){
                die("数据库创建失败");
            }else{
                $this->pdo=new PDO($dsn."dbname={$this->dbname};",$user, $pwd, $options);
            }
            //创建数据表
$createTable=<<<SQL
                  CREATE TABLE IF NOT EXISTS `{$this->table}` ( 
                  `id` int(5) PRIMARY KEY AUTO_INCREMENT, 
                  `name` varchar(40) NOT NULL, 
                  `pid` int(5) NOT NULL 
                 ) DEFAULT CHARSET=utf8;
SQL;
            $this->pdo->query($createTable);
        }catch (PDOException $e){
            die($e->getMessage());
        }
    }

    /**
     * 添加分类
     */
    public function addCates($name,$pid){
        //先判断是否存在
        $sqlExt = "SELECT * FROM {$this->table} WHERE `name`=? and `pid`=?";
        $pre = $this->pdo->prepare($sqlExt);
        $pre->execute(array($name,$pid));
        if($pre->rowCount()){
            die("请不要添加重复数据");
        }
        $sql = "INSERT INTO {$this->table}(`name`,`pid`) VALUES (?,?)";
        try{
            $pre = $this->pdo->prepare($sql);
            $res = $pre->execute(array($name,$pid));
        }catch (PDOException $e){
            die($e->getMessage());
        }
    }

    /**
     * 获取所有分类
     */
    public function getCates($pid=0,&$data=array(),$level=0)
    {
        $level = $level+1;
        $sql = "SELECT * FROM {$this->table} WHERE `pid`=?";
        $pre = $this->pdo->prepare($sql);
        $res = $pre->execute(array($pid));
        while ($row = $pre->fetch(PDO::FETCH_ASSOC)){
            $row["name"] = str_repeat("&nbsp;&nbsp;",$level).$row["name"];
            $data[]=$row;
            $this->getCates($row["id"],$data,$level);
        }
        return $data;
    }
}