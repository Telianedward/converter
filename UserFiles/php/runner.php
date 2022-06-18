<?php
namespace Converter;
ini_set("max_execution_time", 300000000);
set_time_limit(300000000);
date_default_timezone_set('Europe/Moscow');
define("__ROOT__",$_SERVER["DOCUMENT_ROOT"]?:'/var/www/php');

include_once( __ROOT__ .'/var.php' );
include_once( __ROOT__ .'/config_cron.ini');
include_once( __ROOT__ .'/apps/namespace/all/pdo.php');
include_once( __ROOT__ .'/apps/namespace/all/sql_dev.php');
include_once( __ROOT__ .'/vendor/S3/autoload.php');

include_once 'shell.php';
include_once 'shell2.php';
include_once 'shell3.php';
include_once 'shell4.php';
include_once 'shell5.php';
include_once 'shell6.php';

use requests_dev\sql_dev;
use Aws\S3\S3Client;
use \parallel\Runtime;
use minio;

use ConverterShellOne\ShellOne;
use ConverterShellOne\ShellTwo;
use ConverterShellOne\ShellThree;
use ConverterShellOne\ShellFour;
use ConverterShellOne\ShellFive;
use ConverterShellOne\ShellSix;

$sql = new sql_dev();

// if(empty($sql->_pdo_connect)) {
//     echo 'not connect';
//     exit();
// }
$s3 = new S3Client([
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

class Converter {

    public function __construct() {
        
        $r1 = new Runtime();

        $r1->run(new ShellOne);
        $r1->run(new ShellTwo);
        $r1->run(new ShellThree);
        $r1->run(new ShellFour);
        $r1->run(new ShellFive);
        $r1->run(new ShellSix);

    }

}

new Converter();


?>