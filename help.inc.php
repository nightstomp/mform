<?php
/*
mform help.inc.php

@author mail[at]joachim-doerr[dot]com Joachim Doerr
@author <a href="http://joachim-doerr.com">joachim-doerr.com</a>

@package redaxo4
@version 1.2
*/

$mypage = 'mform';
require_once $REX['INCLUDE_PATH'].'/addons/' . $mypage . '/pages/demo.inc.php';

?>
<h2><?php echo $I18N->msg('mform_headline'); ?></h2>
<p><?php echo $I18N->msg('mform_description'); ?></p>
<br/>
<h3><?php echo $I18N->msg('mform_headline_example'); ?></h3>
<?php rex_highlight_string($mdl_im); ?><br/>

<p>
<?php
  $file = dirname( __FILE__) .'/_changelog.txt';
  if(is_readable($file))
    echo str_replace( '+', '&nbsp;&nbsp;+', nl2br(file_get_contents($file)));
?>
</p>