<?php
namespace previewer_dev
{
    class previewer_dev
    {
        public function __construct($in)
        {
            $in

            $parseInfoString = shell_exec('ffprobe '.$in.' -v quiet -show_format -show_streams 2>&1');
            $parseInfoArr = explode(PHP_EOL, $parseInfoString);
            $keyframes = '00:00:00,00:00:01';

            $time_base = explode('=', $parseInfoArr[31])[1];
            $duration_ts = explode('=', $parseInfoArr[34])[1];
            $duration = explode('=', $parseInfoArr[35])[1];
            $tbArr = explode('/', $time_base);

            $p33 = (int) ($duration_ts * (33/100));
            $p33sec = (($tbArr[0]/$tbArr[1])/1)*$p33;
            $secArr = $this->secToArray($p33sec);
            $keyframe3 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs'], 2, '0', STR_PAD_LEFT);
            $keyframe4 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs']+1, 2, '0', STR_PAD_LEFT);

            $p66 = (int) ($duration_ts * (66/100));
            $p66sec = (($tbArr[0]/$tbArr[1])/1)*$p66;
            $secArr = $this->secToArray($p66sec);
            $keyframe5 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs'], 2, '0', STR_PAD_LEFT);
            $keyframe6 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs']+1, 2, '0', STR_PAD_LEFT);

            $secArr = $this->secToArray($duration);
            $keyframe7 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs']-1, 2, '0', STR_PAD_LEFT);
            $keyframe8 = str_pad($secArr['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($secArr['secs'], 2, '0', STR_PAD_LEFT);

            $keyframes .= ','.$keyframe3.','.$keyframe4.','.$keyframe5.','.$keyframe6.','.$keyframe7.','.$keyframe8;

            shell_exec('ffmpeg -i /usr/src/bandicam.mp4 -force_key_frames '.$keyframes.' /usr/src/bout.mp4 2>&1');

            shell_exec('ffmpeg -ss 00:00:00 -i /usr/src/bout.mp4 -t 00:00:01 -c copy -bsf:v h264_mp4toannexb -f mpegts /usr/src/bcut1.ts 2>&1');
            shell_exec('ffmpeg -ss '.$keyframe3.' -i /usr/src/bout.mp4 -t 00:00:01 -c copy -bsf:v h264_mp4toannexb -f mpegts /usr/src/bcut2.ts 2>&1');
            shell_exec('ffmpeg -ss '.$keyframe5.' -i /usr/src/bout.mp4 -t 00:00:01 -c copy -bsf:v h264_mp4toannexb -f mpegts /usr/src/bcut3.ts 2>&1');
            shell_exec('ffmpeg -ss '.$keyframe7.' -i /usr/src/bout.mp4 -t 00:00:01 -c copy -bsf:v h264_mp4toannexb -f mpegts /usr/src/bcut4.ts 2>&1');
            shell_exec("ffmpeg -i 'concat:bcut1.ts|bcut2.ts|bcut3.ts|bcut4.ts' -vcodec copy -acodec copy /usr/src/complete.mp4 2>&1");

            shell_exec("ffmpeg -y -re -i /usr/src/complete.mp4 -sn -dn -codec copy -map 0 -f segment -segment_list /usr/src/pl500.m3u8 -segment_list_flags +live -segment_time 1 -segment_clocktime_offset 1 /usr/src/segs/seg-%03d.ts 2>&1");
        }

        public function secToArray($secs)
        {
            $res = array();
            $res['days'] = floor($secs / 86400);
            $secs = $secs % 86400;
            $res['hours'] = floor($secs / 3600);
            $secs = $secs % 3600;
            $res['minutes'] = floor($secs / 60);
            $res['secs'] = $secs % 60;
            return $res;
        }     
    }
}
?>