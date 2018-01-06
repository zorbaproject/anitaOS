<?php
include("config.php");



class sqlDB {
    
    public $datatypes;
    private $usernamedb;
    private $passworddb;
    private $host;
    private $db;
    private $dbtype;
    
    private $opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    function __construct($user = "", $pass = "") {
        $this->datatypes = ["int","tinyint","smallint","mediumint","bigint","float","double","decimal","date","datetime","timestamp","time","year","char","varchar","blob","text","tinyblob","tinytext","mediumblob","mediumtext","longblob","longtext","enum"];
        //https://www.w3schools.com/sql/sql_datatypes.asp
        $this->dbtype = $GLOBALS['dbtype'];
        $this->host = $GLOBALS['hostname'];
        $this->charset = $GLOBALS['charset'];
        $this->usernamedb = $user;
        $this->passworddb = $pass;
        $this->db = "";
        //print_r(PDO::getAvailableDrivers());
    }
    
    function setuser($user){
        $this->usernamedb = $user;
    }
    
    function setpassword($pass){
        $this->passworddb = $pass;
    }
    
    function checkLogin(){
        
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";charset=".$this->charset;
            $result = array();
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $stmt = $pdo->query('SHOW DATABASES');
                while ($row = $stmt->fetch())
                {
                    $result[] = $row['Database'];
                }
            }  catch (Exception $e) {
                $result[] = 'ERROR: '.$e->getMessage();
            }
            return $result;
        }
        
    }
    
    function listDatabases(){
        
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";charset=".$this->charset;
            $result = array();
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $stmt = $pdo->query('SHOW DATABASES');
                while ($row = $stmt->fetch())
                {
                    $result[] = $row['Database'];
                }
            }  catch (Exception $e) {
                $result[] = 'ERROR: '.$e->getMessage();
            }
            return $result;
        }
        
    }
    
    function loadDB($dbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $result = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function listTables($dbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $stmt = $pdo->query('SHOW TABLES');
                $result = array();
                while ($row = $stmt->fetch())
                {
                    $result[] = $row['Tables_in_'.$dbname];
                }
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function tableExists($dbname, $table) {
        
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
            } catch (Exception $e) {
                // We got an exception == table not found
                return FALSE;
            }
            
            // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
            return TRUE;
        }
    }
    
    function selectFrom($dbname,$tbname, $where = "", $params = []) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                //$where = 'WHERE email = ? AND status=?';
                //$params = [$email, $status];
                $stmt = $pdo->prepare('SELECT * FROM '.$tbname.' '.$where);
                $stmt->execute($params);
                $result = $stmt->fetchAll();
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function deleteFrom($dbname,$tbname, $where = "", $params = []) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $stmt = $pdo->prepare('DELETE FROM '.$tbname.' '.$where);
                $stmt->execute($params);
                $result = $stmt->fetch();
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function insert($dbname,$tbname, $fields = []) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'REPLACE INTO '.$tbname.'(';
                foreach ($fields as $key => $value) {
                    $query = $query.$key.',';
                }
                $query = substr($query, 0, -1);
                $query = $query.') VALUES(';
                $params = [];
                foreach ($fields as $key => $value) {
                    $query = $query.'?,';
                    $params[] = $value;
                }
                $query = substr($query, 0, -1);
                $query = $query.') ';
                //print $query;
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $result = $stmt->fetch();
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function getStructure($dbname,$tbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $stmt = $pdo->query('DESCRIBE '.$tbname);
                $result = array();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($res as $column)
                {
                    $result[] = array($column['Field'], $column['Type']);
                }
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function createDB($dbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'CREATE DATABASE IF NOT EXISTS '.$dbname;
                $result = $pdo->query($query);
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function createTable($dbname, $tbname, $columns){
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'CREATE TABLE '.$tbname;
                if ($columns != "") $query = $query.' ( '.$columns.' ) ;';
                $pdo->query($query);
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function addColumn($dbname,$tbname,$colname,$coltype){
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'ALTER TABLE '.$tbname.' ADD '.$colname.' '.$coltype.';';//.' NOT NULL';
                $pdo->query($query);
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function removeColumn($dbname,$tbname,$colname){
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'ALTER TABLE '.$tbname.' DROP COLUMN '.$colname.';';
                $pdo->query($query);
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function updateColumn($dbname,$tbname,$oldcolname,$newcolname,$coltype){
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'ALTER TABLE '.$tbname.' CHANGE '.$oldcolname.' '.$newcolname.' '.$coltype.';';//.' NOT NULL';
                $pdo->query($query);
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    
    function deleteDB($dbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'DROP DATABASE IF EXISTS '.$dbname;
                $result = $pdo->query($query);
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    function deleteTable($dbname, $tbname) {
        if ($this->dbtype == "sqlite") {
            $myPDO = new PDO('sqlite:/home/example/books.db');
        }
        if ($this->dbtype == "mysql") {
            $dsn = "mysql:host=".$this->host.";dbname=".$dbname.";charset=".$this->charset;
            try {
                $pdo = new PDO($dsn, $this->usernamedb, $this->passworddb, $this->opt);
                $query = 'DROP TABLE IF EXISTS '.$tbname;
                $result = $pdo->query($query);
                return $result;
            }  catch (Exception $e) {
                $result = array();
                $result[] = 'ERROR: '.$e->getMessage();
                return $result;
            }
        }
    }
    
    
}

?>
