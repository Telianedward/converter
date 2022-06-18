<?php

while(true == true) {
    $load = sys_getloadavg();
    echo '
    '.$load[0].' ';
sleep('1.5');
}

// for ($times = 0; $times < 120; ++$times) {
    
//     // if ($load[0] > 0.80) {
//     //     header('HTTP/1.1 503 Too busy, try again later');
//     //     die('Server too busy. Please try again later.');
//     // } else {
//     //     echo $load[0];
//     // }
// }
// $load = sys_getloadavg();

// print_r($load);
?>