<?php

function fileToString($filename)
{
    $file = fopen($filename, 'r');
    $ret = fread($file, filesize($filename));
    fclose($file);

    return $ret;
}
