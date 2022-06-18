<?php
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

$sql = new sql_dev();

$s3 = new Aws\S3\S3Client([
    'version' => minio\VERSION,
    'region'  => minio\REGION,
	'endpoint' => 'http://minio.vhst.ru:'.minio\PORT,
	'use_path_style_endpoint' => true,
	'credentials' => [
		'key'    => minio\LOGIN,
		'secret' => minio\PASS,
	],
]);
$bucket = 'uploads';

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
                    RDir( $tmp );
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

for ($times = 0; $times < 8; ++$times) {
        echo '###'.$times.'###';
        $videos = array();

        $res = $sql->_select_shellSave_tasks_shell_tasks();

        if(!$res || count($res) == 0) {
            sleep(4);
            continue;
        }
        $i = 0;
        foreach($res as $video) {

            echo '$$$'.$i.'$$$';

            if(!isset($video['user_id']) || empty($video['user_id'])) continue;

            $uid = $video['user_id'];
            $id = array_reverse(explode('/', explode('.', $video['m3u8'])[0]))[0];
            $ext = explode('.', $video['src_origin'])[1];

            $bucket = 'uploads';

            //echo '<br><br>'.$id.'<br>';
            // пути для удаления
            //$origin = __ROOT__.'/UserFiles/'.$uid.'/'.$id.'/'.$id.'.'.$ext;
            $poster = 'i/'.$id.'/poster.jpeg';
            $poster_quad = 'i/'.$id.'/posterQuad.jpeg';
            $res = $sql->_update_shellSave_tasks_shell_tasks($poster, $poster_quad, $video['id']);
            
            $originDir = __ROOT__.'/UserFiles/'.$uid.'/'.$id.'/';
            $originDirNB = __ROOT__.'/UserFiles/'.$uid.'/generated/'.$id.'nb.'.$ext;

            $del500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$id.'/'.$id.'-500k.'.$ext;
            $del1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id.'/'.$id.'-1000k.'.$ext;
            $del2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$id.'/'.$id.'-2000k.'.$ext;
            $del500cut = __ROOT__.'/UserFiles/'.$uid.'/cut/'.$id.'cut.'.$ext;
            $del500cut2 = __ROOT__.'/UserFiles/'.$uid.'/500k/cut/'.$id;
            $del1000cut = __ROOT__.'/UserFiles/'.$uid.'/cut/1000k/'.$id.'/'.$id.'-q1000k.'.$ext;
            $del2000cut = __ROOT__.'/UserFiles/'.$uid.'/cut/2000k/'.$id.'/'.$id.'-q2000k.'.$ext;
            // удаление
            if(file_exists($del500cut2)) {
                //RDir($originDir);
                unlink($originDirNB);
                unlink($del500);
                unlink($del1000);
                unlink($del2000);
                unlink($del500cut);
                RDir($del500cut2);
                unlink($del1000cut);
                unlink($del2000cut);
            }

//------------ Пути для сохранения
            // плейлист для стандартного размера
            $playlist1 = __ROOT__.$video['m3u8'];
            // файлы для плейлиста 1
            $pl1FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$id;
            $pl1FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id;
            $pl1FilesDir2000 = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$id;
            // постер
            $poster = __ROOT__.$video['poster'];

            // плейлист для квадратного размера
            $playlist2 = __ROOT__.$video['m3u8_quad'];
            // файлы для плейлиста 2
            $pl2FilesDir500 = __ROOT__.'/UserFiles/'.$uid.'/cut/500k/'.$id;
            $pl2FilesDir1000 = __ROOT__.'/UserFiles/'.$uid.'/cut/1000k/'.$id;
            $pl2FilesDir2000 = __ROOT__.'/UserFiles/'.$uid.'/cut/2000k/'.$id;
            // постер квадрат
            $posterQuad = __ROOT__.$video['poster_quad'];

            // echo $bucket.'<br>';
            // echo $id.'<br>';
            // echo $playlist1.'<br>';
            // $insert = $s3->putObject([
            //     'Bucket' => $bucket,
            //     'Key'    => 'i/'.$id,
            //     'SourceFile' => $playlist1,
            //     'ACL'        => 'public-read'
            // ]);

            // $origin500k = __ROOT__.'/UserFiles/'.$uid.'/500k/'.$id.'/'.$id.'-500k.'.$ext;
            // $origin1000k = __ROOT__.'/UserFiles/'.$uid.'/1000k/'.$id.'/'.$id.'-1000k.'.$ext;
            // $origin2000k = __ROOT__.'/UserFiles/'.$uid.'/2000k/'.$id.'/'.$id.'-2000k.'.$ext;

            // 
            // сохраняем плейлисты  
            $s3->putObject(array(
                'Bucket'     => $bucket,
                'SourceFile' => $playlist1,
                'Key'        => 'i/'.$id.'/main.m3u8',
                'ACL'        => 'public-read'
            ));
            $s3->putObject(array(
                'Bucket'     => $bucket,
                'SourceFile' => $playlist2,
                'Key'        => 'i/'.$id.'/quad.m3u8',
                'ACL'        => 'public-read'
            ));
            // сохраняем постеры
            $s3->putObject(array(
                'Bucket'     => $bucket,
                'SourceFile' => $poster,
                'Key'        => 'i/'.$id.'/poster.jpeg',
                'ACL'        => 'public-read'
            ));
            $s3->putObject(array(
                'Bucket'     => $bucket,
                'SourceFile' => $posterQuad,
                'Key'        => 'i/'.$id.'/posterQuad.jpeg',
                'ACL'        => 'public-read'
            ));
            // записываем все в MINIO
            // $keyPrefix = '';
            // $dir = __ROOT__.'/s3/testhls/';
            $UploadAWS1 = $s3->uploadDirectory($pl1FilesDir500, $bucket, 'i/'.$id.'/500', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            $UploadAWS2 = $s3->uploadDirectory($pl1FilesDir1000, $bucket, 'i/'.$id.'/1000', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            $UploadAWS3 = $s3->uploadDirectory($pl1FilesDir2000, $bucket, 'i/'.$id.'/2000', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            $UploadAWS4 = $s3->uploadDirectory($pl2FilesDir500, $bucket, 'i/'.$id.'/cut/500', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            $UploadAWS5 = $s3->uploadDirectory($pl2FilesDir1000, $bucket, 'i/'.$id.'/cut/1000', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            $UploadAWS6 = $s3->uploadDirectory($pl2FilesDir2000, $bucket, 'i/'.$id.'/cut/2000', array(
                'concurrency' => 20,
                'debug'       => true,
                'before' => function (\Aws\Command $command) {
                $command['ACL'] = strpos($command['Key'], 'CONFIDENTIAL') === false
                    ? 'public-read'
                    : 'public-read';
                }
            ));
            // получаем ID видео
            $vidData = $sql->_select_id_shellSave_media_uN($uid, $id);
            $vid = $vidData['id'];
            echo 'id: '.$id.' --';
            // сохраняем новые minio-пути к плейлистам в базу
            $src1 = 'i/'.$id.'/main.m3u8';
            $src2 = 'i/'.$id.'/quad.m3u8';

            echo '-- '.$uid.' --';
            echo '-- '.$id.' --';
            echo '-- '.$src1.' --';
            echo '-- '.$src2.' --';
            
            $mres = $sql->_update_shellSave_media_uN($uid, $vid, $src1, $src2);

            // очищаем оставшиеся файлы с сервера
            
            echo '

            очищаем
            
';
            sleep(10);
            
            $result = $s3->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $id.'.'.$ext
            ]);

            unlink($playlist1);
            unlink($playlist2);
            unlink($poster);
            unlink($posterQuad);
            RDir($pl1FilesDir500);
            RDir($pl1FilesDir1000);
            RDir($pl1FilesDir2000);
            RDir($pl2FilesDir500);
            RDir($pl2FilesDir1000);
            RDir($pl2FilesDir2000);

            echo '

            очистили
            
';

            $i++;
        }



    


    sleep(4);
}