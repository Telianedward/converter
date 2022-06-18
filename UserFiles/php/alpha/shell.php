<?php
namespace ConverterShellOne;
use requests_dev\sql_dev;
use Aws\S3\S3Client;

class ShellOne {

    public function __construct() {

for ($times = 0; $times < 12; ++$times) {

    $videos = array();

    $res = $sql->_select_shell_tasks_shell_tasks();

    if(empty($res)) {
        sleep(4);
        continue;
    }
    
    foreach($res as $video) {

        $steps = $video['steps'];

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $video['src_origin']
        ]);
        $request = $s3->createPresignedRequest($cmd, '+9999 seconds');
        echo $imgPath = (string)$request->getUri();

        $videos[] = $video;
        $id = $video['id'];
        $videoName = array_reverse(explode('/', $video['src_origin']))[0];
        $videoNameArr = explode('.', $videoName);
        $workDir = __ROOT__.'/UserFiles/'.$video['user_id'];
        if (!file_exists($workDir)) {
            mkdir($workDir, 0755);
            // chown($workDir, 'root');
            // chgrp($workDir, 'root');
        }
        $logDir = __ROOT__.'/shellLog/'.$videoNameArr[0];
        if (!file_exists($logDir)) {
            // место которое выполняется разово, очень подойдет для выдачи прав оригинальному файлу
            // chown(__ROOT__.$video['src_origin'], 'root');
            // chgrp(__ROOT__.$video['src_origin'], 'root');

            mkdir($logDir, 0755);
            // меняем юзера и группу папки логов
            chown($logDir, 'root');
            chgrp($logDir, 'root');
        }
        $inwork = false;
        for($i = 1; $i<=12; $i++) {
            if($i == 5 || $i == 6 || $i == 9 || $i == 10) continue;
            if (file_exists($logDir.'/step'.$i.'.txt')) {
                $ls = file($logDir.'/step'.$i.'.txt');
                if(count($ls) > 0) {
                    $ll = $ls[count($ls)-1];
                    $stepEnd = strpos($ll, 'end');
                    if($stepEnd === false) {
                        $inwork = true;
                    }
                }
            }
        }
        // if(!$inwork && $video['m3u8'] != '' && $video['m3u8_quad'] != '' && $video['m3u8'] != 'in work' && $video['m3u8_quad'] != 'in work') {
        //     $resCheckCompleted = mysql_query('
        //         UPDATE 
        //             shell_tasks
        //         SET
        //             status = "complete"
        //         WHERE
        //             id = '.$id.'
        //     ;')
        //     or die("Could not connect : " . mysql_error());
            
        //     continue;
        // }

        $video1 = $sql->_select2_shell_tasks_shell_tasks($id);

        $steps = $video1['steps'];
        // echo '<pre>';
        // print_r($video1);
        // echo '</pre>';

        $ext = strtolower(pathinfo($video['src_origin'], PATHINFO_EXTENSION));
        if($ext == 'm4v') {
            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);

            if(shell_exec('ffmpeg -y -i "'.$imgPath.'" "'.__ROOT__.'/UserFiles/'.$video['user_id'].'/'.$videoNameArr[0]."/".$videoNameArr[0].'.mp4" 2>&1')) {
                // меняем юзера и группу папки
                chown(__ROOT__."/UserFiles/".$video1['user_id']."/".$videoNameArr[0]."/".$videoNameArr[0].".mp4", 'root');
                chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/".$videoNameArr[0]."/".$videoNameArr[0].".mp4", 'root');
                $resM4V = mysql_query('
                    UPDATE 
                        shell_tasks
                    SET
                        src_origin = "/UserFiles/'.$video1['user_id'].'/'.$videoNameArr[0]."/".$videoNameArr[0].'.mp4"
                    WHERE
                        id = '.$id.'
                ;')
                or die("Could not connect : " . mysql_error());
                continue;
            }
            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);
        }
        // проверяем состояние видео
        if($video1['src_noblack'] == '') {
            //echo ' шаг1 ';
        // ШАГ 1: -------------------------------------------
          // Запускаем первую обработку (уменьшаем до 1280) ---
            // --------------------------------------------------
            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);
            // получаем данные:
            $parseInfoString = shell_exec("ffprobe '".$imgPath."' -v quiet -show_format -show_streams 2>&1");

            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);

            $parseInfoArr = explode(PHP_EOL, $parseInfoString);
            $w = explode('=', $parseInfoArr[9]);
            $h = explode('=', $parseInfoArr[10]);
            $vW = $w[1];
            $vH = $h[1];
            if($vW > 1280) {
                $scaleString = "scale=1280:-2";
            } else {
                $scaleString = "scale=".$vW.":".$vH;
                //$scaleString = "scale=".$vW.":-2";
            }

            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);

            $parseInfo = shell_exec("ffmpeg -i '".$imgPath."' -hide_banner 2>&1");

            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);

            $bitrate = $this->preg_match_between('bitrate: ', ' kb\/s', $parseInfo)."K";
            $resStartGen = $sql->_update2_shell_tasks_shell_tasks($bitrate, $id);

            $gDir = __ROOT__.'/UserFiles/'.$video["user_id"].'/generated/';
            if (!file_exists($gDir)) {
                mkdir($gDir, 0755);
                // меняем юзера и группу папки
                chown($gDir, 'root');
                chgrp($gDir, 'root');
            }
            $steps = $steps+1;
            $sql->_update_steps_shell_tasks($steps, $id);
            // обрезаем
            if(shell_exec('ffmpeg -y -progress "'.$logDir.'/step1.txt" -i "'.$imgPath.'" -vf "'.$scaleString.'" -vb '.$bitrate.' '.__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1].' 2>&1')) {
                $steps = $steps+1;
                $sql->_update_steps_shell_tasks($steps, $id);
                // меняем юзера и группу лога шага
                chown($logDir."/step1.txt", 'root');
                chgrp($logDir."/step1.txt", 'root');
                // меняем юзера и группу файла
                chown(__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1], 'root');
                chgrp(__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1], 'root');

                $src_noblack = '/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1];
                $resEndGen = $sql->_update3_shell_tasks_shell_tasks($src_noblack, $id);

                $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '1');

            }
        } else if($video1['src_quad'] == '' && $video1['src_noblack'] != 'in work') {
            //echo 'work';
            $lines = file($logDir.'/step1.txt');
            if(count($lines) > 0) {
                $lastLine = $lines[count($lines)-1];
                $step1End = strpos($lastLine, 'end');
                if($step1End !== false) {
                    
                    if (file_exists($logDir.'/step2.txt')) {

                        $lines2 = file($logDir.'/step2.txt');
                        if(count($lines2) > 0) {
                            $lastLine2 = $lines2[count($lines2)-1];
                            $step2End = strpos($lastLine2, 'end');
                            if($step2End !== false) {
                                continue;
                            }
                        }
                    }
                // ШАГ 2: ---------------------------------
                  // Запускаем вырезание квадратной версии --
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    // ----------------------------------------
                    $parseInfoStringForCrop = shell_exec("ffprobe ".__ROOT__.$video1['src_noblack']." -v quiet -show_format -show_streams 2>&1");

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    $parseInfoArrForCrop = explode(PHP_EOL, $parseInfoStringForCrop);
                    $w = explode('=', $parseInfoArrForCrop[9]);
                    $h = explode('=', $parseInfoArrForCrop[10]);
                    if($w[1] > $h[1]) {
                        $newW = $h[1];
                        $newH = $h[1];
                    } else {
                        $newW = $w[1];
                        $newH = $w[1];
                    }
                    
                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);
                    
                    $parseInfo = shell_exec("ffmpeg -i ".__ROOT__.$video1['src_noblack']." -t 1 -vf cropdetect -f null - 2>&1 | awk '/crop/ { print \$NF }' | tail -1 2>&1");

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    $pos = strpos($parseInfo, 'crop=');
                    $len = strlen($parseInfo);
                    $rest = substr($parseInfo, $pos, $len);
                    $clearRes = str_replace('crop=', '', $rest);
                    $cArr = explode(':', $clearRes);
                    if ($cArr[0] > $cArr[1]) {
                        //$secondSize = "crop=".$newW.":".$newH.":".((($w[1]/1)-($newH/1))/2).":".$cArr[3];
                        $secondSize = "crop=".$newW.":".$newH.":".((($w[1]/1)-($newH/1))/2).":0";
                    } else {
                        //$secondSize = "crop=".$newW.":".$newH.":".$cArr[2].":".((($h[1]/1)-($newW/1))/2);
                        $secondSize = "crop=".$newW.":".$newH.":0:".((($h[1]/1)-($newW/1))/2);
                    }
                    // echo $parseInfo.' --- ';
                    // echo $secondSize;
                    //$secondSize = $parseInfo;
                    $thumbDir = __ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo";
                    if (!file_exists($thumbDir)) {
                        mkdir($thumbDir, 0755);
                    }

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    shell_exec("ffmpeg -i ".__ROOT__.$video1['src_noblack']." -filter:v '".$secondSize."' -qscale:v 1 -ss 00:00:00 -vframes 1 ".__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/".$videoNameArr[0]."cut.jpeg 2>&1");

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    // меняем юзера и группу картинки quad
                    chown(__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/", 'root');
                    chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/", 'root');
                    chown(__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/".$videoNameArr[0]."cut.jpeg", 'root');
                    chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/".$videoNameArr[0]."cut.jpeg", 'root');

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    shell_exec("ffmpeg -i ".__ROOT__.$video1['src_noblack']." -qscale:v 1 -ss 00:00:00 -vframes 1 ".__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/".$videoNameArr[0].".jpeg 2>&1");

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    // меняем юзера и группу картинки
                    chown(__ROOT__."/UserFiles/".$video1["user_id"]."/thumbnailForVideo/".$videoNameArr[0].".jpeg", 'root');
                    chgrp(__ROOT__."/UserFiles/".$video1["user_id"]."/thumbnailForVideo/".$videoNameArr[0].".jpeg", 'root');

                    $poster_quad = '/UserFiles/'.$video1["user_id"].'/thumbnailForVideo/'.$videoNameArr[0].'cut.jpeg';
                    $poster = '/UserFiles/'.$video1["user_id"].'/thumbnailForVideo/'.$videoNameArr[0].'.jpeg';
                    $resStartCut = $sql->_update4_shell_tasks_shell_tasks($secondSize, $poster_quad, $poster, $id);

                    $cDir = __ROOT__.'/UserFiles/'.$video1["user_id"].'/cut/';
                    if (!file_exists($cDir)) {
                        mkdir($cDir, 0755);
                        // меняем юзера и группу папки
                        chown($cDir, 'root');
                        chgrp($cDir, 'root');
                    }

                    $steps = $steps+1;
                    $sql->_update_steps_shell_tasks($steps, $id);

                    if(shell_exec("ffmpeg -y -progress '".$logDir."/step2.txt' -y -hide_banner -i ".__ROOT__.$video1['src_noblack']." -filter:v '".$secondSize."' ".__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$videoNameArr[0]."cut.".$videoNameArr[1]." 2>&1")) {

                        $steps = $steps+1;
                        $sql->_update_steps_shell_tasks($steps, $id);

                        // меняем юзера и группу лога шага
                        chown($logDir."/step2.txt", 'root');
                        chgrp($logDir."/step2.txt", 'root');
                        // меняем юзера и группу папки
                        chown(__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$videoNameArr[0]."cut.".$videoNameArr[1], 'root');
                        chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$videoNameArr[0]."cut.".$videoNameArr[1], 'root');
                        
                        $src_quad = '/UserFiles/'.$video1["user_id"].'/cut/'.$videoNameArr[0].'cut.'.$videoNameArr[1];
                        $resEndCut = $sql->_update5_shell_tasks_shell_tasks($src_quad, $id);

                        $sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '2');

                    }
                }
            }
        }
    }
    
    sleep(4);
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

    public function preg_match_between($a_sStart, $a_sEnd, $a_sSubject)
    {
        $pattern = '/'. $a_sStart .'(.*?)'. $a_sEnd .'/';
        preg_match ($pattern, $a_sSubject, $result);
        $pattern = '/'. $a_sStart .'/';
        $result = preg_replace ($pattern, '', $result[0]);
        $pattern = '/'. $a_sEnd .'/';
        $result = preg_replace ($pattern, '', $result);
        return $result;
    } 
    
}
?>