<?php
$runtime = new \parallel\Runtime;
$future  = $runtime->run(function(){
    echo "в дочернем потоке ";
    for ($i = 0; $i < 500; $i++) {
        if ($i % 10 == 0) {
            echo ".";
        }
    }
    echo " выход из дочернего потока";
});

//$future->value();
echo "\nродительский поток продолжает работать\n";
?>