<?php

function run($sc, $sh, $try) {
    shell_exec("screen -L -Logfile /var/www/php/shellStart/".$sc.".log -dmS '".$sc."' /etc/php8z/bin/php -q /var/www/php/UserFiles/php/shellX.php ".$sh." 2>&1");

    $res = shell_exec('screen -ls 2>&1');

    $pos = strpos($res, $sc);
        
    if ($pos === false) {
        if($try == 50) {
            echo 'не запустилось совсем
';
            return false;
        }
        echo 'не запустилось, ждем 50 секунд
';
        sleep(50);
        $try++;
        run($sc, $sh, $try);

    } else {
        echo 'запустилось';
        return true;
    }
}

if(isset($argv[1]) && isset($argv[2])) {

    $screenId = $argv[1];
    $shellId = $argv[2];

    run($screenId, $shellId, 1);

}

?>