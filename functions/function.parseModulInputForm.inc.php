<?php 
/*
mform function.parseModulInputForm.inc.php

@author mail[at]joachim-doerr[dot]com Joachim Doerr
@author <a href="http://joachim-doerr.com">joachim-doerr.com</a>

@package redaxo4
@version 1.2
*/

if (!function_exists('parseModulInputForm')) {
  function parseModulInputForm ($params) {
    $strOutput = preg_replace_callback('|<mform>(.*?)</mform>|ism', function($matches){
      return !empty($matches[0]) ? parseMFormTemplate(linesToForm($matches[0]),'wrapper') : '';
    }, $params['subject']);
    return $strOutput; 
  } 
}