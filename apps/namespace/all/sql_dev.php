<?php
namespace requests_dev
{
    include_once __ROOT__.'/apps/namespace/all/pdo.php';
    use \pdo\connect;
	use use\session\Handler;
	use \service\my;
	use security\openssl_encrypt;
	use Help\Func;
    




        class sql_dev
        {

            public $_pdo_connect;

            public function __construct()
            {
                
                $this->_pdo_connect = new connect();
                
            }


            // public function _insert_login_main_user()
            // {
            //     $handler = new Handler();
			// 	$obj = new openssl_encrypt();
            //     $this->_pdo_connect = new connect();
            //     $_func = new Func();
                
            //     $this->_db ='main';
            //     $this->_nt = 'users';
            //         $this->_q = 'SHOW TABLES FROM `'.$this->_db.'` LIKE  "'.$this->_nt.'"';
            //             $this->_result = $this->_pdo_connect->my_service_db($this->_q);
            //                 if($this->_result == 0) 
            //                 {
            //                     (string)$this->_q = 'CREATE TABLE '.$this->_db.'.`'.$this->_nt.'` (
            //                             `id` int NOT NULL,
            //                             `unilink` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            //                             `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            //                             `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            //                             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
                                    
            //                         $this->_pdo_connect->my_service_db($this->_q);
            //                         (string)$this->_q= 'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'`
            //                                     ADD PRIMARY KEY (`id`);';
            //                         $this->_pdo_connect->my_service_db($this->_q);
            //                         (string)$this->_q=  'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'` MODIFY `id` int NOT NULL AUTO_INCREMENT';
            //                         $this->_pdo_connect->my_service_db($this->_q);
            //                         (string)$this->_q= 'ALTER TABLE '.$this->_db.'.`'.$this->_nt.'` 
            //                                 ADD KEY `id` (`id`),
            //                                 ADD KEY `unilink` ( `unilink`),
            //                                 ADD KEY `phone` (`phone`),
            //                                 ADD KEY `email` (`email`)
            //                                 ';
            //                         $this->_pdo_connect->my_service_db($this->_q);
            //                         $this->_zero = 'no-info';
            //                         (array)$this->_var = [ 
            //                             [':unilink',$this->_login],
            //                             [':phone',$this->_email],
            //                             [':email',$this->_telephone]
            //                         ];  
            //                     (string)$this->_set = '(`unilink`, `phone`, `email`) VALUES( :unilink, :phone , :email ) ';

            //                     (string) $this->_q = 'INSERT INTO '.$this->_db.'.`'.$this->_nt.'` '.$this->_set.' ;';
            //                     // echo $this->_q . ' -q- <br>';
            //                     (bool) $bool = true;  // ($i,$var,(bool)true);
            //                     $this->query = $this->_pdo_connect->my_insert($this->_q,$this->_db,$this->_nt, $this->_var,$bool);
            //                 }
            //                 else
            //                 {
            //                     $this->_zero = 'no-info';
            //                     (array)$this->_var = [ 
            //                         [':unilink',$this->_login],
            //                         [':phone',$this->_email],
            //                         [':email',$this->_telephone]
            //                     ];  
            //                     (string)$this->_set = '(`unilink`, `phone`, `email`) VALUES( :unilink, :phone , :email ) ';
            //                         (string) $this->_q = 'INSERT INTO '.$this->_db.'.`'.$this->_nt.'` '.$this->_set.' ;';
            //                         (bool) $bool = true;  // ($i,$var,(bool)true);
            //                         $this->query = $this->_pdo_connect->my_insert($this->_q,$this->_db,$this->_nt, $this->_var,$bool);

            //                 }
            // }

            //plugins/uppy/upload.php
            public function _insert_upload_tasks_shell_tasks($uid, $nn)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $var = [ 
                    [':user_id', $uid],
                    [':src_origin', $nn]
                ];     
                $set = '(`date`, `user_id`, `status`, `src_origin`) VALUES( NOW(), :user_id, "in work", :src_origin ) ;';
                $i = 'INSERT INTO '.$db.'.`'.$nt.'` '.$set.' ;';
                $bool = (bool)true;
                $this->_pdo_connect->my_insert($i, $db, $nt, $var, $bool);
            }
            
            public function _insert_upload_media_uN($uid, $size, $sizes, $ext, $key048, $src500, $src3)
            {
                $db = 'media';
                $nt = 'u'.$uid;
                $var = [ 
                    [':size', $size],
                    [':sizes', $sizes],
                    [':type', $ext],
                    [':key048', $key048],
                    [':src500', $src500],
                    [':src3', $src3]
                ];     
                $set = '(`status`, `date`, `title`, `size`, `sizes`, `type`, `key`, `size2`, `sizes2`, `key`, `src3`) VALUES( "0", NOW(), "added", :size, :sizes, :type, :key048, :size, :sizes, :src500, :src3 )';
                $i = 'INSERT INTO '.$db.'.`'.$nt.'` '.$set.' ;';
                $bool = (bool)true;
                $this->_pdo_connect->my_insert($i, $db, $nt, $var, $bool);
            }

            // new - 0
            // in work - 1
            // complete - 2
            // saved1 - 3
            // save1 - 4
            // saved2 - 5
            // save2 - 6
            // saved3 - 7
            // save3 - 8
            public function _statusSwitch_steps_shell_tasks($s, $uid, $mid, $sid)
            {
                $db = 'media';
                $nt = 'u'.$uid;
                $set = '`st_status` = :st_status';
                $var = [ 
                    [':st_status', $s],
                    [':id', $mid]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                $this->_pdo_connect->my_update($q,$var);

                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`status` = :status';
                $var = [ 
                    [':status', $s],
                    [':id', $sid]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';

                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update_steps_shell_tasks($step, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`steps` = :step';
                $var = [ 
                    [':step', $step],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            // shellX
            public function _update_origin_shell_tasks($key, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_origin` = :src_origin';
                $var = [ 
                    [':src_origin', $key],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            public function _update_sizePriority_shell_tasks($p, $uid, $id)
            {
                $db = 'media';
                $nt = 'u'.$uid;
                $set = '`size_priority` = :size_priority';
                $var = [ 
                    [':size_priority', $p],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            public function _updatePosterQuad_shellX_tasks_shell_tasks($blob, $vid)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`poster_quad`= :poster_quad';
                $var = [ 
                    [':poster_quad', $blob],
                    [':id', $vid]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            public function _updatePoster_shellX_tasks_shell_tasks($blob, $vid)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`poster`= :poster';
                $var = [ 
                    [':poster', $blob],
                    [':id', $vid]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shellX1000.php:
            public function _selectX_shell_tasks_shell_tasks()
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        (status = "4"
                    OR
                        status = "3")
                    AND
                        src_origin <> ""
                ;';
                
                $active_tasks = $this->_pdo_connect->my_fetch_array($q);

                return $active_tasks;
            }
            // shellX2000.php:
            public function _selectXX_shell_tasks_shell_tasks()
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        (status = "6"
                    OR
                        status = "5")
                    AND
                        src_origin <> ""
                ;';
                
                $active_tasks = $this->_pdo_connect->my_fetch_array($q);

                return $active_tasks;
            }
            
            // shell.php:
            public function _select_shell_tasks_shell_tasks()
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        status = "0"
                    AND
                        src_origin <> ""
                ;';
                
                $active_tasks = $this->_pdo_connect->my_fetch_array($q);

                return $active_tasks;
            }

            public function _select2_shell_tasks_shell_tasks($id)
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        id = '.$id.'
                ;';
                
                return $this->_pdo_connect->my_fetch($q);
            }
            
            public function _select2X1000_shell_tasks_shell_tasks($id)
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        id = '.$id.'
                    AND
                        (status = "4" OR status = "3")
                ;';
                
                return $this->_pdo_connect->my_fetch($q);
            }
            public function _select2X2000_shell_tasks_shell_tasks($id)
            {
                $dbB = 'tasks';
                $ntB = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$dbB.'.`'.$ntB.'`
                    WHERE
                        id = '.$id.'
                ;';
                
                return $this->_pdo_connect->my_fetch($q);
            }

            public function _insert_shell_tasks_shell_steps($file_id, $step)
            {
                $db = 'tasks';
                $nt = 'shell_steps';
                $var = [ 
                    [':file_id', $file_id],
                    [':step', $step]
                ];     
                $set = '(`file_id`, `step`, `value`) VALUES( :file_id, :step, "end" ) ;';
                $i = 'INSERT INTO '.$db.'.`'.$nt.'` '.$set.' ;';
                $bool = (bool)true;
                return $this->_pdo_connect->my_insert($i, $db, $nt, $var, $bool);
            }
            
            public function _update_shell_tasks_shell_tasks($src_origin, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_origin`= :src_origin';
                $var = [ 
                    [':src_origin', $src_origin],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update2_shell_tasks_shell_tasks($bitrate, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_noblack`= "in work", `bitrate` = :bitrate';
                $var = [ 
                    [':bitrate', $bitrate],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update3_shell_tasks_shell_tasks($src_noblack, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_noblack`= :src_noblack';
                $var = [ 
                    [':src_noblack', $src_noblack],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update4_shell_tasks_shell_tasks($secondSize, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad` = "in work", `bitrate`= :bitrate';
                $var = [ 
                    [':bitrate', $secondSize],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update5_shell_tasks_shell_tasks($src_quad, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad`= :src_quad';
                $var = [ 
                    [':src_quad', $src_quad],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shell2.php:
            public function _update_shell2_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_500`= "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update2_shell2_tasks_shell_tasks($src_500, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_500`= :src_500';
                $var = [ 
                    [':id', $id],
                    [':src_500', $src_500]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update3_shell2_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_500`= "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update4_shell2_tasks_shell_tasks($src_quad_500, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_500`= :src_quad_500';
                $var = [ 
                    [':id', $id],
                    [':src_quad_500', $src_quad_500]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shell3.php:
            public function _update_shell3_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`m3u8` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update2_shell3_tasks_shell_tasks($m3u8, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`m3u8` = :m3u8';
                $var = [ 
                    [':id', $id],
                    [':m3u8', $m3u8]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update3_shell3_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_500` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update4_shell3_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`m3u8_quad` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            public function _update5_shell3_tasks_shell_tasks($m3u8_quad, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`m3u8_quad` = :m3u8_quad';
                $var = [ 
                    [':m3u8_quad', $m3u8_quad],
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shell4.php:
            public function _update_shell4_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_1000` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update2_shell4_tasks_shell_tasks($src_1000, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_1000` = :src_1000';
                $var = [ 
                    [':id', $id],
                    [':src_1000', $src_1000]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update3_shell4_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_1000` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update4_shell4_tasks_shell_tasks($src_quad_1000, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_1000` = :src_quad_1000';
                $var = [ 
                    [':id', $id],
                    [':src_quad_1000', $src_quad_1000]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shell5.php:
            public function _update_shell5_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_1000` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update2_shell5_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_1000` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shell6.php:
            public function _update_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_2000` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update2_shell6_tasks_shell_tasks($src_2000, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_2000` = :src_2000';
                $var = [ 
                    [':id', $id],
                    [':src_2000', $src_2000]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update3_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_2000` = "in work"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update4_shell6_tasks_shell_tasks($src_quad_2000, $id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_2000` = :src_quad_2000';
                $var = [ 
                    [':id', $id],
                    [':src_quad_2000', $src_quad_2000]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update5_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_2000` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update6_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_2000` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _update7_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_quad_2000` = "stream"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update8_shell6_tasks_shell_tasks($id)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`src_2000` = "stream"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // shellSave.php:
            public function _select_shellSave_tasks_shell_tasks()
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$db.'.`'.$nt.'`
                    WHERE
                        status = "complete"
                    AND
                        src_origin <> ""
                ;';
                
                $active_tasks = $this->_pdo_connect->my_fetch_array($q);

                return $active_tasks;
            }

            public function _update_shellSave_tasks_shell_tasks($poster, $vid)
            {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`poster` = :poster';
                $var = [ 
                    [':vid', $vid],
                    [':poster', $poster]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :vid ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _updateQuad_shellSave_tasks_shell_tasks($poster_quad, $vid)
            {
                echo '
                
_updateQuad_shellSave_tasks_shell_tasks сработала

'; 
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`poster_quad` = :poster_quad';
                $var = [ 
                    [':vid', $vid],
                    [':poster_quad', $poster_quad]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :vid ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            public function _select_id_shellSave_media_uN($uid, $vid) {
                $db = 'media';
                $nt = 'u'.$uid;
                $q = '
                    SELECT id FROM 
                        '.$db.'.`'.$nt.'`
                    WHERE
                        `key` LIKE "%'.$vid.'%"
                ;';
                $res = $this->_pdo_connect->my_fetch($q);
                
                return $res['id'];
            }

            public function _update_shellSave_media_uN($uid, $vid, $src1) {
                $db = 'media';
                $nt = 'u'.$uid;
                $set = '`key` = :key';
                $var = [ 
                    [':vid', $vid],
                    [':key', $src1]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :vid ';
                return $this->_pdo_connect->my_update($q,$var);
            }
            
            public function _update_sizes_uN($uid, $sizes, $s_p, $dar, $vid) {
                $db = 'media';
                $nt = 'u'.$uid;
                $set = '`sizes` = :sizes, `size_priority` = :size_priority, `dar` = :dar';
                $var = [ 
                    [':vid', $vid],
                    [':sizes', $sizes],
                    [':size_priority', $s_p],
                    [':dar', $dar]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :vid ';
                return $this->_pdo_connect->my_update($q,$var);
            }

            // gallery/checkVideoStatus.php
            public function _select_checkVideoStatus_shell_tasks() {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $q = '
                    SELECT * FROM 
                        '.$db.'.`'.$nt.'`
                    WHERE
                        status = "in work"
                    OR
                        status = "saved"
                    AND
                        src_origin <> ""
                ;';

                return $this->_pdo_connect->my_fetch_array($q);
            }

            public function _select_id_checkVideoStatus_uN($uid, $vid) {
                $db = 'media';
                $nt = 'u'.$uid;
                $q = '
                    SELECT id FROM 
                        '.$db.'.`'.$nt.'`
                    WHERE
                        `key` LIKE "%'.$vid.'%"
                ;';

                return $this->_pdo_connect->my_fetch($q);
            }

            public function _select_status_checkVideoStatus_shell_tasks($id) {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $q = '
                    SELECT status FROM 
                        '.$db.'.`'.$nt.'`
                    WHERE
                        id = "'.$id.'"
                ;';

                return $this->_pdo_connect->my_fetch($q);
            }

            public function _update_status_checkVideoStatus_shell_tasks($id) {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`status` = "complete"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);

            }

            public function _update2_status_checkVideoStatus_shell_tasks($id) {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`status` = "saved1"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);

            }

            public function _update3_status_checkVideoStatus_shell_tasks($id) {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`status` = "5"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);

            }
            public function _update4_status_checkVideoStatus_shell_tasks($id) {
                $db = 'tasks';
                $nt = 'shell_tasks';
                $set = '`status` = "7"';
                $var = [ 
                    [':id', $id]
                ];
                $q = 'UPDATE '.$db.'.`'.$nt.'` SET '.$set.' WHERE id = :id ';
                return $this->_pdo_connect->my_update($q,$var);

            }
        }

}
?>