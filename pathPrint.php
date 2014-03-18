<?php
namespace keyOP;

require_once 'keyOP.php';

/**
 * Formats and prints the steps from a character to another.
 * @author Hussein Guettaf <ghoucine@gmail.com>
 */

class pathPrint extends keyOP
{

  public function pPrint($char1,$char2)
  {
    $path = $this->getOptimal($char1,$char2);
    $str = '';

    if(isset($path['to'])){

      $str .= $this->flatten($path['to']);

      $str .= $this->flatten($path['from']);

      echo  $this->replace($str);

      return;
    }

      $str = $this->flatten($path);
      echo  $this->replace($str);
      return;

  }
  protected function replace($str)
  {
    $str = str_replace('[r]','right',$str);
    $str = str_replace('[l]','left',$str);
    $str = str_replace('[d]','down',$str);
    $str = str_replace('[u]','up',$str);

    return $str;
  }
  protected function flatten($path)
  {
    $str = '';
    foreach($path as  $direction => $p){
      foreach($p as $dir => $val){
        if($val == 0){
          continue;
        }
        $str .= ' ['.$dir.'] '.$val;
      }
    }

    return $str;
  }
}


?>