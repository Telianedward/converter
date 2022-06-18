<?php
namespace requests
{
    use pdo\connect;
	use use\session\Handler;
	use \service\my;
	use security\openssl_encrypt;
	use Help\Func;







        class sql
        {
            public function __construct()
            {
              
            }


            public function _insert_login_main_user()
            {
                $handler = new Handler();
				$obj = new openssl_encrypt();
                $this->_pdo_connect = new connect();
                $_func = new Func();
                
                $this->_db ='main';
                $this->_nt = 'users';
                    $this->_q = 'SHOW TABLES FROM `'.$this->_db.'` LIKE  "'.$this->_nt.'"';
                        $this->_result = $this->_pdo_connect->my_service_db($this->_q);
                            if($this->_result == 0) 
                            {
                                (string)$this->_q = 'CREATE TABLE '.$this->_db.'.`'.$this->_nt.'` (
                                        `id` int NOT NULL,
                                        `unilink` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                        `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                        `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
                                    
                                    $this->_pdo_connect->my_service_db($this->_q);
                                    (string)$this->_q= 'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'`
                                                ADD PRIMARY KEY (`id`);';
                                    $this->_pdo_connect->my_service_db($this->_q);
                                    (string)$this->_q=  'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'` MODIFY `id` int NOT NULL AUTO_INCREMENT';
                                    $this->_pdo_connect->my_service_db($this->_q);
                                    (string)$this->_q= 'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'` 
                                            ADD KEY `id` (`id`),
                                            ADD KEY `unilink` ( `unilink`),
                                            ADD KEY `phone` (`phone`),
                                            ADD KEY `email` (`email`)
                                            ';
                                    $this->_pdo_connect->my_service_db($this->_q);
                                    $this->_zero = 'no-info';
                                    (array)$this->_var = [ 
                                        [':unilink',$this->_login],
                                        [':phone',$this->_email],
                                        [':email',$this->_telephone]
                                    ];  
                                (string)$this->_set = '(`unilink`, `phone`, `email`) VALUES( :unilink, :phone , :email ) ';

                                (string) $this->_q = 'INSERT INTO '.$this->_db.'.`'.$this->_nt.'` '.$this->_set.' ;';
                                // echo $this->_q . ' -q- <br>';
                                (bool) $bool = true;  // ($i,$var,(bool)true);
                                $this->query = $this->_pdo_connect->my_insert($this->_q,$this->_db,$this->_nt, $this->_var,$bool);
                            }
                            else
                            {
                                $this->_zero = 'no-info';
                                (array)$this->_var = [ 
                                    [':unilink',$this->_login],
                                    [':phone',$this->_email],
                                    [':email',$this->_telephone]
                                ];  
                                (string)$this->_set = '(`unilink`, `phone`, `email`) VALUES( :unilink, :phone , :email ) ';
                                    (string) $this->_q = 'INSERT INTO '.$this->_db.'.`'.$this->_nt.'` '.$this->_set.' ;';
                                    (bool) $bool = true;  // ($i,$var,(bool)true);
                                    $this->query = $this->_pdo_connect->my_insert($this->_q,$this->_db,$this->_nt, $this->_var,$bool);

                            }
            }





        }

}
?>