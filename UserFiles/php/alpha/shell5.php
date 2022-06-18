<?php
namespace ConverterShellFive;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellFive {

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

        $video9 = $sql->_select2_shell_tasks_shell_tasks($id);
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
                    shell_exec("ffmpeg -y -re -i ".__ROOT__.$video9['src_1000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamPl1000k."pl1000.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamPl1000k.$videoNameArr[0]."-%03d.ts' 2>&1");
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

                    $file = __ROOT__.'/UserFiles/'.$video9["user_id"].'/'.$videoNameArr[0].'.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
500/pl500.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
1000/pl1000.m3u8?id='.$videoNameArr[0];
                
                    file_put_contents($file, $playlist);
                    // меняем юзера и группу плейлиста
                    chown($file, 'www-data');
                    chgrp($file, 'www-data');

                    $video9Complete = $sql->_update_shell5_tasks_shell_tasks($id);

                }
            }
        }
        
        $video10 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video10['steps'];
        
        if($video10['src_quad_1000'] != '' && $video10['src_quad_1000'] != 'in work' && $video10['src_quad_1000'] != 'complete' && !file_exists(__ROOT__.'/UserFiles/'.$video10["user_id"].'/cut/1000k/'.$videoNameArr[0].'/pl1000.m3u8')) {
            $lines = file($logDir.'/step8.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step8End = strpos($lastLine, 'end');
                if($step8End !== false) {
                // ШАГ 10: -----------------------------------------
                  // Запускаем генерацию потока 1000К битрейт (1:1) --
                    // -------------------------------------------------

                    $dirStreamCutPl1000k = __ROOT__.'/UserFiles/'.$video10["user_id"].'/cut/1000k/'.$videoNameArr[0].'/';
                    if (!file_exists($dirStreamCutPl1000k)) {
                        mkdir($dirStreamCutPl1000k, 0755);
                        // меняем юзера и группу папки
                        chown($dirStreamCutPl1000k, 'www-data');
                        chgrp($dirStreamCutPl1000k, 'www-data');
                    }
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    shell_exec("ffmpeg -y -re -i ".__ROOT__.$video10['src_quad_1000']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl1000k."pl1000.m3u8' -segment_list_flags +live -segment_time 10 '".$dirStreamCutPl1000k.$videoNameArr[0]."-%03d.ts' 2>&1");
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    shell_exec("chown -R www-data:www-data '".$dirStreamCutPl1000k."*' 2>&1");
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video10['src_quad']." -v quiet -show_format -show_streams 2>&1");
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                    $vW = explode('=', $parseInfoArr[9]);
                    $vH = explode('=', $parseInfoArr[10]);
                    $p22 = $vW[1].'x'.$vH[1];

                    $file = __ROOT__.'/UserFiles/'.$video10["user_id"].'/'.$videoNameArr[0].'-quad.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/500/pl500.m3u8?id='.$videoNameArr[0].'
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=1031127,BANDWIDTH=1047126,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/1000/pl1000.m3u8?id='.$videoNameArr[0];
            
                    file_put_contents($file, $playlist);
                    // меняем юзера и группу плейлиста
                    chown($file, 'www-data');
                    chgrp($file, 'www-data');

                    $video10Complete = $sql->_update2_shell5_tasks_shell_tasks($id);
                    
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