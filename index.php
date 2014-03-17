<?php
require_once 'pathPrint.php';
require_once 'dataSource.php';
use keyOP;

$kop = new keyOP\pathPrint(new keyOP\dataSource);
//$kop->verbose = true;
$sentence = 'Sam Media';
$tokens = str_split($sentence);
$count = count($tokens);
$indx = 0;

try {

  //$this->tools->wtf($kop->getOptimal('}', ':'));
  foreach($tokens as $token){
    if($indx == $count-1){
      break;
    }
    echo 'To: ',($tokens[$indx+1] == ' ')?'SPACE':$tokens[$indx+1],"\r\n";
    if($tokens[$indx] == $tokens[$indx+1]){
      echo ' <b>Enter</b>'."\r\n";
      $indx++;
      continue;
    }

    $kop->pPrint($tokens[$indx], $tokens[$indx+1]);
    // $this->tools->wtf($kop->getAllPaths($tokens[$indx], $tokens[$indx+1]));
    echo ' <b>Enter</b>'."\r\n";
    $indx++;
  }


}catch(Exception $e){
  echo $e->getMessage();
}
?>
