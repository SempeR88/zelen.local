<?php

function debugPrintF($var)
{
    echo '<pre>' . print_r($var, true) . '</pre>';
}

function debugVarDump($var)
{
    echo '<pre>' . var_dump($var) . '</pre>';
}