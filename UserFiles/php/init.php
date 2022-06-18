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

use requests_dev\sql_dev;
use \parallel\Runtime;

class ShellX {

    public $args;
    public $runtime;
    public $return;

    public function __construct() {

        $this->sql = new sql_dev();

        $tasks = $this->sql->_select_shell_tasks_shell_tasks();

        if(count($tasks) > 0) {

            $runtimeList = [];
            foreach($tasks as $t) {

                $runtimeList[] = new Runtime();

            }
            $i = 0;
            $futureList = [];
            foreach($tasks as $t) {

                $this->args = array();
                $this->args[0] = $t['id'];

                $futureList[] = $runtimeList[$i]->run(function($a){

                    shell_exec('/etc/php8z/bin/php -q /var/www/php/UserFiles/php/shellX.php '.$a[0].' > /var/www/php/UserFiles/shellX'.$a[0].'.log 2>&1');

                }, [$this->args]);

                $i++;
            }

        }

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

new ShellX();

?>