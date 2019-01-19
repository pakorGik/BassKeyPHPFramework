<?php

namespace BassKey\Components\System;

class Curl {
    private $ch;
    private $options = array(CURLOPT_FOLLOWLOCATION => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_HEADER => 0, CURLOPT_NOBODY => 0,
        CURLOPT_MAXREDIRS => 2, CURLOPT_TIMEOUT => 3, CURLOPT_CONNECTTIMEOUT => 3);

    public function __construct($options = NULL) {
        $this->ch = @curl_init();
        curl_setopt_array($this->ch, $this->options);
        if (isset($options)) curl_setopt_array($this->ch, $options);
    }

    public function exec($url) {
        //curl_setopt_array($this->ch, $this->options);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($this->ch);
    }

    public function post($url, $pst) {
        curl_setopt_array($this->ch, $this->options);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $pst);
        return curl_exec($this->ch);
    }

    function fileInfo($link) {
        curl_setopt($this->ch, CURLOPT_URL, $link);
        curl_setopt($this->ch, CURLOPT_NOBODY, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($this->ch);
        $info = curl_getinfo($this->ch);
        return $info;
    }

    function download($dllink, $filename) {
        curl_setopt_array($this->ch, $this->options);
        curl_setopt($this->ch, CURLOPT_URL, $dllink);
        curl_setopt($this->ch, CURLOPT_BINARYTRANSFER , 1);
        $file = fopen(FLS.$filename, 'w');
        curl_setopt($this->ch, CURLOPT_FILE, $file);
        curl_exec($this->ch);
        fclose($file);
    }

    function downloadAccelerated($dllink, $filename) {
        $parts = 5;
        $buffersize = 8388608;
        $info = $this->fileInfo($dllink);
        $size = $info['download_content_length'];
        // prepare part sizes
        $splits = range(-1, $size, ceil($size/$parts));
        $splits[$parts] = ($size-1);
        // create temportary $dir
        $dir = FLS.$filename."_tmp/";
        if (!is_dir($dir)) mkdir($dir);

        $mh = curl_multi_init();
        // open connections and part files
        for ($i=0; $i < $parts; $i++) {
            $part[$i] = $dir.$filename.".".$i;
            $cn[$i] = curl_init($dllink);
            curl_setopt($cn[$i], CURLOPT_RETURNTRANSFER, 0);
            curl_setopt($cn[$i], CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($cn[$i], CURLOPT_NOBODY, 0);
            curl_setopt($cn[$i], CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($cn[$i], CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($cn[$i], CURLOPT_HEADER, 0);
            $fp[$i] = fopen($part[$i], "w+");
            curl_setopt($cn[$i], CURLOPT_FILE, $fp[$i]);
            curl_setopt($cn[$i], CURLOPT_RANGE, ($splits[$i]+1)."-".$splits[$i+1]);
            curl_multi_add_handle($mh, $cn[$i]);
        }
        // downloading
        $active = NULL;
        do $mrc = curl_multi_exec($mh, $active);
        while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do $mrc = curl_multi_exec($mh, $active);
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        $final = fopen(FLS.$filename, "w+");
        // closing connections and part files
        for ($i=0; $i < $parts; $i++) {
            curl_multi_remove_handle($mh, $cn[$i]);
            curl_close($cn[$i]);
            fclose($fp[$i]);
            $current = fopen($dir.$filename.".".$i, 'r');
            // load parts and write to final file
            while ( !feof($current) ) {
                $contents = fread($current, $buffersize);
                fwrite($final, $contents);
            }
            fclose($current);
            unlink($dir.$filename.".".$i);
        }
        curl_multi_close($mh);
        fclose($final);
        rmdir($dir);
    }

    public function close() {
        curl_close($this->ch);
    }
}

