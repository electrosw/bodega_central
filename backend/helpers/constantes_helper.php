<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
if (!function_exists('base_url_cobol')){
    function base_url_cobol(){
        return 'https://'.$_SERVER['HTTP_HOST'];
    }
}

if (!function_exists('base_url')){
    function base_url(){
        return 'https://'.$_SERVER['HTTP_HOST'];
    }
}