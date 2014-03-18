<?php
namespace keyOP;

/**
 * Generates parts of the layout and compiles a dictionary of maps for the keyboard keys.
 *
 * @author Hussein Guettaf <ghoucine@gmail.com>
 */

class dataSource
{
  public $map = array();
  public $index = array();
  public $layout = array();

  public function __construct()
  {
    $this->createMap();
  }

  protected function createMap()
  {
    $template = array_fill(0, 26, '__val__');
    // upercase letters
    foreach($template as $key => $value){
      $map[$key + 1000] = $key + 65;
    }
    // lowercase letters
    foreach($map as $key => $val){
      $map[$key + 1000] = $val + 32;
    }

    $row3 = array(
    3000 => 48,     // 0
    3001 => 49,     // 1
    3002 => 50,     // 2
    3003 => 51,     // 3
    3004 => 52,     // 4
    3005 => 53,     // 5
    3006 => 54,     // 6
    3007 => 55,     // 7
    3008 => 56,     // 8
    3009 => 57,     // 9
    3010 => 33,     // !
    3011 => 64,     // @
    3012 => 35,     // #
    3013 => 36,     // $
    3014 => 37,     // %
    3015 => 94,     // ^
    3016 => 38,     // &
    3017 => 42,     // *
    3018 => 40,     // (
    3019 => 41,     // )
    3020 => 63,     // ?
    3021 => 47,     // /
    3022 => 124,     // |
    3023 => 92,     // \
    3024 => 43,     // +
    3025 => 45)    // -
    ;
    $row4 = array(
    4000 => 96,     // `
    4001 => 126,    // ~
    4002 => 91,     // [
    4003 => 93,     // ]
    4004 => 123,    // {
    4005 => 125,    // }
    4006 => 60,     // <
    4007 => 62,     // >
    4008 => 32,     // space
    4009 => 32,     // space
    4010 => 32,     // space
    4011 => 32,     // space
    4012 => 32,     // space
    4013 => 32,     // space
    4014 => 32,     // space
    4015 => 32,     // space
    4016 => 46,     // .
    4017 => 44,     // ,
    4018 => 59,     // ;
    4019 => 58,     // :
    4020 => 39,     // '
    4021 => 34,     // "
    4022 => 95,     // _
    4023 => 61,     // =
    4024 => 8,      // backspace
    4025 => 8)      // backspace
    ;

    // Create the keyboard layout
    $this->layout = $map + $row3 + $row4;

    // Create a map for all the characters in the keyboard

    $count = 0;
    $previous = null;

    foreach($this->layout as $key => $le){

      $this->index[$key] = $count;

      $index = array();
      $row = substr($key, 0, 1);
      $col = $key - ($row * 1000) + 1;
      $index[$key] = $le;
      $index['row'] = $row;
      $index['col'] = $col;
      $index['onspace'] = false;
      $index['space'] = false;
      $index['bs'] = false;

      if($col >= 9 && $col <= 16 && $le != 32){
        $index['onspace'] = true;
      }
      if($col <= 11){
        $index['location'] = 'left';
      }
      if($col >= 15){
        $index['location'] = 'right';
      }
      if($row == 1){
        $up = 4;
      }else{
        $up = $row - 1;
      }
      if($row == 4){
        $down = 1;
      }else{
        $down = $row + 1;
      }

      if($col == 1){
        $left = 26;
      }else{
        $left = $col - 1;
      }
      if($col == 26){
        $right = 1;
      }else{
        $right = $col + 1;
      }

      $index['u'] = ($up * 1000) + ($col - 1);
      $index['d'] = ($down * 1000) + ($col - 1);

      $index['l'] = ($row * 1000) + ($left - 1);
      $index['r'] = ($row * 1000) + ($right - 1);

      if($le == 8){
        $index['col'] = 26;
        $index['u'] = 3025;
        $index['d'] = 1025;
        $index['l'] = 4023;
        $index['r'] = 4000;
        $index['bs'] = true;
      }
      if($le == 32){
        $index['u'] = 3012;
        $index['d'] = 1008;
        $index['l'] = 4007;
        $index['r'] = 4016;
        $index['space'] = true;
      }

      $previous = $le;
      $this->map[] = $index;
      $count++;
    }

  }
}


?>