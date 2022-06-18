<?php 
namespace pdo;
// use Aws\Api\Service;
use sqlAtomax;
use sql;
use \PDO;
use \PDOException;
use \PDOStatement;
use RT;
use service\my;
include_once __ROOT__.'/apps/namespace/all.php';
include_once( __ROOT__ .'/var.php' );

class connect 
    {
        protected const ATOMAX_MY_IP = sqlAtomax\IP;
        protected const ATOMAX_MY_PORT = sqlAtomax\PORT;
        protected const ATOMAX_MY_DB_NAME = sqlAtomax\DB_NAME;
        protected const ATOMAX_MY_NAME = sqlAtomax\NAME;
        protected const ATOMAX_MY_PASS = sqlAtomax\PASS;
        protected const MY_IP = sql\IP;
        protected const MY_PORT = sql\PORT;
        protected const MY_DB_NAME = sql\DB_NAME;
        protected const MY_NAME = sql\NAME;
        protected const MY_PASS = sql\PASS;
        public $atomAx;
        protected $_atomAx;
        protected $_password;
        protected $_connection;
        protected $_connect;
        protected $__name;
        protected $_dsn;
        protected $_options;  
        protected $q;
        protected $_version;
        protected $_attribute;


        public function __construct()
        {
            $this->_config=[];
        }


        protected function my_choice_db($atomAx = false):array
        {
            
            $this->_atomAx = $atomAx;
            try 
            {
                if ($this->_atomAx == true) 
                {
                    $this->_config=[
                        'dsn' => 'mysql:host='.self::ATOMAX_MY_IP.':'.self::ATOMAX_MY_PORT.';dbname='.self::ATOMAX_MY_DB_NAME,
                        'name' => self::ATOMAX_MY_NAME,
                        'password' => self::ATOMAX_MY_PASS,
                        'options' => [
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                        ]
                    ];
                }
                else if($this->_atomAx == false)
                {
                    $this->_config=[
                        'dsn' => 'mysql:host='.self::MY_IP.':'.self::MY_PORT.';dbname='.self::MY_DB_NAME,
                        'name' => self::MY_NAME,
                        'password' => self::MY_PASS,
                        'options' => [
                            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'  //utf8mb4
                        ]
                    ];
                } 
                else
                {
                return false;
                }
                    if (empty($this->_config['name']) && !empty($this->_config['password'])) 
                    {
                        throw new \InvalidArgumentException(
                            'Пожалуйста, укажите «имя пользователя» вместо «пароль» для подключения к базе данных'
                        );
                        return false;
                    }
                } 
                finally
                {
                return $this->_config?: false ;
            }
        }

    
        public function my_db_connect($atomAx = false):mixed
         // public function my_db_connect($atomAx = false) 
        {   
            $this->_atomAx = $atomAx;
            $this->my_choice_db($this->_atomAx);
            $this->_config = self::my_choice_db();

            try
            {

                $dbh = new PDO(
                    $this->_config['dsn'],
                    $this->_config['name'] ?: null,
                    $this->_config['password'] ?: null,
                    $this->_config['options']
                );
               
                $this->_attributeSet=[
                    [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                    [PDO::ATTR_PERSISTENT, PDO::ERRMODE_EXCEPTION]
                ];
                
                foreach($this->_attributeSet as $atN => $att) 
                {
                    $dbh->setAttribute($att[0],$att[1]);
                }
                $this->_attributeGet=[
                    PDO::ATTR_SERVER_VERSION
                ];
                foreach($this->_attributeGet as $atN => $att)
                { 
                    $dbh->getAttribute($att);
                }       
            }
            catch(PDOException $e) 
            {
                $this->name = 'my_db_connect';
                $this->e=$e;
                if (!isset($dbh))  die( 'Нет соединения с базой ');
                $this->my_error_return($this->e, $this->name, $dbh);
            }
         return $dbh?:(bool)false;
        }

        public function my_update(string $q, array $var,bool $atomAx=false)
        { 
            $this->_connect = $this->my_db_connect($this->_atomAx) ;
            if(!$this->_connect) return false;      
            //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 
            // шаблон запроса
          
            // $today = date('Y-m-d H:i:s');
            // $name = 'Иван  Ивановй';
            // $adres = 'Саратов';
            // $data = '2022-01-20 19:50:33';
            
            //     $set = '`data`= :data, `name`= :name, `adres`= :adres';
            //     // $d = date("Y-m-d h:i:s");
            // $var= [ 
            //         [':data_', $data],
            //         [':data',$today],
            //         [':name',$name],
            //         [':adres',$adres]
            //       ];
                  
            // $db ='test';
            // $nt = 'test';
            // $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE `data`= :data_';
            
            // $pdo_Connect->my_update($q,$var);

            if (!is_bool($atomAx) ) return false;
                    $this->_atomAx = $atomAx;
            try
            {
                // $this->my_db_connect($this->_atomAx)->beginTransaction();

                $s = $this->_connect->prepare($q);

                foreach( $var as $varN => $myVal) 
                {    
                    $this->param = $this->my_pdo_type($myVal[1]);
                    $this->length = strlen($myVal[1])?:'';
                    $s->bindParam($myVal[0], $myVal[1], $this->param?:PDO::PARAM_INPUT_OUTPUT, $this->length);
                }
                $s->execute();

            }
            catch(PDOException $e) 
            { 
                // $this->_connect->rollback();
                $this->name = 'my_update';
                $this->e=$e;
                if (!isset($s))  die( 'Нет соединения с базой ');
                $this->my_error_return($this->e, $this->name,$s);
            }
            finally
            {
                // $this->_connect->commit();
                $s->closeCursor();   
            }
        }
 
        public function my_insert( string $i, string $db, string $nt, array $var,bool $bool = false, bool $atomAx = false):mixed
        {
            $this->_connect = $this->my_db_connect($this->_atomAx) ;
            if(!$this->_connect) return false;    
             //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 
           // шаблон запроса
                    //   (bool) true;//етретим значение  возвращает шв строки последней
                    // $today = date('Y-m-d H:i:s');
                    // $name = 'Иван Ивановй';
                    // $adres = 'Саратов';
            
            
                    //     $var= [ 
                    //         [':data',$today],
                    //         [':name',$name],
                    //         [':adres',$adres]
                    //     ];     
                    //     $set = '(`data`, `name`, `adres`) VALUES( :data, :name, :adres ) ';
                    //     $db ='test';
                    //     $nt = 'test';
                    //     $i = 'INSERT INTO '.$db.'.`'.$nt.'` '.$set.';';
                    // //    $q = 'SELECT LAST_INSERT_ID() FROM '.$db.'.`'.$nt.'`';
                    //     $bool = (bool)true;  // ($i,$var,(bool)true);
                    //     $pdo_Connect->my_insert($i,$var,$bool);       
                if (!is_bool($atomAx) ||!is_bool($bool) ) return false;
                $this->_atomAx = $atomAx;
                $this->bool =  $bool;
            try
            { 
                $this->my_db_connect($this->_atomAx)->beginTransaction();
                $s = $this->_connect->prepare($i);
                foreach( $var as $varN => $myVal) 
                {    
                    $this->param = $this->my_pdo_type($myVal[1]);
                    $this->leng = strlen($myVal[1])?:'0';
                    $s->bindParam($myVal[0], $myVal[1], $this->param?:PDO::PARAM_INPUT_OUTPUT, $this->leng);
                }
                $s->execute();  
                if($this->bool == true)
                {
                    $xs = $this->_connect->lastInsertId($nt);
                } 
                
            }
            catch(PDOException $e) 
            {
                if(isset($xs)) 
                {
                   $this->_connect->rollback();
                } 
                else
                {
                    
                   $this->name = 'my_fetch_array_merge';
                   $this->e=$e;
                   if (!isset($s))  die( 'Нет соединения с базой ');
                   $this->my_error_return($this->e, $this->name,$s);
                }  
            }
            finally
            {    

                return $xs??false;
                $this->_connect->commit();
                $s->closeCursor();
                
            }
        }
        public function my_service_db($i, bool $bool = false, bool $atomAx = false):int
        {
             //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 
           // шаблон запроса
                    //   (bool) true;//етретим значение  возвращает шв строки последней
                    // $today = date('Y-m-d H:i:s');
                    // $name = 'Иван Ивановй';
                    // $adres = 'Саратов';
            
            
                    //     $var= [ 
                    //         [':data',$today],
                    //         [':name',$name],
                    //         [':adres',$adres]
                    //     ];     
                    //     $set = '(`data`, `name`, `adres`) VALUES( :data, :name, :adres ) ';
                    //     $db ='test';
                    //     $nt = 'test';
                    //     $i = 'INSERT INTO '.$db.'.`'.$nt.'` '.$set.';';
                    // //    $q = 'SELECT LAST_INSERT_ID() FROM '.$db.'.`'.$nt.'`';
                    //     $bool = (bool)true;  // ($i,$var,(bool)true);
                    //     $pdo_Connect->my_insert($i,$var,$bool);

                $this->_connect = $this->my_db_connect($this->_atomAx) ;
                // $this->my_db_connect($this->_atomAx)->debugDumpParams();
            if(!$this->_connect) return false;     
            if (!is_bool($atomAx) ||!is_bool($bool) ) return false;
                $this->_atomAx = $atomAx;
                $this->bool =  $bool;
               
            try
            {
                // $this->my_db_connect($this->_atomAx)->debugDumpParams();
                // $this->my_db_connect($this->_atomAx)->beginTransaction();
                $s = $this->_connect->prepare($i,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                // $this->my_db_connect($this->_atomAx)->debugDumpParams();
                $s->execute();
                // $this->my_db_connect($this->_atomAx)->debugDumpParams();
                // if($this->bool == true) print $this->_connect->lastInsertId();
                $xs = $s->rowCount();
            }
            catch(PDOException $e) 
            {
             
                   $this->name = 'my_fetch_array_merge';
                   $this->e=$e;
                   if (!isset($s))  die( 'Нет соединения с базой ');
                   $this->my_error_return($this->e, $this->name,$s);
           
            }
            finally
            {
                return $xs;
                // $this->_connect->commit();
                $s->closeCursor();
            }
        }
    //     public function my_delete_record($q, $atomAx = false):mixed   
    //     {
    //          //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 
    // // шаблон запроса
    //         // $pdo_Connect = new \pdo\connect();
    //         // $db ='ve';
    //         // $nt = 'users';
    //         // $q = 'DELETE FROM '.$db.'.`'.$nt.'` WHERE `id_user`="'.addslashes(self::$id_user) .'"';
    //         // $pdo_Connect->my_delete($q);

    //         $this->_connect = $this->my_db_connect($this->_atomAx) ;
    //     if(!$this->_connect) return false;     
    //     if (!is_bool($atomAx) ) return false;
    //         $this->_atomAx = $atomAx;
    //         try
    //         {
    //             $s = $this->_connect->prepare($q);
    //             $this->my_db_connect($this->_atomAx)->beginTransaction();
    //             $s->execute();
    //         }
    //         catch(PDOException $e) 
    //         {
    //             $this->_connect->rollback();
    //             $this->name = 'my_delete_record';
    //             $this->e=$e;
    //             $this->my_error_return($this->e, $this->name,$s);
    //         }
    //         finally
    //         {
    //             $this->_connect->commit();
    //             $s->closeCursor();
    //         }
    //     }
        public function my_fetch($q, $atomAx = false):mixed  
        {
            $this->_connect = $this->my_db_connect($this->_atomAx) ;
            if(!$this->_connect) return false;    
            if (!is_bool($atomAx) ) return false;
            $this->_atomAx = $atomAx;
             //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 

        // шаблон запроса   
            //  $db ='ve';
            //  $nt = 'users';
            //  $clm = '';
            //      $q = 'SELECT * FROM '.$db.'.`'.$nt.'` WHERE login="'.addslashes(self::$login) .'"';	
 
            try
            {
                $s = $this->_connect->prepare($q);
                $this->my_db_connect($this->_atomAx)->beginTransaction();
                $s->execute();
                $xs = $s->fetch(PDO::FETCH_ASSOC,PDO::ATTR_ORACLE_NULLS);
            }
            catch(PDOException $e) 
            {
                if(isset($xs)) 
                {
                   $this->_connect->rollback();
                } 
                else
                {
                   $this->name = 'my_fetch_array_merge';
                   $this->e=$e;
                   if (!isset($s))  die( 'Нет соединения с базой ');
                   $this->my_error_return($this->e, $this->name,$s);
                }  
            }
            finally
            {
                if (empty($xs)){
                         $xs = $s->rowCount();
                }
               
                // if
                // $xs = $s->rowCount();
                return $xs?:(bool)false;
                $this->_connect->commit();
                $s->closeCursor();
             
            }
        }

        // public function my_last_id($q, $atomAx = false):mixed  
        // {
        //     $this->_connect = $this->my_db_connect($this->_atomAx);
        //     if(!$this->_connect) return false;    
        //     if (!is_bool($atomAx) ) return false;
        //     $this->_atomAx = $atomAx;
        //      //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 

        // // шаблон запроса   
        //     //  $db ='ve';
        //     //  $nt = 'users';
        //     //  $clm = '';
        //     //      $q = 'SELECT * FROM '.$db.'.`'.$nt.'` WHERE login="'.addslashes(self::$login) .'"';	
 
        //     try
        //     {
        //         $s = $this->_connect->query($q);
        //         // $this->my_db_connect($this->_atomAx)->beginTransaction();
        //         $this->my_db_connect($this->_atomAx)->beginTransaction();
        //         $s->execute();
        //         $xs = $s->fetch(PDO::FETCH_NUM);
        //         foreach($xs->fetch() as $db=>$tableName) {
                   
        //             }
        //     }
        //     catch(PDOException $e) 
        //     {
        //         // $this->my_db_connect($this->_atomAx)->rollback();
        //         $this->name = 'my_fetch';
        //         $this->e=$e;
        //         $this->my_error_return($this->e, $this->name,$s);
        //     }
        //     finally
        //     {
        //         // $this->my_db_connect($this->_atomAx)->commit();
                
        //         $this->_connect->commit();
        //         return $s?:(bool)false;
        //         $s->closeCursor();
             
        //     }
        // }
        public function my_count($q, $atomAx = false) 
        {
            $this->_connect = $this->my_db_connect($this->_atomAx);
            if(!$this->_connect) return '--== 1 ===-';    
            if (!is_bool($atomAx) ) return '--== 2 ===-';
            $this->_atomAx = $atomAx;
             //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 

        // шаблон запроса   
        // $db ='ve';
        // $nt = 'b_users';
        // $clm = '';
        //     $q = 'SELECT count(*) FROM '.$db.'.`'.$nt.'` WHERE id="1"';	
        //      $m = $pdo_Connect->my_count($q);
              
        //      if (is_array($m)){
        //        print '<pre>;';  print_r($m);  print '</pre>;'; 
        //      }else {  print $m;}

            try
            {
                $s = $this->_connect->prepare($q);
                $this->my_db_connect($this->_atomAx)->beginTransaction();
                $s->execute(); 
                $number_of_rows = $s->fetchColumn(); 
            }
            catch(PDOException $e) 
            {
                if(isset($number_of_rows)) 
                {
                   $this->_connect->rollback();
                } 
                else
                {
                   $this->name = 'my_fetch_array_merge';
                   $this->e=$e;
                   if (!isset($s))  die( 'Нет соединения с базой ');
                   $this->my_error_return($this->e, $this->name,$s);
                }  
            }
            finally
            {
                return $number_of_rows?:(bool)false;
                $this->_connect->commit();
                $s->closeCursor();
            }
        }
        public function my_fetch_array($q, $atomAx = false) 
        {
            
            $this->_connect = $this->my_db_connect($this->_atomAx);
            if(!$this->_connect) return false;    
             //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 

            // шаблон запроса   
            // $pdo_Connect = new \pdo\connect();
			// $db ='ve';
			// $nt = 'users';
			// $clm = '';
			// 	$q = 'SELECT * FROM '.$db.'.`'.$nt.'` WHERE `id_user`="'.self::$id_user.'"';				
			// 	$row = $pdo_Connect->my_fetch_array($q);
                if (!is_bool($atomAx) ) return false;	
                $this->_atomAx = $atomAx;

            

            try
            {
          
              

                $s = $this->_connect->prepare($q);
                $this->my_db_connect($this->_atomAx)->beginTransaction();
                $s->execute();
                $xs=$s->fetchAll(PDO::FETCH_ASSOC);

                // $xs=$s->fetchAll(PDO::FETCH_DEFAULT);
                if (empty($xs)){
                    $xs =  $s->rowCount();
        
                }
      
            }
            catch(PDOException $e) 
            {
                // $this->name = 'my_fetch_array';
                // $this->e=$e;
                // $this->my_error_return($this->e, $this->name,$s);

             if(isset($xs)) 
             {
                $this->_connect->rollback();
             } 
             else
             {
                $this->name = 'my_fetch_array_merge';
                $this->e=$e;
                if (!isset($s))  die( 'Нет соединения с базой ');
                $this->my_error_return($this->e, $this->name,$s);
             }  


                
            }
            finally
            {
             
                return $xs ?? false ;
                $this->_connect->commit();
                $s->closeCursor();
            }
        }
        public function my_fetch_array_merge($q, $atomAx = false)
        {
            $this->_connect = $this->my_db_connect($this->_atomAx);
            if(!$this->_connect) return false;    
            if (!is_bool($atomAx) ) return false;
            $this->_atomAx = $atomAx;
                  //$atomAx по умолчанию false, при значении  $atomAx = true, connent проходит в Базу данных ATOMAX( дополнительную базу ) 

                            // шаблон запроса   
                    //         $db = 'main';
                    // $nt = 'users';
                    // 	$nP = 'program_status';
                    // 	$q = 'SELECT `program_status` FROM '.$db.'.`'.$nt.'` WHERE `id`="'.$_SESSION['id'].'";';
                    // 	$program_statusData = \pdo\connect::my_fetch_array_merge($q, false);
          
            try
            {
                $x= $this->_connect->prepare($q);
                $this->my_db_connect($this->_atomAx)->beginTransaction();
                $x->execute();
                $xs=array_merge($x->fetch(\PDO::FETCH_ASSOC) );
            }
            catch(PDOException $e) 
            {
                if(isset($xs)) 
                {
                   $this->_connect->rollback();
                } 
                else
                {
                    $this->name = 'my_fetch_array_merge';
                    $this->e=$e;
                    if (!isset($x))  die( 'Нет соединения с базой ');
                    $this->my_error_return($this->e, $this->name,$x); 
                }  
            }
            finally
            {
                return $xs?:(bool)false;
                $this->_connect->commit();
                $x->closeCursor();
            }
        }

        public function my_pdo_type($value) 
        {
            $this->value = $value;
            
            $this->type = is_int($value)?PDO::PARAM_INT:(is_bool($value) ? PDO::PARAM_BOOL:(is_null($value) ?PDO::PARAM_NULL:(is_string($value) ? PDO::PARAM_STR:false) ) ) ;
        
            return $this->type;
        }

       


        public function my_error_return($e, $name, $s)
        {
            if (!isset($s))  die( 'Нет соединения с базой');
            $this->_connect = $this->my_db_connect($this->_atomAx);
            if(!$this->_connect) return false;    
            $this->name = $name;
            if (is_array($e) || is_object($e) ) 
            {
                die(  print'<br>'. $this->name.' - Подключение не удалось: <br>'.  print '</pre>'.  print_r($s->errorInfo() ).  print '</pre>'.  print $s->errorCode() );
            }
            else
            {
                die(  print'<br>'. $this->name.' - Подключение не удалось: <br>'.  print '</pre>'. print_r($s->errorInfo() ) . print('<br>') . print $s->errorCode() );
            }
        }

            public function lastInsertId(?string $table = null, ?string $column = null)
            {
                $this->my_db_connect();

                if ($this->_connection instanceof PDO) {
                    return $this->_connection->lastInsertId($table);
                }

                return $this->_connection->lastInsertId($table);
            }




        public function my_disconnect(): void
        {
            /** @psalm-suppress PossiblyNullPropertyAssignmentValue */
            $this->_connection = null;
            $this->_version = null;
        }



            public function my_setConnection($connection)
            {
                $this->_connection = $connection;

                return $this;
            } 



        public function my_isConnected(): bool
        {
            if ($this->_connection === null) {
                $connected = false;
            } else {
            try 
            {
                    $connected = (bool)$this->_connection->query('SELECT 1');
                } 
                catch (PDOException $e) 
                {
                    $connected = false;
                }
            }
            return $connected;
        }


        public function __destruct()
        {
            $this->_connection = null;
        }
    


        public function __debugInfo(): array
        {
            return [
                'connected' => $this->_connection !== null,
            ];
        }


    };
?>