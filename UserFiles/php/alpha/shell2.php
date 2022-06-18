<?php
namespace ConverterShellTwo;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellTwo {

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
        //$logDir = __ROOT__.'/shellLog/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755);
            // меняем юзера и группу папки логов
            chown($logDir, 'www-data');
            chgrp($logDir, 'www-data');
        }

		$video3 = $sql->_select2_shell_tasks_shell_tasks($id);
        
        $steps = $video3['steps'];

        if($video3['src_noblack'] != '' && $video3['src_noblack'] != 'in work' && $video3['src_500'] == '') {
            $lines = file($logDir.'/step1.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step1End = strpos($lastLine, 'end');
                if($step1End !== false) {
                // ШАГ 3: ---------------------------------------------------
                  // Запускаем генерацию видео под поток 500К битрейт (16:9) --
                    // ----------------------------------------------------------
                    $resStartGen500 = $sql->_update_shell2_tasks_shell_tasks($id);

                    $dir500k1 = __ROOT__.'/UserFiles/'.$video3['user_id'].'/500k/';
                    if (!file_exists($dir500k1)) {
                        mkdir($dir500k1, 0755);
                        // меняем юзера и группу папки
                        chown($dir500k1, 'www-data');
                        chgrp($dir500k1, 'www-data');
                    }
                    $dir500k = __ROOT__.'/UserFiles/'.$video3['user_id'].'/500k/'.$videoNameArr[0].'/';
                    if (!file_exists($dir500k)) {
                        mkdir($dir500k, 0755);
                        // меняем юзера и группу папки
                        chown($dir500k, 'www-data');
                        chgrp($dir500k, 'www-data');
                    }
// $file = __ROOT__.'/UserFiles/run.txt';
// file_put_contents($file, 'Запускаем генерацию видео под поток 500К битрейт (16:9) ', FILE_APPEND);     
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);               
                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step3.txt' -i ".__ROOT__.$video3['src_noblack']." -acodec copy -vb 500K ".$dir500k.$videoNameArr[0]."-500k.".$videoNameArr[1]." 2>&1")) {
                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        // меняем юзера и группу лога шага
                        chown($logDir."/step3.txt", 'www-data');
                        chgrp($logDir."/step3.txt", 'www-data');
                        // меняем юзера и группу папки логов
                        chown($dir500k.$videoNameArr[0]."-500k.".$videoNameArr[1], 'www-data');
                        chgrp($dir500k.$videoNameArr[0]."-500k.".$videoNameArr[1], 'www-data');

                        $src_500 = '/UserFiles/'.$video3['user_id'].'/500k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-500k.'.$videoNameArr[1];

                        $resEndGen500 = $sql->_update2_shell2_tasks_shell_tasks($src_500, $id);

                        $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '3');

                    }
                }
            }
        }

        $video4 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video4['steps'];

        if($video4['src_quad'] != '' && $video4['src_quad'] != 'in work' && $video4['src_quad_500'] == '') {
            $lines = file($logDir.'/step2.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step2End = strpos($lastLine, 'end');
                if($step2End !== false) {
                    // Дополнительно вырезаем превью 1:1
                    //shell_exec("ffmpeg -i ".__ROOT__.$video4['src_quad']." -qscale:v 1 -ss 00:00:00 -vframes 1 /root/php/UserFiles/thumbnailForVideo/".$videoNameArr[0]."cut.jpeg 2>&1");            
                    // ШАГ 4: --------------------------------------------------
                    // Запускаем генерацию видео под поток 500К битрейт (1:1) --
                    // ---------------------------------------------------------
                    $resStartGen500q = $sql->_update3_shell2_tasks_shell_tasks($id);

                    $dirQuad500k1 = __ROOT__.'/UserFiles/'.$video4['user_id'].'/500k/cut/';
                    if (!file_exists($dirQuad500k1)) {
                        mkdir($dirQuad500k1, 0755);
                        // меняем юзера и группу папки
                        chown($dirQuad500k1, 'www-data');
                        chgrp($dirQuad500k1, 'www-data');
                    }

                    $dirQuad500k = __ROOT__.'/UserFiles/'.$video4['user_id'].'/500k/cut/'.$videoNameArr[0].'/';
                    if (!file_exists($dirQuad500k)) {
                        mkdir($dirQuad500k, 0755);
                        // меняем юзера и группу папки
                        chown($dirQuad500k, 'www-data');
                        chgrp($dirQuad500k, 'www-data');
                    }

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step4.txt' -i ".__ROOT__.$video4['src_quad']." -acodec copy -vb 500K ".$dirQuad500k.$videoNameArr[0]."-q500k.".$videoNameArr[1]." 2>&1")) {

                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);

                        // меняем юзера и группу лога шага
                        chown($logDir."/step4.txt", 'www-data');
                        chgrp($logDir."/step4.txt", 'www-data');
                        // меняем юзера и группу папки логов
                        chown($dirQuad500k.$videoNameArr[0]."-q500k.".$videoNameArr[1], 'www-data');
                        chgrp($dirQuad500k.$videoNameArr[0]."-q500k.".$videoNameArr[1], 'www-data');

                        $src_quad_500 = '/UserFiles/'.$video4['user_id'].'/500k/cut/'.$videoNameArr[0].'/'.$videoNameArr[0].'-q500k.'.$videoNameArr[1];
                        $resEndGen500q = $sql->_update4_shell2_tasks_shell_tasks($src_quad_500, $id);
                        
                        $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '4');

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