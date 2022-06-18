<?php
echo '

старт... X1000

';
$bucket = $a[6];


    $video = $sql->_select2X1000_shell_tasks_shell_tasks($id);

echo '

запуск... X1000

';

    $steps = $video['steps'];
    $id = $video['id'];
    $videoName = array_reverse(explode('/', $video['src_origin']))[0];
    $videoNameArr = explode('.', $videoName);
    $logDir = __ROOT__.'/shellLog/'.$videoNameArr[0];
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755);
        // меняем юзера и группу папки логов
        chown($logDir, 'www-data');
        chgrp($logDir, 'www-data');
    }

    $video7 = $sql->_select2X1000_shell_tasks_shell_tasks($id);
    $steps = $video7['steps'];
    
    if($video7['src_noblack'] != '' && $video7['src_noblack'] != 'in work' && $video7['src_1000'] == '') {
        $lines = file($logDir.'/step1.txt');
        if(count($lines) > 0) {
            $lastLine = $lines[count($lines)-1];
            $step1End = strpos($lastLine, 'end');
            if($step1End !== false) {
            // ШАГ 7: ----------------------------------------------------
                // Запускаем генерацию видео под поток 1000К битрейт (16:9) --
                // -----------------------------------------------------------
                $resStartGen1000 = $sql->_update_shell4_tasks_shell_tasks($id);

                $dir1000k1 = __ROOT__.'/UserFiles/'.$video7["user_id"].'/1000k/';
                if (!file_exists($dir1000k1)) {
                    mkdir($dir1000k1, 0755);
                    // меняем юзера и группу папки
                    chown($dir1000k1, 'www-data');
                    chgrp($dir1000k1, 'www-data');
                }
                $dir1000k = __ROOT__.'/UserFiles/'.$video7["user_id"].'/1000k/'.$videoNameArr[0].'/';
                if (!file_exists($dir1000k)) {
                    mkdir($dir1000k, 0755);
                    // меняем юзера и группу папки
                    chown($dir1000k, 'www-data');
                    chgrp($dir1000k, 'www-data');
                }
                $steps = $steps+2;
                $sql->_update_steps_shell_tasks($steps, $id);
echo '

запуск... X1000: Запускаем генерацию видео под поток 1000К битрейт (16:9)

';
                if(shell_exec("ffmpeg -y -progress '".$logDir."/step7.txt' -i ".__ROOT__.$video7['src_noblack']." -acodec copy -vb 1000K ".$dir1000k.$videoNameArr[0]."-1000k.".$videoNameArr[1]." 2>&1")) {
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    // меняем юзера и группу лога шага
                    chown($logDir."/step7.txt", 'www-data');
                    chgrp($logDir."/step7.txt", 'www-data');
                    // меняем юзера и группу папки логов
                    chown($dir1000k.$videoNameArr[0]."-1000k.".$videoNameArr[1], 'www-data');
                    chgrp($dir1000k.$videoNameArr[0]."-1000k.".$videoNameArr[1], 'www-data');
                    $src_1000 = '/UserFiles/'.$video7["user_id"].'/1000k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-1000k.'.$videoNameArr[1];
                    $resEndGen1000 = $sql->_update2_shell4_tasks_shell_tasks($src_1000, $id);
                    
                    $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '7');

                    $video9 = $sql->_select2X1000_shell_tasks_shell_tasks($id);
                    $steps = $video9['steps'];
                    
                    if($video9['src_1000'] != '' && $video9['src_1000'] != 'in work' && $video9['src_1000'] != 'complete' && !file_exists(__ROOT__.'/UserFiles/'.$video9["user_id"].'/1000k/'.$videoNameArr[0].'/pl1000.m3u8')) {
                        $lines = file($logDir.'/step7.txt');
                        if(count($lines) > 0) {
                            $lastLine = $lines[count($lines)-1];
                            $step7End = strpos($lastLine, 'end');
                            if($step7End !== false) {
                            // ШАГ 9: -------------------------------------------
                            // Запускаем генерацию потока 1000К битрейт (16:9) --
                                // --------------------------------------------------
                                $dirStreamPl1000k = __ROOT__.'/UserFiles/'.$video9["user_id"].'/1000k/'.$videoNameArr[0].'/';
                                if (!file_exists($dirStreamPl1000k)) {
                                    mkdir($dirStreamPl1000k, 0755);
                                    // меняем юзера и группу папки
                                    chown($dirStreamPl1000k, 'www-data');
                                    chgrp($dirStreamPl1000k, 'www-data');
                                }
                                $steps = $steps+1;
                                $sql->_update_steps_shell_tasks($steps, $id);
                                shell_exec("ffmpeg -y -re -i ".__ROOT__.$video9['src_1000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamPl1000k."pl1000.m3u8' -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamPl1000k.$videoNameArr[0]."-%03d.ts' 2>&1");
                                $steps = $steps+1;
                                $sql->_update_steps_shell_tasks($steps, $id);
                                shell_exec("chown -R www-data:www-data '".$dirStreamPl1000k."*' 2>&1");
                                $steps = $steps+2;
                                $sql->_update_steps_shell_tasks($steps, $id);

                                $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video9['src_noblack']." -v quiet -show_format -show_streams 2>&1");
                                $steps = $steps+1;
                                $sql->_update_steps_shell_tasks($steps, $id);
                                $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                                $vW = explode('=', $parseInfoArr[9]);
                                $vH = explode('=', $parseInfoArr[10]);
                                $p2 = $vW[1].'x'.$vH[1];
echo '

запуск... X1000: Сохраняем

';

                                $file = __ROOT__.'/UserFiles/'.$video9["user_id"].'/'.$videoNameArr[0].'.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
500/pl500.m3u8
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
1000/pl1000.m3u8';
                            
                                file_put_contents($file, $playlist);
                                // меняем юзера и группу плейлиста
                                chown($file, 'www-data');
                                chgrp($file, 'www-data');

                                $video9Complete = $sql->_update_shell5_tasks_shell_tasks($id);


// ====================================================================

                                $uid = $video9["user_id"];
                                $id = array_reverse(explode('/', explode('.', $video9['m3u8'])[0]))[0];
                                $ext = explode('.', $video9['src_origin'])[1];

                                //$originDir = __ROOT__.'/UserFiles/'.$uid.'/'.$id.'/';
                                $del1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id.'/'.$id.'-1000k.'.$ext;
                                // удаление
                                if(file_exists($del1000)) {
                                    unlink($del1000);
                                }

//------------ Пути для сохранения
                                // плейлист для стандартного размера
                                $playlist1 = __ROOT__.$video9['m3u8'];
                                // файлы для плейлиста 1
                                $pl1FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id;

                                // сохраняем плейлисты  
                                $s3->putObject(array(
                                    'Bucket'     => $bucket,
                                    'SourceFile' => $playlist1,
                                    'Key'        => $uid.'/v/'.$id.'/m.m3u8',
                                    'ACL'        => 'public-read'
                                ));
                                
                                // записываем все в MINIO
                                $UploadAWS2 = $s3->uploadDirectory($pl1FilesDir1000, $bucket, $uid.'/v/'.$id.'/1000', array(
                                    'concurrency' => 20,
                                    'debug'       => true,
                                    'before' => function (\Aws\Command $command) {
                                    $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                                        ? 'public-read'
                                        : 'public-read';
                                    }
                                ));

                                
                                $sql->_statusSwitch_steps_shell_tasks('5', $uid, $a[9], $a[7]);
echo '

запуск... X1000: финальное сохранение

';
                                include_once('shellX2000.php');

                            }
                        }
                    }

                }
            }
        }
    }
    
//     $video8 = $sql->_select2X1000_shell_tasks_shell_tasks($id);
//     $steps = $video8['steps'];
    
//     if($video8['src_quad'] != '' && $video8['src_quad'] != 'in work' && $video8['src_quad_1000'] == '') {
//         $lines = file($logDir.'/step2.txt');
//         if(count($lines) > 0) {
//             $lastLine = $lines[count($lines)-1];
//             $step2End = strpos($lastLine, 'end');
//             if($step2End !== false) {
//             // ШАГ 8: ---------------------------------------------------
//                 // Запускаем генерацию видео под поток 1000К битрейт (1:1) --
//                 // ----------------------------------------------------------
//                 $resStartGen1000q = $sql->_update3_shell4_tasks_shell_tasks($id);

//                 $dirQuad1000k1 = __ROOT__.'/UserFiles/'.$video8["user_id"].'/cut/1000k/';
//                 if (!file_exists($dirQuad1000k1)) {
//                     mkdir($dirQuad1000k1, 0755);
//                     // меняем юзера и группу папки
//                     chown($dirQuad1000k1, 'www-data');
//                     chgrp($dirQuad1000k1, 'www-data');
//                 }
//                 $dirQuad1000k = __ROOT__.'/UserFiles/'.$video8["user_id"].'/cut/1000k/'.$videoNameArr[0].'/';
//                 if (!file_exists($dirQuad1000k)) {
//                     mkdir($dirQuad1000k, 0755);
//                     // меняем юзера и группу папки
//                     chown($dirQuad1000k, 'www-data');
//                     chgrp($dirQuad1000k, 'www-data');
//                 }
//                 $steps = $steps+2;
//                 $sql->_update_steps_shell_tasks($steps, $id);
// echo '

// запуск... X1000: Запускаем генерацию видео под поток 1000К битрейт (1:1)

// ';
//                 if(shell_exec("ffmpeg -y -progress '".$logDir."/step8.txt' -i ".__ROOT__.$video8['src_quad']." -acodec copy -vb 1000K ".$dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1]." 2>&1")) {
//                     $steps = $steps+2;
//                     $sql->_update_steps_shell_tasks($steps, $id);
//                     // меняем юзера и группу лога шага
//                     chown($logDir."/step8.txt", 'www-data');
//                     chgrp($logDir."/step8.txt", 'www-data');
//                     // меняем юзера и группу папки логов
//                     echo '
                    
//                     '.$dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1].'
                    
// ';
//                     chown($dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1], 'www-data');
//                     chgrp($dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1], 'www-data');

//                     $src_quad_1000 = '/UserFiles/'.$video8["user_id"].'/cut/1000k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-q1000k.'.$videoNameArr[1];
//                     $resEndGen1000q = $sql->_update4_shell4_tasks_shell_tasks($src_quad_1000, $id);

//                     $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '8');

//                     $video10 = $sql->_select2X1000_shell_tasks_shell_tasks($id);
//                     $steps = $video10['steps'];
                    
//                     if($video10['src_quad_1000'] != '' && $video10['src_quad_1000'] != 'in work' && $video10['src_quad_1000'] != 'complete' && !file_exists(__ROOT__.'/UserFiles/'.$video10["user_id"].'/cut/1000k/'.$videoNameArr[0].'/pl1000.m3u8')) {
//                         $lines = file($logDir.'/step8.txt');
//                         if(count($lines) > 0) {
//                             $lastLine = $lines[count($lines)-1];
//                             $step8End = strpos($lastLine, 'end');
//                             if($step8End !== false) {
//                             // ШАГ 10: -----------------------------------------
//                             // Запускаем генерацию потока 1000К битрейт (1:1) --
//                                 // -------------------------------------------------

//                                 $dirStreamCutPl1000k = __ROOT__.'/UserFiles/'.$video10["user_id"].'/cut/1000k/'.$videoNameArr[0].'/';
//                                 if (!file_exists($dirStreamCutPl1000k)) {
//                                     mkdir($dirStreamCutPl1000k, 0755);
//                                     // меняем юзера и группу папки
//                                     chown($dirStreamCutPl1000k, 'www-data');
//                                     chgrp($dirStreamCutPl1000k, 'www-data');
//                                 }
//                                 $steps = $steps+1;
//                                 $sql->_update_steps_shell_tasks($steps, $id);
//                                 shell_exec("ffmpeg -y -re -i ".__ROOT__.$video10['src_quad_1000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl1000k."pl1000.m3u8' -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamCutPl1000k.$videoNameArr[0]."-%03d.ts' 2>&1");
//                                 $steps = $steps+2;
//                                 $sql->_update_steps_shell_tasks($steps, $id);
//                                 shell_exec("chown -R www-data:www-data '".$dirStreamCutPl1000k."*' 2>&1");
//                                 $steps = $steps+2;
//                                 $sql->_update_steps_shell_tasks($steps, $id);
//                                 $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video10['src_quad']." -v quiet -show_format -show_streams 2>&1");
//                                 $steps = $steps+1;
//                                 $sql->_update_steps_shell_tasks($steps, $id);
//                                 $parseInfoArr = explode(PHP_EOL, $parseInfoString);
//                                 $vW = explode('=', $parseInfoArr[9]);
//                                 $vH = explode('=', $parseInfoArr[10]);
//                                 $p22 = $vW[1].'x'.$vH[1];
// echo '

// запуск... X1000: сохраняем поток 1000К битрейт (1:1)

// ';
//                                 $file = __ROOT__.'/UserFiles/'.$video10["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';

// $playlist = '#EXTM3U
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// cut/500/pl500.m3u8?id='.$video10["user_id"].'/v/'.$videoNameArr[0].'
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// cut/1000/pl1000.m3u8?id='.$video10["user_id"].'/v/'.$videoNameArr[0];
                        
//                                 file_put_contents($file, $playlist);
//                                 // меняем юзера и группу плейлиста
//                                 chown($file, 'www-data');
//                                 chgrp($file, 'www-data');

//                                 $video10Complete = $sql->_update2_shell5_tasks_shell_tasks($id);

                                
//                                 $video = $sql->_select2X1000_shell_tasks_shell_tasks($id);
//                                 $shellId = $id;
                                
//                                 $uid = $video["user_id"];
//                                 $id = array_reverse(explode('/', explode('.', $video['m3u8'])[0]))[0];
// echo '

// $video[m3u8]...'.$video['m3u8'].' id: '.$id.'

// ';

//                                 $ext = explode('.', $video['src_origin'])[1];
                                
//                                 $originDir = __ROOT__.'/UserFiles/'.$uid.'/'.$id.'/';

//                                 $del1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id.'/'.$id.'-1000k.'.$ext;
//                                 $del1000cut = __ROOT__.'/UserFiles/'.$uid.'/cut/1000k/'.$id.'/'.$id.'-q1000k.'.$ext;
//                                 // удаление
//                                 if(file_exists($del500cut2)) {
//                                     unlink($del1000);
//                                     unlink($del1000cut);
//                                 }

//                     //------------ Пути для сохранения
//                                 // плейлист для стандартного размера
//                                 $playlist1 = __ROOT__.$video['m3u8'];
//                                 // файлы для плейлиста 1
//                                 $pl1FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id;

//                                 // плейлист для квадратного размера
//                                 $playlist2 = __ROOT__.$video['m3u8_quad'];
//                                 // файлы для плейлиста 2
//                                 $pl2FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/cut/1000k/'.$id;

//                                 // 
//                                 // сохраняем плейлисты  
//                                 $s3->putObject(array(
//                                     'Bucket'     => $bucket,
//                                     'SourceFile' => $playlist1,
//                                     'Key'        => $uid.'/v/'.$id.'/m.m3u8',
//                                     'ACL'        => 'public-read'
//                                 ));
//                                 $s3->putObject(array(
//                                     'Bucket'     => $bucket,
//                                     'SourceFile' => $playlist2,
//                                     'Key'        => $uid.'/v/'.$id.'/q.m3u8',
//                                     'ACL'        => 'public-read'
//                                 ));
//                                 // записываем все в MINIO
//                                 $UploadAWS2 = $s3->uploadDirectory($pl1FilesDir1000, $bucket, $uid.'/v/'.$id.'/1000', array(
//                                     'concurrency' => 20,
//                                     'debug'       => true,
//                                     'before' => function (\Aws\Command $command) {
//                                     $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
//                                         ? 'public-read'
//                                         : 'public-read';
//                                     }
//                                 ));
//                                 $UploadAWS5 = $s3->uploadDirectory($pl2FilesDir1000, $bucket, $uid.'/v/'.$id.'/cut/1000', array(
//                                     'concurrency' => 20,
//                                     'debug'       => true,
//                                     'before' => function (\Aws\Command $command) {
//                                     $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
//                                         ? 'public-read'
//                                         : 'public-read';
//                                     }
//                                 ));
//                                 // получаем ID видео
//                                 // $vidData = $sql->_select_id_shellSave_media_uN($uid, $id);
//                                 // $vid = $vidData['id'];
//                                 echo 'a9: '.$a[9].' --';
//                                 // сохраняем новые minio-пути к плейлистам в базу

//                                 echo '-- '.$uid.' --';
//                                 echo '--$a[7] '.$id.' --';
                                
//                                 //$mres = $sql->_update_shellSave_media_uN($uid, $a[9], $a[7]);

//                                 //echo $sql->_update3_status_checkVideoStatus_shell_tasks($shellId);

//                                 $sql->_statusSwitch_steps_shell_tasks('5', $uid, $a[9], $a[7]);
// echo '

// запуск... X1000: финальное сохранение

// ';
//                                 include_once('shellX2000.php');
                                
//                             }
//                         }
//                     }

//                 }
//             }
//         }
//     }


?>