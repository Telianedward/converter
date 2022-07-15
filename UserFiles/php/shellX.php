<?php
namespace ConverterShellX;

ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
ini_set("max_execution_time", 300000000);
set_time_limit(300000000);
date_default_timezone_set('Europe/Moscow');
define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');

include_once( __ROOT__ .'/var.php' );
include_once( __ROOT__ .'/config_cron.ini');
include_once( __ROOT__ .'/apps/namespace/all/pdo.php');
include_once( __ROOT__ .'/apps/namespace/all/sql_dev.php');
include_once( __ROOT__ .'/vendor/S3/autoload.php');

use requests_dev\sql_dev;
use Aws\S3\S3Client;
use \parallel\Runtime;
use minio;

class ShellX {

    public $args;
    public $runtime;
    public $return;

    public function __construct($id) {

        $this->sql = new sql_dev();


        $minioConfig = [minio\VERSION, minio\REGION, minio\PORT, minio\LOGIN, minio\PASS];

        $this->s3 = new S3Client([
            'version' => minio\VERSION,
            'region'  => minio\REGION,
            'endpoint' => 'http://minio.vhst.ru:'.minio\PORT,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => minio\LOGIN,
                'secret' => minio\PASS,
            ],
        ]);


        // $videos = array();

        // $res = $this->sql->_select_shell_tasks_shell_tasks();


    
        
            $video = $this->sql->_select2_shell_tasks_shell_tasks($id);
            // print_r($video);
            // exit();
            $key = explode('.', explode('/', $video['src_origin'])[2])[0];
            $sid = $video['id'];
            $uid = $video['user_id'];

            $this->bucket = 'us'.$uid;
            
            $mid = $this->sql->_select_id_shellSave_media_uN($uid, $key);
            $this->sql->_statusSwitch_steps_shell_tasks('1', $uid, $mid, $sid);
            $this->sql->_insert_shell_tasks_shell_steps($key, '0');
        echo '
        
запуск...

        ';
        
        $steps = $video['steps'];

        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $video['src_origin']
        ]);
        $request = $this->s3->createPresignedRequest($cmd, '+9999 seconds');
        $imgPath = (string)$request->getUri();

        $videos[] = $video;
        $id = $video['id'];
        $videoName = array_reverse(explode('/', $video['src_origin']))[0];
        $videoNameArr = explode('.', $videoName);
        $workDir = __ROOT__.'/UserFiles/'.$video['user_id'];
        if (!file_exists($workDir)) {
            mkdir($workDir, 0755);
            // chown($workDir, 'www-data');
            // chgrp($workDir, 'www-data');
        }
        $logDir = __ROOT__.'/shellLog/'.$videoNameArr[0];
        if (!file_exists($logDir)) {
            // место которое выполняется разово, очень подойдет для выдачи прав оригинальному файлу
            // chown(__ROOT__.$video['src_origin'], 'www-data');
            // chgrp(__ROOT__.$video['src_origin'], 'www-data');

            mkdir($logDir, 0755);
            // меняем юзера и группу папки логов
            chown($logDir, 'www-data');
            chgrp($logDir, 'www-data');
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

        $video1 = $this->sql->_select2_shell_tasks_shell_tasks($id);

        $steps = $video1['steps'];
        // echo '<pre>';
        // print_r($video1);
        // echo '</pre>';

        $ext = strtolower(pathinfo($video['src_origin'], PATHINFO_EXTENSION));
        // if($ext == 'm4v') {
        //     $steps = $steps+1;
        //     $this->sql->_update_steps_shell_tasks($steps, $id);

        //     if(shell_exec('ffmpeg -y -i "'.$imgPath.'" "'.__ROOT__.'/UserFiles/'.$video['user_id'].'/'.$videoNameArr[0]."/".$videoNameArr[0].'.mp4" 2>&1')) {
        //         // меняем юзера и группу папки
        //         chown(__ROOT__."/UserFiles/".$video1['user_id']."/".$videoNameArr[0]."/".$videoNameArr[0].".mp4", 'www-data');
        //         chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/".$videoNameArr[0]."/".$videoNameArr[0].".mp4", 'www-data');
        //         $resM4V = mysql_query('
        //             UPDATE 
        //                 shell_tasks
        //             SET
        //                 src_origin = "/UserFiles/'.$video1['user_id'].'/'.$videoNameArr[0]."/".$videoNameArr[0].'.mp4"
        //             WHERE
        //                 id = '.$id.'
        //         ;')
        //         or die("Could not connect : " . mysql_error());
        //         continue;
        //     }
        //     $steps = $steps+1;
        //     $this->sql->_update_steps_shell_tasks($steps, $id);
        // }


echo '

запуск... 2

        ';

        // проверяем состояние видео
        if($video1['src_noblack'] == '') {
            //echo ' шаг1 ';
        // ШАГ 1: -------------------------------------------
          // Запускаем первую обработку (уменьшаем до 1280) ---
            // --------------------------------------------------
            $steps = $steps+1;
            $this->sql->_update_steps_shell_tasks($steps, $id);
            // получаем данные:
            
            $parseInfoString = shell_exec('ffprobe "'.$imgPath.'" -v quiet -show_format -show_streams 2>&1');
            $cropInfo = shell_exec("ffmpeg -i '".$imgPath."' -t 1 -vf cropdetect -f null - 2>&1 | awk '/crop/ { print \$NF }' | tail -1 2>&1");
            $steps = $steps+1;
            $this->sql->_update_steps_shell_tasks($steps, $id);
            $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '1');
            $parseInfoArr = explode(PHP_EOL, $parseInfoString);

echo '

var_dump findKey -------------------------------------

        ';
        $matches = preg_grep('/^width=(\w+)/i', $parseInfoArr);
        $w = [];
        foreach ($matches as $v) {
            $w[1] = explode('=', $v)[1];
        }

        print_r($w);
        
        $matches2 = preg_grep('/^height=(\w+)/i', $parseInfoArr);
        $h = [];
        foreach ($matches2 as $v) {
            $h[1] = explode('=', $v)[1];
        }
        print_r($h);
echo '

cropInfo -------------------------------------

        ';
            print_r($cropInfo);
echo '

VIDEO INFO -------------------------------------

        ';
            print_r($parseInfoArr);
echo '

VIDEO INFO end -------------------------------------

        ';
            // $w = explode('=', $parseInfoArr[9]);
            // $h = explode('=', $parseInfoArr[10]);
            $vW = $w[1];
            $vH = $h[1];

            $p = ($vW>$vH)?'w':'h';
            if($vW==$vH) $p = 'r';

            $darKeys = preg_grep('/^display_aspect_ratio/i', $parseInfoArr);
            
            foreach($darKeys as $dk) {
                $dar = explode('=', $dk)[1];
            }

            // 90 rotation=90 вертикаль
            // 180 rotation=-180 горизонт
            // 360 горизонт
            // 270 rotation=-90 вертикаль
            $skey = array_search('rotation=90', $parseInfoArr);
            if(empty($skey)) $skey = array_search('rotation=-90', $parseInfoArr);
            if(empty($skey)) $skey = array_search('rotation=-180', $parseInfoArr);
            if(!empty($skey)) {
                $sign = explode('=', $parseInfoArr[$skey])[1];

                if($p == 'w') {
                    if($sign == '90' || $sign == '-90') {
                        $p = 'h';
                        $dar = '9:16';
                    }
                } else if($p == 'h') {
                    if($sign == '90' || $sign == '-90') {
                        $p = 'w';
                        $dar = '16:9';
                    }
                }
            }
            
            // $vData = $this->sql->_select_id_shellSave_media_uN($video1["user_id"], $videoNameArr[0]);
            // $vuid = $vData['id'];
            $sizes = $vW.'x'.$vH;

            $this->sql->_update_sizes_uN($video1['user_id'], $sizes, $p, $dar, $mid);

            $this->sql->_update_sizePriority_shell_tasks($p, $video1["user_id"], $mid);

            $cropWH = explode('=', $cropInfo)[1];
            $cropWHArr = explode(':', $cropWH);
            $cropW = $cropWHArr[0];
            $cropH = $cropWHArr[1];
            if($p=='w' && $cropW<$cropH) {
                $cropInfo = 'crop='.$vW.':'.$vH;
            }
echo '

cropInfo -------------------------------------

        ';
            print_r($cropInfo);

            if($vW > 1280) {
                $scaleString = trim($cropInfo).",scale=1280:-2";
            } else {
                //$scaleString = "scale=".$vW.":".$vH;
                $scaleString = trim($cropInfo).",scale=".$vW.":-2";
            }

            $steps = $steps+1;
            $this->sql->_update_steps_shell_tasks($steps, $id);

            $parseInfo = shell_exec("ffmpeg -i '".$imgPath."' -hide_banner 2>&1");

            $steps = $steps+1;
            $this->sql->_update_steps_shell_tasks($steps, $id);

            $bitrate = $this->preg_match_between('bitrate: ', ' kb\/s', $parseInfo)."K";
            $resStartGen = $this->sql->_update2_shell_tasks_shell_tasks($bitrate, $id);

            $gDir = __ROOT__.'/UserFiles/'.$video["user_id"].'/generated/';
            if (!file_exists($gDir)) {
                mkdir($gDir, 0755);
                // меняем юзера и группу папки
                chown($gDir, 'www-data');
                chgrp($gDir, 'www-data');
            }
            $steps = $steps+1;
            $this->sql->_update_steps_shell_tasks($steps, $id);

echo '

ffmpeg -y -progress "'.$logDir.'/step1.txt" -i "'.$imgPath.'" -vf "'.$scaleString.'" -vb '.$bitrate.' '.__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1].' 2>&1

';
            // обрезаем
            if(shell_exec('ffmpeg -y -progress "'.$logDir.'/step1.txt" -i "'.$imgPath.'" -vf "'.$scaleString.'" -vb '.$bitrate.' -pix_fmt yuv420p '.__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1].' 2>&1')) {
                $steps = $steps+1;
                $this->sql->_update_steps_shell_tasks($steps, $id);
                // меняем юзера и группу лога шага
                chown($logDir."/step1.txt", 'www-data');
                chgrp($logDir."/step1.txt", 'www-data');
                // меняем юзера и группу файла
                chown(__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1], 'www-data');
                chgrp(__ROOT__.'/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1], 'www-data');

                $src_noblack = '/UserFiles/'.$video1["user_id"].'/generated/'.$videoNameArr[0].'nb.'.$videoNameArr[1];
                $resEndGen = $this->sql->_update3_shell_tasks_shell_tasks($src_noblack, $id);

                $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '2');

                $video1 = $this->sql->_select2_shell_tasks_shell_tasks($id);

                if($video1['src_quad'] == '' && $video1['src_noblack'] != 'in work') {
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
                                        echo 'данные для шага не готовы';
                                        exit();
                                    }
                                }
                            }
echo '

запуск... 4

        ';
                        // ШАГ 2: ---------------------------------
                          // Запускаем вырезание превью и блюр версии превью --
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);
                            // ----------------------------------------
                            $parseInfoStringForCrop = shell_exec("ffprobe ".__ROOT__.$video1['src_noblack']." -v quiet -show_format -show_streams 2>&1");
        
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);
        
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
                            $this->sql->_update_steps_shell_tasks($steps, $id);
                            
                            $parseInfo = shell_exec("ffmpeg -i ".__ROOT__.$video1['src_noblack']." -t 1 -vf cropdetect -f null - 2>&1 | awk '/crop/ { print \$NF }' | tail -1 2>&1");
        
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);
                            $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '3');
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
                            // exit();
                            //$secondSize = $parseInfo;
                            $thumbDir = __ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo";
                            if (!file_exists($thumbDir)) {
                                mkdir($thumbDir, 0755);
                            }
        
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);
                            // делаем квадратное превью
                            echo '

-------------  делаем квадратное превью  -----------------

';
                            $dir500k1 = __ROOT__.'/UserFiles/'.$video1['user_id'].'/500k/';
                            if (!file_exists($dir500k1)) {
                                mkdir($dir500k1, 0755);
                                // меняем юзера и группу папки
                                chown($dir500k1, 'www-data');
                                chgrp($dir500k1, 'www-data');
                            }
                            $dir500k = __ROOT__.'/UserFiles/'.$video1['user_id'].'/500k/'.$videoNameArr[0].'/';
                            if (!file_exists($dir500k)) {
                                mkdir($dir500k, 0755);
                                // меняем юзера и группу папки
                                chown($dir500k, 'www-data');
                                chgrp($dir500k, 'www-data');
                            }
                            
                            $this->runtime = new Runtime();
                            $this->args = array();
                            $this->args[0] = __ROOT__;
                            $this->args[1] = __ROOT__.$video1['src_noblack'];
                            $this->args[2] = $video1['user_id'];
                            $this->args[3] = $videoNameArr[0];
                            $this->args[4] = $secondSize;
                            $this->args[5] = $minioConfig;
                            $this->args[6] = $this->bucket;
                            $this->args[7] = $video1['id'];
                            $this->args[8] = $videoNameArr[1];
                            $this->args[9] = $mid;

                            $run1 = function ($a) {
                                define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');
                                echo "

делаем постеры
                                
";
                                $posterQuad = __ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3]."cut.webp";
                                $posterQBlur = __ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3]."cutblur.webp";
                                if(shell_exec("ffmpeg -i ".$a[1]." -filter:v '".$a[4]."' -qscale:v 1 -ss 00:00:00 -vframes 1 -vf scale=iw:ih -codec libwebp -lossless 0 -quality 100 ".$posterQuad." 2>&1")) {
                                    echo "

делаем блюр постеры
                                
";
                                    // меняем юзера и группу картинки quad
                                    // chown(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/", 'www-data');
                                    // chgrp(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/", 'www-data');
                                    // chown(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3]."cut.jpeg", 'www-data');
                                    // chgrp(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3]."cut.jpeg", 'www-data');

                                    // делаем блюр версию
                                    if(shell_exec("convert ".$posterQuad." -blur 0x108 ".$posterQBlur." | echo DonePosterQuadBlur 2>&1")) {
                                        echo "

сохраняем постеры 2
                                
";
                                        include_once( __ROOT__ .'/vendor/S3/autoload.php');
                                        include( __ROOT__ .'/apps/namespace/all/sql_dev.php');
                                        $sql = new sql_dev();
                                        $s3 = new S3Client([
                                            'version' => $a[5][0],
                                            'region'  => $a[5][1],
                                            'endpoint' => 'http://minio.vhst.ru:'.$a[5][2],
                                            'use_path_style_endpoint' => true,
                                            'credentials' => [
                                                'key'    => $a[5][3],
                                                'secret' => $a[5][4],
                                            ],
                                        ]);
                                        echo "

сохраняем постеры 3
                                
";
                                        //$imagick = new \Imagick($posterQuad);
                                        //print_r($imagick);
                                        //$blobQuad = 'data:image/jpg;base64,'.base64_encode($imagick->getImageBlob());
                                        //$blobQuad = $imagick->getImageBlob();

                                        //$res = $sql->_updatePosterQuad_shellX_tasks_shell_tasks($blobQuad, $a[7]);
                                        //$imagick = new Imagick($posterQBlur); 
                                        //$blobQuadBlur = 'data:image/jpg;base64,'.base64_encode($imagick->getImageBlob());

                                        $s3->putObject(array(
                                            'Bucket'     => $a[6],
                                            'SourceFile' => $posterQuad,
                                            'Key'        => 'v/'.$a[3].'/p/q/n/'.$a[3].'.webp',
                                            'ACL'        => 'public-read'
                                        ));
                                        $s3->putObject(array(
                                            'Bucket'     => $a[6],
                                            'SourceFile' => $posterQBlur,
                                            'Key'        => 'v/'.$a[3].'/p/q/b/'.$a[3].'.webp',
                                            'ACL'        => 'public-read'
                                        ));
                                        echo "

сохранили в минио
                                
";
                                        $poster_quad = $a[3];
                                        $res = $sql->_updateQuad_shellSave_tasks_shell_tasks($poster_quad, $a[7]);

                                        unlink($posterQuad);
                                        unlink($posterQBlur);
                                        $sql->_insert_shell_tasks_shell_steps($a[3], '4');
                                        echo "

сохранили в базу
                                
";

                                    }
                        
                                }
                            };
                            //$cl = new Closure;
                            //$run2 = $this->run1();


                            $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '4');
                            //$future1 = $this->runtime->run($run1, [$this->args]);

                            // делаем прямоугольное превью
                            echo '

-------------  делаем прямоугольное превью  -----------------

';
                            $run2 = function ($a) {
                                define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');
                                $poster = __ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3].".webp";
                                $posterBlur = __ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3]."blur.webp";
                                if(shell_exec("ffmpeg -i ".$a[1]." -qscale:v 1 -ss 00:00:00 -vframes 1 -vf scale=iw:ih -codec libwebp -lossless 0 -quality 100 ".$poster." 2>&1")) {
                                    
                                    // меняем юзера и группу картинки quad
                                    chown(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3].".webp", 'www-data');
                                    chgrp(__ROOT__."/UserFiles/".$a[2]."/thumbnailForVideo/".$a[3].".webp", 'www-data');

                                    // делаем блюр версию
                                    if(shell_exec("convert ".$poster." -blur 0x108 ".$posterBlur." | echo DonePosterBlur 2>&1")) {
                                        include_once( __ROOT__ .'/vendor/S3/autoload.php');
                                        include( __ROOT__ .'/apps/namespace/all/sql_dev.php');
                                        $sql = new sql_dev();
                                        $s3 = new S3Client([
                                            'version' => $a[5][0],
                                            'region'  => $a[5][1],
                                            'endpoint' => 'http://minio.vhst.ru:'.$a[5][2],
                                            'use_path_style_endpoint' => true,
                                            'credentials' => [
                                                'key'    => $a[5][3],
                                                'secret' => $a[5][4],
                                            ],
                                        ]);

                                        //$imagick = new \Imagick($poster); 
                                        //$blob = 'data:image/jpg;base64,'.base64_encode($imagick->getImageBlob());
                                        //$blob = $imagick->getImageBlob();
                                        //$res = $sql->_updatePoster_shellX_tasks_shell_tasks($blob, $a[7]);

                                        $s3->putObject(array(
                                            'Bucket'     => $a[6],
                                            'SourceFile' => $poster,
                                            'Key'        => 'v/'.$a[3].'/p/n/n/'.$a[3].'.webp',
                                            'ACL'        => 'public-read'
                                        ));
                                        unlink($poster);

                                        $s3->putObject(array(
                                            'Bucket'     => $a[6],
                                            'SourceFile' => $posterBlur,
                                            'Key'        => 'v/'.$a[3].'/p/n/b/'.$a[3].'.webp',
                                            'ACL'        => 'public-read'
                                        ));
                                        unlink($posterBlur);
                                        

                                        $poster = $a[3];
                                        $res = $sql->_update_shellSave_tasks_shell_tasks($poster, $a[7]);
                                        $sql->_insert_shell_tasks_shell_steps($a[3], '5');

                                    }
                                }
                            };
                            //$cl = new Closure;
                            //$run2 = $this->run1();
                            $this->runtime2 = new Runtime();
                            $future2 = $this->runtime2->run($run2, [$this->args]);

        
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);
                            
                            // $runtime->run(function(){
                            //     shell_exec("ffmpeg -i ".__ROOT__.$video1['src_noblack']." -qscale:v 1 -ss 00:00:00 -vframes 1 ".__ROOT__."/UserFiles/".$video1['user_id']."/thumbnailForVideo/".$videoNameArr[0].".jpeg 2>&1");
                            // });
                            // $runtime->close();

                            // $steps = $steps+1;
                            // $this->sql->_update_steps_shell_tasks($steps, $id);
        
                            // $poster_quad = '/UserFiles/'.$video1["user_id"].'/thumbnailForVideo/'.$videoNameArr[0].'cut.jpeg';
                            // $poster = '/UserFiles/'.$video1["user_id"].'/thumbnailForVideo/'.$videoNameArr[0].'.jpeg';
                            $resStartCut = $this->sql->_update4_shell_tasks_shell_tasks($secondSize, $id);

                            $cDir = __ROOT__.'/UserFiles/'.$video1["user_id"].'/cut/';
                            if (!file_exists($cDir)) {
                                mkdir($cDir, 0755);
                                // меняем юзера и группу папки
                                chown($cDir, 'www-data');
                                chgrp($cDir, 'www-data');
                            }
        
                            $steps = $steps+1;
                            $this->sql->_update_steps_shell_tasks($steps, $id);

                            $run3 = function ($a) {
                                define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');
                                include_once( __ROOT__ .'/vendor/S3/autoload.php');
                                include( __ROOT__ .'/apps/namespace/all/sql_dev.php');
echo '

-------------  run 3  -----------------

';
                                $logDir = __ROOT__.'/shellLog/'.$a[3];
                                $ext = $a[8];
                                $id = $a[7];
                                $uid = $a[2];
                                $sql = new sql_dev();
                                $s3 = new S3Client([
                                    'version' => $a[5][0],
                                    'region'  => $a[5][1],
                                    'endpoint' => 'http://minio.vhst.ru:'.$a[5][2],
                                    'use_path_style_endpoint' => true,
                                    'credentials' => [
                                        'key'    => $a[5][3],
                                        'secret' => $a[5][4],
                                    ],
                                ]);

                                $video3 = $sql->_select2_shell_tasks_shell_tasks($id);
                                $steps = $video3['steps'];

                                if($video3['src_noblack'] != '' && $video3['src_noblack'] != 'in work' && $video3['src_500'] == '') {
echo '

-------------  run 3 if 1  -----------------

';
                                    $lines = file($logDir.'/step1.txt');
                                    if(count($lines) > 0) {
                                        $lastLine = $lines[count($lines)-1];
                                        $step1End = strpos($lastLine, 'end');
                                        if($step1End !== false) {
echo '

-------------  run 3 if 2 3 -----------------

';
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
                                            $dir500k = __ROOT__.'/UserFiles/'.$video3['user_id'].'/500k/'.$a[3].'/';
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
                                            if(shell_exec("ffmpeg -y -progress '".$logDir."/step3.txt' -i ".__ROOT__.$video3['src_noblack']." -acodec copy -vb 500K ".$dir500k.$a[3]."-500k.".$a[8]." 2>&1")) {
echo '

-------------  run 3 if 4  -----------------

';
                                                $steps = $steps+1;
                                                $sql->_update_steps_shell_tasks($steps, $id);
                                                // меняем юзера и группу лога шага
                                                chown($logDir."/step3.txt", 'www-data');
                                                chgrp($logDir."/step3.txt", 'www-data');
                                                // меняем юзера и группу папки логов
                                                chown($dir500k.$a[3]."-500k.".$a[8], 'www-data');
                                                chgrp($dir500k.$a[3]."-500k.".$a[8], 'www-data');

                                                $src_500 = '/UserFiles/'.$video3['user_id'].'/500k/'.$a[3].'/'.$a[3].'-500k.'.$a[8];

                                                $resEndGen500 = $sql->_update2_shell2_tasks_shell_tasks($src_500, $id);

                                                $sql->_insert_shell_tasks_shell_steps($a[3], '6');



                                                $video5 = $sql->_select2_shell_tasks_shell_tasks($id);

                                                if(count($video5) > 0 && $video5['src_500'] != '' && $video5['src_500'] != 'in work' && $video5['src_500'] != 'complete' && $video5['m3u8'] == '') {
                                                    $lines = file($logDir.'/step3.txt');
                                                    if(count($lines) > 0) {
                                                        $lastLine = $lines[count($lines)-1];
                                                        $step3End = strpos($lastLine, 'end');
                                                        if($step3End !== false) {
echo '

-------------  run 3 if 5 6 7  -----------------

';
                                                        // ШАГ 5: ------------------------------------------------------------------
                                                        // Запускаем генерацию потока 500К битрейт (16:9) и мастер плейлиста m3u8 --
                                                            // -------------------------------------------------------------------------
                                                            $resStartGen500kStream = $sql->_update_shell3_tasks_shell_tasks($id);

                                                            $dirStreamPl500k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/500k/'.$a[3].'/';
                                                            $dirStreamPl1000k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/1000k/'.$a[3].'/';
                                                            $dirStreamPl2000k = __ROOT__.'/UserFiles/'.$video5["user_id"].'/2000k/'.$a[3].'/';
                                                            if (!file_exists($dirStreamPl500k)) {
                                                                mkdir($dirStreamPl500k, 0755);
                                                                // меняем юзера и группу папки
                                                                chown($dirStreamPl500k, 'www-data');
                                                                chgrp($dirStreamPl500k, 'www-data');
                                                            }

                                                            $steps = $steps+1;
                                                            $sql->_update_steps_shell_tasks($steps, $id);
                                                            $sql->_insert_shell_tasks_shell_steps($a[3], '9');
                                                            $sql->_statusSwitch_steps_shell_tasks('2', $uid, $a[9], $a[7]);
                                                            // 1> /var/www/php/out/out".$a[3].".txt
                                                            shell_exec("ffmpeg -y -re -i '".__ROOT__.$video5['src_500']."' -codec copy -map 0 -f segment -segment_list '".$dirStreamPl500k."pl500.m3u8' -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamPl500k.$a[3]."-%03d.ts' 1> /var/www/php/out/out".$a[3].".txt 2>&1");
                                                            
                                                            //if($shellRes) {
echo '

-------------  run 3 if 8  -----------------

';
                                                            
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

                                                                $file = __ROOT__.'/UserFiles/'.$video5["user_id"].'/'.$a[3].'.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
500/pl500.m3u8';
                                                        
                                                                file_put_contents($file, $playlist);
                                                                // меняем юзера и группу плейлиста
                                                                chown($file, 'www-data');
                                                                chgrp($file, 'www-data');

                                                                $m3u8 = '/UserFiles/'.$video5["user_id"].'/'.$a[3].'.m3u8';
                                                                $resEndGen500kStream = $sql->_update2_shell3_tasks_shell_tasks($m3u8, $id);
                                                                $playlist1 = __ROOT__.$m3u8;
                                                                $s3->putObject(array(
                                                                    'Bucket'     => $a[6],
                                                                    'SourceFile' => $playlist1,
                                                                    'Key'        => 'v/'.$a[3].'/m.m3u8',
                                                                    'ACL'        => 'public-read'
                                                                ));

                                                                $video5Complete = $sql->_update3_shell3_tasks_shell_tasks($id);

                                                                $del500 = __ROOT__.'/UserFiles/'.$video5["user_id"].'/500k/'.$a[3].'/'.$a[3].'-500k.'.$ext;
                                                                // удаление оригинала
                                                                if(file_exists($del500)) {
                                                                    echo '
                                                                    
                                                                    del500: '.$del500.'

                                                                    ';
                                                                    unlink($del500);
                                                                }

                                                                $pl1FilesDir500 = __ROOT__.'/UserFiles/'.$video5["user_id"].'/500k/'.$a[3];
                                                                $UploadAWS1 = $s3->uploadDirectory($pl1FilesDir500, $a[6], 'v/'.$a[3].'/500', array(
                                                                    'concurrency' => 20,
                                                                    'debug'       => true,
                                                                    'before' => function (\Aws\Command $command) {
                                                                    $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                                                                        ? 'public-read'
                                                                        : 'public-read';
                                                                    }
                                                                ));

                                                                $sql->_statusSwitch_steps_shell_tasks('3', $uid, $a[9], $a[7]);
                                                                $sql->_insert_shell_tasks_shell_steps($a[3], '10');
                                                                $id = $a[7];
                                                                include_once('shellX1000.php');

                                                            //}
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }

                            };
                            //$cl = new Closure;
                            //$run2 = $this->run1();
                            //
                           //
                          // ШАГ 2.1: ---------------------------------
                          $run4 = function ($a) {
echo '

-------------  run 4  -----------------

';
                            // ---------------------------------
                            define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');
                            include_once( __ROOT__ .'/vendor/S3/autoload.php');
                            include( __ROOT__ .'/apps/namespace/all/sql_dev.php');
                            $logDir = __ROOT__.'/shellLog/'.$a[3];
                            $ext = $a[8];
                            $id = $a[7];
                            $sql = new sql_dev();
                            $s3 = new S3Client([
                                'version' => $a[5][0],
                                'region'  => $a[5][1],
                                'endpoint' => 'http://minio.vhst.ru:'.$a[5][2],
                                'use_path_style_endpoint' => true,
                                'credentials' => [
                                    'key'    => $a[5][3],
                                    'secret' => $a[5][4],
                                ],
                            ]);
                            $video1 = $sql->_select2_shell_tasks_shell_tasks($id);
                            $steps = $video1['steps'];
                            // Запускаем вырезание квадратной версии --
                            if(shell_exec("ffmpeg -y -progress '".$logDir."/step2.txt' -y -hide_banner -i ".__ROOT__.$video1['src_noblack']." -filter:v '".$a[4]."' ".__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$a[3]."cut.".$a[8]." 2>&1")) {
echo '

-------------  run 4 if 1  -----------------

';
                                $steps = $steps+1;
                                $sql->_update_steps_shell_tasks($steps, $id);
        
                                // меняем юзера и группу лога шага
                                chown($logDir."/step2.txt", 'www-data');
                                chgrp($logDir."/step2.txt", 'www-data');
                                // меняем юзера и группу папки
                                chown(__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$a[3]."cut.".$a[8], 'www-data');
                                chgrp(__ROOT__."/UserFiles/".$video1['user_id']."/cut/".$a[3]."cut.".$a[8], 'www-data');
                                
                                $src_quad = '/UserFiles/'.$video1["user_id"].'/cut/'.$a[3].'cut.'.$a[8];
                                $resEndCut = $sql->_update5_shell_tasks_shell_tasks($src_quad, $id);
        
                                $sql->_insert_shell_tasks_shell_steps($a[3], '7');

                                

                                $video4 = $sql->_select2_shell_tasks_shell_tasks($id);
                                $steps = $video4['steps'];

                                if($video4['src_quad'] != '' && $video4['src_quad'] != 'in work' && $video4['src_quad_500'] == '') {
                                    $lines = file($logDir.'/step2.txt');
                                    if(count($lines) > 0) {
                                        $lastLine = $lines[count($lines)-1];
                                        $step2End = strpos($lastLine, 'end');
                                        if($step2End !== false) {
echo '

-------------  run 4 if 2 3 4  -----------------

';
                                            // Дополнительно вырезаем превью 1:1
                                            //shell_exec("ffmpeg -i ".__ROOT__.$video4['src_quad']." -qscale:v 1 -ss 00:00:00 -vframes 1 /root/php/UserFiles/thumbnailForVideo/".$videoNameArr[0]."cut.jpeg 2>&1");            
                                            // ШАГ 4: --------------------------------------------------
                                            // Запускаем генерацию видео под поток 500К битрейт (1:1) --
                                            // ---------------------------------------------------------
                                            $resStartGen500q = $sql->_update3_shell2_tasks_shell_tasks($id);
                                            $dirStreamPl500k = __ROOT__.'/UserFiles/'.$video4["user_id"].'/500k/';
                                            if (!file_exists($dirStreamPl500k)) {
                                                mkdir($dirStreamPl500k, 0755);
                                                // меняем юзера и группу папки
                                                chown($dirStreamPl500k, 'www-data');
                                                chgrp($dirStreamPl500k, 'www-data');
                                            }
                                            $dirQuad500k1 = __ROOT__.'/UserFiles/'.$video4['user_id'].'/500k/cut/';
                                            if (!file_exists($dirQuad500k1)) {
                                                mkdir($dirQuad500k1, 0755);
                                                // меняем юзера и группу папки
                                                chown($dirQuad500k1, 'www-data');
                                                chgrp($dirQuad500k1, 'www-data');
                                            }

                                            $dirQuad500k = __ROOT__.'/UserFiles/'.$video4['user_id'].'/500k/cut/'.$a[3].'/';
                                            if (!file_exists($dirQuad500k)) {
                                                mkdir($dirQuad500k, 0755);
                                                // меняем юзера и группу папки
                                                chown($dirQuad500k, 'www-data');
                                                chgrp($dirQuad500k, 'www-data');
                                            }

                                            $steps = $steps+1;
                                            $sql->_update_steps_shell_tasks($steps, $id);

                                            if(shell_exec("ffmpeg -y -progress '".$logDir."/step4.txt' -i ".__ROOT__.$video4['src_quad']." -acodec copy -vb 500K ".$dirQuad500k.$a[3]."-q500k.".$a[8]." 2>&1")) {
echo '

-------------  run 4 if 5  -----------------

';
                                                $steps = $steps+1;
                                                $sql->_update_steps_shell_tasks($steps, $id);

                                                // меняем юзера и группу лога шага
                                                chown($logDir."/step4.txt", 'www-data');
                                                chgrp($logDir."/step4.txt", 'www-data');
                                                // меняем юзера и группу папки логов
                                                chown($dirQuad500k.$a[3]."-q500k.".$a[8], 'www-data');
                                                chgrp($dirQuad500k.$a[3]."-q500k.".$a[8], 'www-data');

                                                $src_quad_500 = '/UserFiles/'.$video4['user_id'].'/500k/cut/'.$a[3].'/'.$a[3].'-q500k.'.$a[8];
                                                $resEndGen500q = $sql->_update4_shell2_tasks_shell_tasks($src_quad_500, $id);
                                                
                                                $sql->_insert_shell_tasks_shell_steps($a[3], '8');

                                                
                                                
                                                $video6 = $sql->_select2_shell_tasks_shell_tasks($id);
                                                $steps = $video6['steps'];

                                                if(count($video6) > 0 && $video6['src_quad_500'] != '' && $video6['src_quad_500'] != 'in work' && $video6['src_quad_500'] != 'complete' && $video6['m3u8_quad'] == '') {
                                                    $lines = file($logDir.'/step4.txt');
                                                    if(count($lines) > 0) {
                                                        $lastLine = $lines[count($lines)-1];
                                                        $step4End = strpos($lastLine, 'end');
                                                        if($step4End !== false) {
echo '

-------------  run 4 if 6 7 8  -----------------

';
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

                                                            $dirStreamCutPl500k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/500k/'.$a[3].'/';
                                                            $dirStreamCutPl1000k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/1000k/'.$a[3].'/';
                                                            $dirStreamCutPl2000k = __ROOT__.'/UserFiles/'.$video6["user_id"].'/cut/2000k/'.$a[3].'/';
                                                            if (!file_exists($dirStreamCutPl500k)) {
                                                                mkdir($dirStreamCutPl500k, 0755);
                                                                // меняем юзера и группу папки
                                                                chown($dirStreamCutPl500k, 'www-data');
                                                                chgrp($dirStreamCutPl500k, 'www-data');
                                                            }
                                                            $steps = $steps+1;
                                                            $sql->_update_steps_shell_tasks($steps, $id);
                                                            if(shell_exec("ffmpeg -y -re -i ".__ROOT__.$video6['src_quad_500']." -codec copy -map 0 -f segment -segment_list '".$dirStreamCutPl500k."pl500.m3u8' -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 '".$dirStreamCutPl500k.$a[3]."-%03d.ts' 2>&1")) {
echo '

-------------  run 4 if 9  -----------------

';
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

                                                                $file = __ROOT__.'/UserFiles/'.$video6["user_id"].'/'.$a[3].'-quad.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/500/pl500.m3u8';
                                                                file_put_contents($file, $playlist);
                                                                // меняем юзера и группу плейлиста
                                                                chown($file, 'www-data');
                                                                chgrp($file, 'www-data');
                                                                $m3u8_quad = '/UserFiles/'.$video6["user_id"].'/'.$a[3].'-quad.m3u8';
                                                                $resEndGen500kStream = $sql->_update5_shell3_tasks_shell_tasks($m3u8_quad, $id);

                                                                $video6Complete = $sql->_update3_shell3_tasks_shell_tasks($id);

//                                                                 $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video6['src_noblack']." -v quiet -show_format -show_streams 2>&1");
//                                                                 $steps = $steps+1;
//                                                                 $sql->_update_steps_shell_tasks($steps, $id);
//                                                                 $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                                                                
//                                                                 //print_r($parseInfoArr);

//                                                                 $vW = explode('=', $parseInfoArr[9]);
//                                                                 $vH = explode('=', $parseInfoArr[10]);
//                                                                 $p2 = $vW[1].'x'.$vH[1];

//                                                                 $file = __ROOT__.'/UserFiles/'.$video6["user_id"].'/'.$a[3].'.m3u8';
// $playlist = '#EXTM3U
// #EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p2.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
// 500/pl500.m3u8?id='.$a[3];
                                                                
//                                                             file_put_contents($file, $playlist);
//                                                             // меняем юзера и группу плейлиста
//                                                             chown($file, 'www-data');
//                                                             chgrp($file, 'www-data');

                                                            $parseInfoString = shell_exec("ffprobe ".__ROOT__.$video6['src_quad']." -v quiet -show_format -show_streams 2>&1");
                                                            $steps = $steps+1;
                                                            $sql->_update_steps_shell_tasks($steps, $id);
                                                            $parseInfoArr = explode(PHP_EOL, $parseInfoString);
                                                            $vW = explode('=', $parseInfoArr[9]);
                                                            $vH = explode('=', $parseInfoArr[10]);
                                                            $p22 = $vW[1].'x'.$vH[1];

                                                            $file = __ROOT__.'/UserFiles/'.$video6["user_id"].'/'.$a[3].'-quad.m3u8';
$playlist = '#EXTM3U
#EXT-X-STREAM-INF:AVERAGE-BANDWIDTH=511646,BANDWIDTH=531402,VIDEO-RANGE=SDR,CODECS="mp4a.40.2,avc1.640033",RESOLUTION='.$p22.',FRAME-RATE=29.970,CLOSED-CAPTIONS=NONE
cut/500/pl500.m3u8';
                                                            
                                                            file_put_contents($file, $playlist);
                                                            // меняем юзера и группу плейлиста
                                                            chown($file, 'www-data');
                                                            chgrp($file, 'www-data');
                                                            
                                                            $uid = $video6["user_id"];
                                                            $video = $sql->_select2_shell_tasks_shell_tasks($id);
                                                            $fn = explode('/', $video['src_origin'])[2];
                                                            $vid = explode('.', $fn)[0];
                                                            $ext = explode('.', $fn)[1];
                                                            
                                                            $originDir = __ROOT__.'/UserFiles/'.$uid.'/'.$vid.'/';

                                                            $del500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$vid.'/'.$vid.'-500k.'.$ext;
                                                            //$del500cut = __ROOT__.'/UserFiles/'.$uid.'/cut/'.$id.'cut.'.$ext;
                                                            $del500cut2 = __ROOT__.'/UserFiles/'.$uid.'/500k/cut/'.$vid;
                                                            // удаление
                                                            if(file_exists($del500)) {
                                                                //RDir($originDir);
                                                                //unlink($del500);
                                                                //unlink($del500cut);
                                                                //$this->RDir($del500cut2);
                                                            }

                                                            //------------ Пути для сохранения
                                                            // плейлист для стандартного размера
                                                            //$playlist1 = __ROOT__.'/UserFiles/'.$video["user_id"].'/'.$a[3].'.m3u8';
                                                            // файлы для плейлиста 1
                                                            $pl1FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$vid;
                                                            // постер
                                                            $poster = __ROOT__.$video['poster'];

                                                            // плейлист для квадратного размера
                                                            $playlist2 = __ROOT__.$video['m3u8_quad'];
                                                            // файлы для плейлиста 2
                                                            $pl2FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/cut/500k/'.$vid;
                                                            // постер квадрат
                                                            $posterQuad = __ROOT__.$video['poster_quad'];

                                                            // сохраняем плейлисты  
                                                            
echo '

-------------  run 4 if saved -----------------

';
                                                            $s3->putObject(array(
                                                                'Bucket'     => $a[6],
                                                                'SourceFile' => $playlist2,
                                                                'Key'        => 'v/'.$vid.'/q.m3u8',
                                                                'ACL'        => 'public-read'
                                                            ));

                                                            $UploadAWS4 = $s3->uploadDirectory($pl2FilesDir500, $a[6], 'v/'.$vid.'/cut/500', array(
                                                                'concurrency' => 20,
                                                                'debug'       => true,
                                                                'before' => function (\Aws\Command $command) {
                                                                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                                                                    ? 'public-read'
                                                                    : 'public-read';
                                                                }
                                                            ));
                                                            $nameid = $vid;
                                                            echo 'id: '.$id.' --';
                                                            // сохраняем новые minio-пути к плейлистам в базу

                                                            echo '-uid- '.$uid.' --';
                                                            echo '-nameid- '.$nameid.' --';
                                                            
                                                            $mres = $sql->_update_shellSave_media_uN($uid, $a[9], $nameid);

                                                            $sql->_statusSwitch_steps_shell_tasks('3', $uid, $a[9], $a[7]);
                                                            $sql->_insert_shell_tasks_shell_steps($a[3], '10');

                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }
        
                            }
                        };
                        //$this->runtime3 = new Runtime();
                        
                        $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '7');$this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '8');
                        sleep(1);
                        $this->sql->_insert_shell_tasks_shell_steps($videoNameArr[0], '11');
                        $future3 = $this->runtime->run($run3, [$this->args]);
                        //$future4 = $this->runtime3->run($run4, [$this->args]);
echo '

future3: '.$future3->value().'

';

                        $this->runtime->close();
                        $this->runtime2->close();   
                        //$this->runtime3->close();
                        }
                    }
                }
            }
            
        }
    


//}
// $file = __ROOT__.'/UserFiles/shell_log.txt';
// // Открываем файл для получения существующего содержимого
// $current = file_get_contents($file);
// // Добавляем нового человека в файл
// $current = "<pre>".$videos[0][3]."</pre>";
// // Пишем содержимое обратно в файл
// if(file_put_contents($file, $current)) {
//     echo 'put done \n';
// }
    

    } //__construct

    
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

    public function RDir( $path ) {
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

}

new ShellX($argv[1]);

?>