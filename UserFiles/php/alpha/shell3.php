<?php
namespace ConverterShellThree;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellThree {

    public function __construct() {

for ($times = 0; $times < 4; ++$times) {

    $videos = array();
     
    $res = $sql->_select_shell_tasks_shell_tasks();

    if(empty($res)) {
        sleep(4);
        continue;
    }
    
    foreach($res as $video) {
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

        $video5 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video5['steps'];

        if(count($video5) > 0 && $video5['src_500'] != '' && $video5['src_500'] != 'in work' && $video5['src_500'] != 'complete' && $video5['m3u8'] == '') {
            $lines = file($logDir.'/step3.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step3End = strpos($lastLine, 'end');
                if($step3End !== false) {
                // ШАГ 5: ------------------------------------------------------------------
                  // Запускаем генерацию потока 500К битрейт (16:9) и мастер плейлиста m3u8 --
                    // -------------------------------------------------------------------------
                    $resStartGen500kStream = $sql->_update_shell3_tasks_shell_tasks($id);

                    $dirStreamPl500k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/500k/'.$videoNameArr[0].'/';
                    $dirStreamPl1000k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/1000k/'.$videoNameArr[0].'/';
                    $dirStreamPl2000k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/2000k/'.$videoNameArr[0].'/';
                    if (!file_exists($dirStreamPl500k)) {
                        mkdir($dirStreamPl500k, 0755);
                        // меняем юзера и группу папки
                        chown($dirStreamPl500k, 'www-data');
                        chgrp($dirStreamPl500k, 'www-data');
                    }

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    if(shell_exec("ffmpeg -y -re -i '".__ROOT__.$video5['src_500']."' -codec copy -map 0 -f segment -segment_list '".$dirStreamPl500k."pl500.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamPl500k.$videoNameArr[0]."-%03d.ts' 2>&1")) {

                        $steps = $steps+2;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        shell_exec("chown -R www-data:www-data '".$dirStreamPl500k."*' 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        // Генератор мастер плейлиста
                        //$parsed = shell_exec("ffmpeg -i ".__ROOT__.$video['src_noblack']." -hide_banner 2>&1");
                        //$p1 = preg_match_between('Stream #0:0', ' \[', $parsed);
                        //$p2 = array_reverse(explode(', ', $p1))[0];
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video5['src_noblack']." -v quiet -show_format -show_streams 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                        $vW = explode('=', $parseInfoArr[9]);
                        $vH = explode('=', $parseInfoArr[10]);
                        $p2 = $vW[1].'x'.$vH[1];

                        $file = __ROOT__.'/UserFiles/'.$video5["user_id"].'/'.$videoNameArr[0].'.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
500/pl500.m3u8?id='.$videoNameArr[0];
                
                        file_put_contents($file, $playlist);
                        // меняем юзера и группу плейлиста
                        chown($file, 'www-data');
                        chgrp($file, 'www-data');

                        $m3u8 = '/UserFiles/'.$video5["user_id"].'/'.$videoNameArr[0].'.m3u8';
                        $resEndGen500kStream = $sql->_update2_shell3_tasks_shell_tasks($m3u8, $id);

                        $video5Complete = $sql->_update3_shell3_tasks_shell_tasks($id);

                    }
                }
            }
        }
        
        $video6 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video6['steps'];

        if(count($video6) > 0 && $video6['src_quad_500'] != '' && $video6['src_quad_500'] != 'in work' && $video6['src_quad_500'] != 'complete' && $video6['m3u8_quad'] == '') {
            $lines = file($logDir.'/step4.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step4End = strpos($lastLine, 'end');
                if($step4End !== false) {
                // ШАГ 6: ------------------------------------------------------------------
                  // Запускаем генерацию потока 500К битрейт (1:1) и мастер плейлиста m3u8 ---
                    // -------------------------------------------------------------------------
                    $resStartGen500kStream = $sql->_update4_shell3_tasks_shell_tasks($id);

                    $uploaddirCut = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/';
                    $udirCut = substr($uploaddirCut, 0, -1);
                    if (!file_exists($udirCut)) {
                        mkdir($udirCut, 0755);
                    }
                    $uploaddirCut500 = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/500k/';
                    $udirCut500 = substr($uploaddirCut500, 0, -1);
                    if (!file_exists($udirCut500)) {
                        mkdir($udirCut500, 0755);
                    }
                    $uploaddirCut1000 = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/1000k/';
                    $udirCut1000 = substr($uploaddirCut1000, 0, -1);
                    if (!file_exists($udirCut1000)) {
                        mkdir($udirCut1000, 0755);
                    }
                    $uploaddirCut2000 = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/2000k/';
                    $udirCut2000 = substr($uploaddirCut2000, 0, -1);
                    if (!file_exists($udirCut2000)) {
                        mkdir($udirCut2000, 0755);
                    }

                    $dirStreamCutPl500k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/500k/'.$videoNameArr[0].'/';
                    $dirStreamCutPl1000k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/1000k/'.$videoNameArr[0].'/';
                    $dirStreamCutPl2000k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/2000k/'.$videoNameArr[0].'/';
                    if (!file_exists($dirStreamCutPl500k)) {
                        mkdir($dirStreamCutPl500k, 0755);
                        // меняем юзера и группу папки
                        chown($dirStreamCutPl500k, 'www-data');
                        chgrp($dirStreamCutPl500k, 'www-data');
                    }
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    if(shell_exec("ffmpeg -y -re -i ".__ROOT__.$video6['src_quad_500']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl500k."pl500.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamCutPl500k.$videoNameArr[0]."-%03d.ts' 2>&1")) {
                        $steps = $steps+2;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        shell_exec("chown -R www-data:www-data '".$dirStreamCutPl500k."*' 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        // Генератор мастер плейлиста
                        // $parser = shell_exec("ffmpeg -i ".__ROOT__.$video['src_quad']." -hide_banner 2>&1");
                        // $p11 = preg_match_between('Stream #0:0', ' \[', $parser);
                        // $p22 = array_reverse(explode(', ', $p11))[0];
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video6['src_quad']." -v quiet -show_format -show_streams 2>&1");
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                        $vW = explode('=', $parseInfoArr[9]);
                        $vH = explode('=', $parseInfoArr[10]);
                        $p22 = $vW[1].'x'.$vH[1];

                        $file = __ROOT__.'/UserFiles/'.$video6["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/500/pl500.m3u8?id='.$videoNameArr[0];
                        file_put_contents($file, $playlist);
                        // меняем юзера и группу плейлиста
                        chown($file, 'www-data');
                        chgrp($file, 'www-data');
                        $m3u8_quad = '/UserFiles/'.$video6["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';
                        $resEndGen500kStream = $sql->_update5_shell3_tasks_shell_tasks($m3u8_quad, $id);

                        $video6Complete = $sql->_update3_shell3_tasks_shell_tasks($id);

                    }
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