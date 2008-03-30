<?php
error_reporting(E_ALL|E_STRICT);

set_include_path(
                '.'
                . PATH_SEPARATOR . '/var/milk/lib'
                . PATH_SEPARATOR . get_include_path()
                );

require_once 'Zend/Loader.php';
spl_autoload_register(array('Zend_Loader', 'autoload'));


function randStr()
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $i = 0;
    $str = '';

    while ($i <= 19) {
        $num = rand(1, strlen($chars));
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    return $str;
}
