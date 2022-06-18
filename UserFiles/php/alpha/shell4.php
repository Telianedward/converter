<?php
namespace ConverterShellFour;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellFour {

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

        $video7 = $sql->_select2_shell_tasks_shell_tasks($id);
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

                    }
                }
            }
        }
        
        $video8 = $sql->_select2_shell_tasks_shell_tasks($id);
        $steps = $video8['steps'];
        
        if($video8['src_quad'] != '' && $video8['src_quad'] != 'in work' && $video8['src_quad_1000'] == '') {
            $lines = file($logDir.'/step2.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step2End = strpos($lastLine, 'end');
                if($step2End !== false) {
                // ШАГ 8: ---------------------------------------------------
                  // Запускаем генерацию видео под поток 1000К битрейт (1:1) --
                    // ----------------------------------------------------------
                    $resStartGen1000q = $sql->_update3_shell4_tasks_shell_tasks($id);

                    $dirQuad1000k1 = __ROOT__.'/UserFiles/'.$video8["user_id"].'/cut/1000k/';
                    if (!file_exists($dirQuad1000k1)) {
                        mkdir($dirQuad1000k1, 0755);
                        // меняем юзера и группу папки
                        chown($dirQuad1000k1, 'www-data');
                        chgrp($dirQuad1000k1, 'www-data');
                    }
                    $dirQuad1000k = __ROOT__.'/UserFiles/'.$video8["user_id"].'/cut/1000k/'.$videoNameArr[0].'/';
                    if (!file_exists($dirQuad1000k)) {
                        mkdir($dirQuad1000k, 0755);
                        // меняем юзера и группу папки
                        chown($dirQuad1000k, 'www-data');
                        chgrp($dirQuad1000k, 'www-data');
                    }
                    $steps = $steps+2;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step8.txt' -i ".__ROOT__.$video8['src_quad']." -acodec copy -vb 1000K ".$dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1]." 2>&1")) {
                        $steps = $steps+2;
                        $sql->_update_steps_shell_tasks($steps, $id);
                        // меняем юзера и группу лога шага
                        chown($logDir."/step8.txt", 'www-data');
                        chgrp($logDir."/step8.txt", 'www-data');
                        // меняем юзера и группу папки логов
                        chown($dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1], 'www-data');
                        chgrp($dirQuad1000k.$videoNameArr[0]."-q1000k.".$videoNameArr[1], 'www-data');

                        $src_quad_1000 = '/UserFiles/'.$video8["user_id"].'/cut/1000k/'.$videoNameArr[0].'/'.$videoNameArr[0].'-q1000k.'.$videoNameArr[1];
                        $resEndGen1000q = $sql->_update4_shell4_tasks_shell_tasks($src_quad_1000, $id);

                        $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '8');

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