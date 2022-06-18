<?php

shell_exec("ffmpeg -y -re -i /var/www/php/UserFiles/1/500k/upload_622888669c3f6/upload_622888669c3f6-500k.mp4 -codec copy -map 0 -f segment -segment_list '/var/www/php/UserFiles/1/500k/upload_622888669c3f6/pl500.m3u8'                     -segment_list_flags +live -segment_time 10 '/var/www/php/UserFiles/1/500k/upload_622888669c3f6/upload_622888669c3f6-%03d.ts' 2>&1");