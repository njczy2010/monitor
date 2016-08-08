<?php

$_cache = Array();

function getline($filename, $lineno) {
    $lines = getlines($filename, $lineno);
    if ($lineno > 0 && $lineno <= count($lines)) {
        return $lines[$lineno - 1];
    } else {
        return '';
    }
}

function getlines($filename, $lineno) {
    global $_cache;
    if (in_array($filename, $_cache)) {
        return $_cache[$filename];
    } else {
        return updatecache($filename);
    }
}

function updatecache($filename) {
    $lines = Array();
    $handle = @fopen($filename, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $lines[] = $line;
        }
    }
    global $_cache;
    $_cache[$filename] = $lines;
    return $lines;
}
