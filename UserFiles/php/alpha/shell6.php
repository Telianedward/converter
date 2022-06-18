<?php
namespace ConverterShellSix;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellSix {

    public function __construct() {

for ($times = 0; $times < 4; ++$times) {
    echo '###'.$times.'###';
    $videos = array();
    
    $res = $sql->_select_shell_tasks_shell_tasks();
    echo 'запуск 
    
';
    if(empty($res)) {
        sleep(4);
        continue;
    }
    echo 'запуск 2
    
';
    foreach($res as $video) {
        echo 'запуск 3
    
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

        $video11 = $sql->_select2_shell_tasks_shell_tasks($id);
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

        $video12 = $sql->_select2_shell_tasks_shell_tasks($id);
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
        
        $video13 = $sql->_select2_shell_tasks_shell_tasks($id);
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
                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step13.txt' -re -i ".__ROOT__.$video13['src_2000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamPl2000k."pl2000.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamPl2000k.$videoNameArr[0]."-%03d.ts' 2>&1")) {
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
500/pl500.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
1000/pl1000.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=2032924,BANDWIDTH=2047668,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
2000/pl2000.m3u8?id='.$videoNameArr[0];
                        
                        file_put_contents($file, $playlist);
                        // меняем юзера и группу плейлиста
                        chown($file, 'www-data');
                        chgrp($file, 'www-data');

                        $video13Complete = $sql->_update5_shell6_tasks_shell_tasks($id);
                        
                    }
                }
            }
        }
        
        $video14 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video14['steps'];
        
        echo '-'.count($video14).'- ';
        print_r($video14);
        if(count($video14) > 0 && $video14['src_quad_2000'] != '' && $video14['src_quad_2000'] != 'complete' && $video14['src_quad_2000'] != 'in work' && $video14['src_quad_2000'] != 'stream') {
            $lines = file($logDir.'/step12.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step12End = strpos($lastLine, 'end');
                if($step12End !== false) {
                    
                $video14StreamGenStart = $sql->_update7_shell6_tasks_shell_tasks($id);
                // ШАГ 14: -----------------------------------------
                  // Запускаем генерацию потока 2000К битрейт (1:1) --
                    // -------------------------------------------------
                    $dirStreamCutPl2000k = __ROOT__.'/UserFiles/'.$video14["user_id"].'/cut/2000k/'.$videoNameArr[0].'/';
                    if (!file_exists($dirStreamCutPl2000k)) {
                        mkdir($dirStreamCutPl2000k, 0755);
                        // меняем юзера и группу папки
                        chown($dirStreamCutPl2000k, 'www-data');
                        chgrp($dirStreamCutPl2000k, 'www-data');
                    }
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step14.txt' -re -i ".__ROOT__.$video14['src_quad_2000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl2000k."pl2000.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamCutPl2000k.$videoNameArr[0]."-%03d.ts' 2>&1")) {
                        $steps = $steps+2;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        shell_exec("chown -R www-data:www-data '".$dirStreamCutPl2000k."*' 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video14['src_quad']." -v quiet -show_format -show_streams 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                        $vW = explode('=', $parseInfoArr[9]);
                        $vH = explode('=', $parseInfoArr[10]);
                        $p22 = $vW[1].'x'.$vH[1];

                        $file = __ROOT__.'/UserFiles/'.$video14["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/500/pl500.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/1000/pl1000.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=2032924,BANDWIDTH=2047668,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/2000/pl2000.m3u8?id='.$videoNameArr[0];
                        
                        file_put_contents($file, $playlist);
                        // меняем юзера и группу плейлиста
                        chown($file, 'www-data');
                        chgrp($file, 'www-data');

                        $video14Complete = $sql->_update6_shell6_tasks_shell_tasks($id);
                                                
                    }
                } else {
                    echo 'оригинал еще не готов';
                }
            }
        }
        

    }

    sleep(12);
}
// $file = __ROOT__.'/UserFiles/shell_log.txt';
// // Открываем файл для получения существующего содержимого
// $current = file_get_contents($file);
// // Добавляем нового человека в файл
// $current = "<pre>".$videos[0][3]."</pre>";
// // Пишем содержимое обратно в файл
// if(file_put_contents($file, $current)) {
//     echo 'put done \n';
// }
    }
}
?>