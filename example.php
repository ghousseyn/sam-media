<?php
require_once 'pathPrint.php';
require_once 'dataSource.php';
use keyOP;

define(EOL, "<br />");

$kop = new keyOP\pathPrint(new keyOP\dataSource);
//$kop->verbose = true;

$sentence = 'Sam Media testing page.';
$tokens = str_split($sentence);
$count = count($tokens);
$indx = 0;

try {

  //print_r($kop->getOptimal('}', ':'));
  foreach($tokens as $token){
    if($indx == $count-1){
      break;
    }

    echo 'To: ',($tokens[$indx+1] == ' ')?'SPACE':$tokens[$indx+1],EOL;

    if($tokens[$indx] == $tokens[$indx+1]){
      echo ' <b>Enter</b>'.EOL;
      $indx++;
      continue;
    }

    $kop->pPrint($tokens[$indx], $tokens[$indx+1]);
    // print_r($kop->getAllPaths($tokens[$indx], $tokens[$indx+1]));
    echo ' <b>Enter</b>'.EOL;
    $indx++;
  }


}catch(Exception $e){
  echo $e->getMessage();
}
?>
