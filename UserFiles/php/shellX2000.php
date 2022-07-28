<?php
$id = $a[7];
$video = $sql->_select2X2000_shell_tasks_shell_tasks($id);

function RDir( $path ) {
    // если путь существует и это папка
    if ( file_exists( $path ) AND is_dir( $path ) ) {
        // открываем папку
        $dir = opendir($path);
        while ( false !== ( $element = readdir( $dir ) ) ) {
            // удаляем только содержимое папки
            if ( $element != '.' AND $element != '..' )  {
                $tmp = $path . '/' . $element;
                chmod( $tmp, 0777 );
                // если элемент является папкой, то
                // удаляем его используя нашу функцию RDir
                if ( is_dir( $tmp ) ) {
                    $this->RDir( $tmp );
                    // если элемент является файлом, то удаляем файл
                } else {
                    unlink( $tmp );
                }
            }
        }
        // закрываем папку
        closedir($dir);
        // удаляем саму папку
        if ( file_exists( $path ) ) {
            rmdir( $path );
        }
    }
}

echo '

запуск... X2000

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

    $video11 = $sql->_select2X2000_shell_tasks_shell_tasks($id);
    $steps = $video11['steps'];
    
    if(count($video11) > 0 && $video11['src_noblack'] != '' && $video11['src_noblack'] != 'in work' && $video11['src_2000'] == '') {
        $lines = file($logDir.'/step1.txt');
        if(count($lines) > 0) {
            $lastLine = $lines[count($lines)-1];
            $step1End = strpos($lastLine, 'end');
            if($step1End !== false) {
            // ШАГ 11: ---------------------------------------------------
              // Запускаем генерацию видео под поток 2000К битрейт (16:9) --
                // -----------------------------------------------------------
                $resStartGen2000 = $sql->_update_shell6_tasks_shell_tasks($id);

                $dir2000k1 = __ROOT__.'/UserFiles/'.$video11["user_id"].'/2000k/';
                if (!file_exists($dir2000k1)) {
                    mkdir($dir2000k1, 0755);
                    // меняем юзера и группу папки
                    chown($dir2000k1, 'www-data');
                    chgrp($dir2000k1, 'www-data');
                }
                $dir2000k = __ROOT__.'/UserFiles/'.$video11["user_id"].'/2000k/'.$videoNameArr[0].'/';
                if (!file_exists($dir2000k)) {
                    mkdir($dir2000k, 0755);
                    // меняем юзера и группу папки
                    chown($dir2000k, 'www-data');
                    chgrp($dir2000k, 'www-data');
                }
                $steps = $steps+1;
                $sql->_update_steps_shell_tasks($steps, $id);
                if(shell_exec("ffmpeg -y -progress '".$logDir."/step11.txt' -i ".__ROOT__.$video11['src_noblack']." -acodec copy -vb 2000K ".$dir2000k.$videoNameArr[0]."-2000k.".$videoNameArr[1]." 2>&1")) {
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    // меняем юзера и группу лога шага
                    chown($logDir."/step11.txt", 'www-data');
                    chgrp($logDir."/step11.txt", 'www-data');
                    // меняем юзера и группу папки логов
                    chown($dir2000k.$videoNameArr[0]."-2000k.".$videoNameArr[1], 'www-data');
                    chgrp($dir2000k.$videoNameArr[0]."-2000k.".$videoNameArr[1], 'www-data');

                    $src_2000 = '/UserFiles/'.$video11["user_id"].'/2000k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-2000k.'.$videoNameArr[1];
                    $resEndGen2000 = $sql->_update2_shell6_tasks_shell_tasks($src_2000, $id);

                    $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '11');

                }
            }
        }
    }

    $video12 = $sql->_select2X2000_shell_tasks_shell_tasks($id);
    $steps = $video12['steps'];
    
    if(count($video12) > 0 && $video12['src_quad'] != '' && $video12['src_quad'] != 'in work' && $video12['src_quad_2000'] == '') {
        $lines = file($logDir.'/step2.txt');
        if(count($lines) > 0) {
            $lastLine = $lines[count($lines)-1];
            $step2End = strpos($lastLine, 'end');
            if($step2End !== false) {
            // ШАГ 12: --------------------------------------------------
              // Запускаем генерацию видео под поток 2000К битрейт (1:1) --
                // ----------------------------------------------------------
                $resStartGen2000q = $sql->_update3_shell6_tasks_shell_tasks($id);

                $dirQuad2000k1 = __ROOT__.'/UserFiles/'.$video12["user_id"].'/cut/2000k/';
                if (!file_exists($dirQuad2000k1)) {
                    mkdir($dirQuad2000k1, 0755);
                    // меняем юзера и группу папки
                    chown($dirQuad2000k1, 'www-data');
                    chgrp($dirQuad2000k1, 'www-data');
                }
                $dirQuad2000k = __ROOT__.'/UserFiles/'.$video12["user_id"].'/cut/2000k/'.$videoNameArr[0].'/';
                if (!file_exists($dirQuad2000k)) {
                    mkdir($dirQuad2000k, 0755);
                    // меняем юзера и группу папки
                    chown($dirQuad2000k, 'www-data');
                    chgrp($dirQuad2000k, 'www-data');
                }
                $steps = $steps+1;
                $sql->_update_steps_shell_tasks($steps, $id);
                if(shell_exec("ffmpeg -y -progress '".$logDir."/step12.txt' -i ".__ROOT__.$video12['src_quad']." -acodec copy -vb 2000K ".$dirQuad2000k.$videoNameArr[0]."-q2000k.".$videoNameArr[1]." 2>&1")) {
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    // меняем юзера и группу лога шага
                    chown($logDir."/step12.txt", 'www-data');
                    chgrp($logDir."/step12.txt", 'www-data');
                    // меняем юзера и группу папки логов
                    chown($dirQuad2000k.$videoNameArr[0]."-q2000k.".$videoNameArr[1], 'www-data');
                    chgrp($dirQuad2000k.$videoNameArr[0]."-q2000k.".$videoNameArr[1], 'www-data');

                    $src_quad_2000 = '/UserFiles/'.$video12["user_id"].'/cut/2000k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-q2000k.'.$videoNameArr[1];
                    $resEndGen2000q = $sql->_update4_shell6_tasks_shell_tasks($src_quad_2000, $id);
                    
                    $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '12');

                }
            }
        }
    }
    
    $video13 = $sql->_select2X2000_shell_tasks_shell_tasks($id);
    $steps = $video13['steps'];
    
    if(count($video13) > 0 && $video13['src_2000'] != '' && $video13['src_2000'] != 'complete' && $video13['src_2000'] != 'in work' && file_exists(__ROOT__.$video13['src_noblack']) && $video13['src_2000'] != 'stream') {
        $lines = file($logDir.'/step11.txt');
        if(count($lines) > 0) {
            $lastLine = $lines[count($lines)-1];
            $step11End = strpos($lastLine, 'end');
            if($step11End !== false) {

            $video13StreamGenStart = $sql->_update8_shell6_tasks_shell_tasks($id);
            // ШАГ 13: ------------------------------------------
              // Запускаем генерацию потока 2000К битрейт (16:9) --
                // --------------------------------------------------
                $dirStreamPl2000k = __ROOT__.'/UserFiles/'.$video13["user_id"].'/2000k/'.$videoNameArr[0].'/';
                if (!file_exists($dirStreamPl2000k)) {
                    mkdir($dirStreamPl2000k, 0755);
                    // меняем юзера и группу папки
                    chown($dirStreamPl2000k, 'www-data');
                    chgrp($dirStreamPl2000k, 'www-data');
                }
                $steps = $steps+1;
                $sql->_update_steps_shell_tasks($steps, $id);
                if(shell_exec("ffmpeg -y -progress '".$logDir."/step13.txt' -re -i ".__ROOT__.$video13['src_2000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamPl2000k."pl2000.m3u8' -hls_playlist_type vod -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamPl2000k.$videoNameArr[0]."-%03d.ts' 2>&1")) {
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    shell_exec("chown -R www-data:www-data '".$dirStreamPl2000k."*' 2>&1");
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    //echo __ROOT__.$video13['src_noblack'];
                    //echo ' ### ';
                    $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video13['src_noblack']." -v quiet -show_format -show_streams 2>&1");
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                    
                    //print_r($parseInfoArr);

                    $vW = explode('=', $parseInfoArr[9]);
                    $vH = explode('=', $parseInfoArr[10]);
                    $p2 = $vW[1].'x'.$vH[1];

                    $file = __ROOT__.'/UserFiles/'.$video13["user_id"].'/'.$videoNameArr[0].'.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
500/pl500.m3u8
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
1000/pl1000.m3u8
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=2032924,BANDWIDTH=2047668,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
2000/pl2000.m3u8';
                    
                    file_put_contents($file, $playlist);
                    // меняем юзера и группу плейлиста
                    chown($file, 'www-data');
                    chgrp($file, 'www-data');

                    $video13Complete = $sql->_update5_shell6_tasks_shell_tasks($id);

                    $video = $sql->_select2X2000_shell_tasks_shell_tasks($id);
                    $shellId = $id;        
                    $uid = $video["user_id"];
                    $nameid = array_reverse(explode('/', explode('.', $video['m3u8'])[0]))[0];
                    $ext = explode('.', $video['src_origin'])[1];

                    
                    $del2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$nameid.'/'.$nameid.'-2000k.'.$ext;
                    // удаление
                    if(file_exists($del2000)) {
                        unlink($del2000);
                    }

        //------------ Пути для сохранения
                    // плейлист для стандартного размера
                    $playlist1 = __ROOT__.$video['m3u8'];
                    // файлы для плейлиста 1
                    $pl1FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$nameid;
                    $pl1FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$nameid;
                    $pl1FilesDir2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$nameid;
                    $pl1FilesDirGen = __ROOT__.'/UserFiles/'.$uid.'/generated/'.$nameid.'nb.'.$ext;
                    $shellLog = __ROOT__.'/shellLog/'.$nameid;
                    $out = __ROOT__.'/out/out'.$nameid.'.txt';
                    // постер
                    $poster = __ROOT__.$video['poster'];
                    // 
                    // сохраняем плейлисты  
                    $s3->putObject(array(
                        'Bucket'     => $bucket,
                        'SourceFile' => $playlist1,
                        'Key'        => 'v/'.$nameid.'/m.m3u8',
                        'ACL'        => 'public-read'
                    ));

                    $playlist2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/pl2000.m3u8';
                    chmod($playlist2000, 0777);
                    $p2000Content = file_get_contents($playlist2000);
                    $p2000ContentNew = str_replace('#EXT-X-MEDIA-SEQUENCE:0','#EXT-X-MEDIA-SEQUENCE:0
#EXT-X-PLAYLIST-TYPE:VOD', $p2000Content);
                    file_put_contents($playlist2000, $p2000ContentNew);

                    $UploadAWS3 = $s3->uploadDirectory($pl1FilesDir2000, $bucket, 'v/'.$nameid.'/2000', array(
                        'concurrency' => 20,
                        'debug'       => true,
                        'before' => function (\Aws\Command $command) {
                        $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                            ? 'public-read'
                            : 'public-read';
                        }
                    ));

                    unlink($playlist1);
                    unlink($pl1FilesDirGen);
                    unlink($out);
                    RDir($pl1FilesDir500);
                    RDir($pl1FilesDir1000);
                    RDir($pl1FilesDir2000);
                    RDir($shellLog);
echo '

конец... X2000: меняем статус:

';
                    //echo $sql->_update4_status_checkVideoStatus_shell_tasks($shellId);
                    echo '---uid-'.$uid;
                    //echo '---vid-'.$vid;
                    echo '---shellId-'.$shellId;
                    sleep(10);
                    $sql->_statusSwitch_steps_shell_tasks('7', $uid, $a[9], $shellId);
echo '

конец..........................................................................

';
                }
            }
        }
    }
    
//     $video14 = $sql->_select2X2000_shell_tasks_shell_tasks($id);
//     $steps = $video14['steps'];
    
//     echo '-'.count($video14).'- ';
//     print_r($video14);
//     if(count($video14) > 0 && $video14['src_quad_2000'] != '' && $video14['src_quad_2000'] != 'complete' && $video14['src_quad_2000'] != 'in work' && $video14['src_quad_2000'] != 'stream') {
//         $lines = file($logDir.'/step12.txt');
//         if(count($lines) > 0) {
//             $lastLine = $lines[count($lines)-1];
//             $step12End = strpos($lastLine, 'end');
//             if($step12End !== false) {
                
//             $video14StreamGenStart = $sql->_update7_shell6_tasks_shell_tasks($id);
//             // ШАГ 14: -----------------------------------------
//               // Запускаем генерацию потока 2000К битрейт (1:1) --
//                 // -------------------------------------------------
//                 $dirStreamCutPl2000k = __ROOT__.'/UserFiles/'.$video14["user_id"].'/cut/2000k/'.$videoNameArr[0].'/';
//                 if (!file_exists($dirStreamCutPl2000k)) {
//                     mkdir($dirStreamCutPl2000k, 0755);
//                     // меняем юзера и группу папки
//                     chown($dirStreamCutPl2000k, 'www-data');
//                     chgrp($dirStreamCutPl2000k, 'www-data');
//                 }
//                 $steps = $steps+1;
//                 $sql->_update_steps_shell_tasks($steps, $id);
//                 if(shell_exec("ffmpeg -y -progress '".$logDir."/step14.txt' -re -i ".__ROOT__.$video14['src_quad_2000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl2000k."pl2000.m3u8' -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamCutPl2000k.$videoNameArr[0]."-%03d.ts' 2>&1")) {
//                     $steps = $steps+2;
//                     $sql->_update_steps_shell_tasks($steps, $id);
//                     shell_exec("chown -R www-data:www-data '".$dirStreamCutPl2000k."*' 2>&1");
//                     $steps = $steps+1;
//                     $sql->_update_steps_shell_tasks($steps, $id);
//                     $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video14['src_quad']." -v quiet -show_format -show_streams 2>&1");
//                     $steps = $steps+1;
//                     $sql->_update_steps_shell_tasks($steps, $id);
//                     $parseInfoArr = explode(PHP_EOL, $parseInfoString);
//                     $vW = explode('=', $parseInfoArr[9]);
//                     $vH = explode('=', $parseInfoArr[10]);
//                     $p22 = $vW[1].'x'.$vH[1];
                    
//                     $file = __ROOT__.'/UserFiles/'.$video14["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';
// $playlist = '#EXTM3U
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// cut/500/pl500.m3u8?id='.$video14["user_id"].'/v/'.$videoNameArr[0].'
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// cut/1000/pl1000.m3u8?id='.$video14["user_id"].'/v/'.$videoNameArr[0].'
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=2032924,BANDWIDTH=2047668,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// cut/2000/pl2000.m3u8?id='.$video14["user_id"].'/v/'.$videoNameArr[0];
                    
//                     file_put_contents($file, $playlist);
//                     // меняем юзера и группу плейлиста
//                     chown($file, 'www-data');
//                     chgrp($file, 'www-data');

//                     $video14Complete = $sql->_update6_shell6_tasks_shell_tasks($id);

//                     $video = $sql->_select2X2000_shell_tasks_shell_tasks($id);
//                     $shellId = $id;        
//                     $uid = $video["user_id"];
//                     $nameid = array_reverse(explode('/', explode('.', $video['m3u8'])[0]))[0];
//                     $ext = explode('.', $video['src_origin'])[1];

                    
//                     $del2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$nameid.'/'.$nameid.'-2000k.'.$ext;
//                     $del2000cut = __ROOT__.'/UserFiles/'.$uid.'/cut/2000k/'.$nameid.'/'.$nameid.'-q2000k.'.$ext;
//                     // удаление
//                     if(file_exists($del2000)) {
//                         unlink($del2000);
//                         unlink($del2000cut);
//                     }

//         //------------ Пути для сохранения
//                     // плейлист для стандартного размера
//                     $playlist1 = __ROOT__.$video['m3u8'];
//                     // файлы для плейлиста 1
//                     $pl1FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$nameid;
//                     $pl1FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$nameid;
//                     $pl1FilesDir2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$nameid;
//                     // постер
//                     $poster = __ROOT__.$video['poster'];

//                     // плейлист для квадратного размера
//                     $playlist2 = __ROOT__.$video['m3u8_quad'];
//                     // файлы для плейлиста 2
//                     $pl2FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/cut/500k/'.$nameid;
//                     $pl2FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/cut/1000k/'.$nameid;
//                     $pl2FilesDir2000 = __ROOT__.'/UserFiles/'.$uid.'/cut/2000k/'.$nameid;
//                     // постер квадрат
//                     $posterQuad = __ROOT__.$video['poster_quad'];

//                     // echo $bucket.'<br>';
//                     // echo $id.'<br>';
//                     // echo $playlist1.'<br>';
//                     // $insert = $s3->putObject([
//                     //     'Bucket' => $bucket,
//                     //     'Key'    => 'i/'.$id,
//                     //     'SourceFile' => $playlist1,
//                     //     'ACL'        => 'public-read'
//                     // ]);

//                     // $origin500k = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$id.'/'.$id.'-500k.'.$ext;
//                     // $origin1000k = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id.'/'.$id.'-1000k.'.$ext;
//                     // $origin2000k = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$id.'/'.$id.'-2000k.'.$ext;

//                     // 
//                     // сохраняем плейлисты  
//                     $s3->putObject(array(
//                         'Bucket'     => $bucket,
//                         'SourceFile' => $playlist1,
//                         'Key'        => $uid.'/v/'.$nameid.'/m.m3u8',
//                         'ACL'        => 'public-read'
//                     ));
//                     $s3->putObject(array(
//                         'Bucket'     => $bucket,
//                         'SourceFile' => $playlist2,
//                         'Key'        => $uid.'/v/'.$nameid.'/q.m3u8',
//                         'ACL'        => 'public-read'
//                     ));

//                     $UploadAWS3 = $s3->uploadDirectory($pl1FilesDir2000, $bucket, $uid.'/v/'.$nameid.'/2000', array(
//                         'concurrency' => 20,
//                         'debug'       => true,
//                         'before' => function (\Aws\Command $command) {
//                         $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
//                             ? 'public-read'
//                             : 'public-read';
//                         }
//                     ));

//                     $UploadAWS6 = $s3->uploadDirectory($pl2FilesDir2000, $bucket, $uid.'/v/'.$nameid.'/cut/2000', array(
//                         'concurrency' => 20,
//                         'debug'       => true,
//                         'before' => function (\Aws\Command $command) {
//                         $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
//                             ? 'public-read'
//                             : 'public-read';
//                         }
//                     ));

//                     // получаем ID видео
//                     // $vidData = $sql->_select_id_shellSave_media_uN($uid, $id);
//                     // $vid = $vidData['id'];
//                     // сохраняем новые minio-пути к плейлистам в базу
// echo '

// конец... 4

// ';
//                     //$mres = $sql->_update_shellSave_media_uN($uid, $a[9], $nameid);
// echo '

// конец...'.$a[9].' 3 id: '.$nameid.'.'.$ext.'

// ';
//                     // очищаем оставшиеся файлы с сервера
                    
//                     // $result = $s3->deleteObject([
//                     //     'Bucket' => $bucket,
//                     //     'Key'    => $nameid.'.'.$ext
//                     // ]);
// echo '

// конец... 2

// ';
//                     unlink($playlist1);
//                     unlink($playlist2);
//                     //unlink($poster);
//                     //unlink($posterQuad);
// echo '

// конец... 1

// ';
//                     RDir($pl1FilesDir500);
// echo '

// конец... 0.9

// ';
//                     RDir($pl1FilesDir1000);
// echo '

// конец... 0.8

// ';
//                     RDir($pl1FilesDir2000);
// echo '

// конец... 0.7

// ';
//                     RDir($pl2FilesDir500);
// echo '

// конец... 0.6

// ';
//                     RDir($pl2FilesDir1000);
// echo '

// конец... 0.5

// ';
//                     RDir($pl2FilesDir2000);
// echo '

// конец... X2000: меняем статус:

// ';
//                     //echo $sql->_update4_status_checkVideoStatus_shell_tasks($shellId);
//                     echo '---uid-'.$uid;
//                     echo '---vid-'.$vid;
//                     echo '---shellId-'.$shellId;
//                     $sql->_statusSwitch_steps_shell_tasks('7', $uid, $a[9], $shellId);
            
// echo '

// -----------------------------------------------------

// ';

                                            
//                 }
//             } else {
//                 echo 'оригинал еще не готов';
//             }
//         }
//     }
    


    
?>