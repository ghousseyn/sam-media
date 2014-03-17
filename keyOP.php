<?php
namespace keyOP;

/**
 *
 * @author Hussein Guettaf <ghoucine@gmail.com>
 */

class keyOP
{

  /**
   * Enable this to see more information about each step (could be a lot of output)
   * @var boolean
   */
  public $verbose = false;

  /**
   * Change the end of line if you want (for verbose mode)
   * @var string
   */
  const EOL = "<br />";
  /**
   * Should changes in direction affect the score of the path and how much this effect is
   * @var float
   */
  const DIR_SWITCH_WEIGHT = 0.5;

  /**********************************
   * End of configuration
   */

  /**
   * The character squence being processed two characters at atime)
   * @var string
   */
  protected $elmOne = null;
  protected $elmTwo = null;

  /**
   * These are the dataSource properties holding the layout of the keybard and the dictionary of the keys and their map
   * @var array
   */
  protected $map = array();
  protected $index = array();
  protected $layout = array();

  /**
   * This will hold the path
   * @var array
   */
  protected $path = array();

  /**
   * Vertical and Horizontal steps respectively
   * @var integer
   */
  protected $vsteps = 0;
  protected $hsteps = 0;

  /**
   * Holds available directions (vertical and horizontal)
   * @var array
   */
  protected $directions = array();

  /**
   * Vertical and Horizontal directions respectively
   * @var string
   */
  protected $vdir = null;
  protected $hdir = null;

/**
 * Initialize with loading keys dictionary and index and keybord layout from data source
 * @param dataSource $source
 */
  public function __construct(dataSource $source)
  {
    $this->map = $source->map;
    $this->index = $source->index;
    $this->layout = $source->layout;

  }

  /**
   * Selects the optimal path between two given characters based on their scores
   * @param string $char1
   * @param string $char2
   * @return array The compiled path
   */
  public function getOptimal($char1, $char2)
  {
    $this->getAllPaths($char1, $char2);

    if(isset($this->path['space']) && ($this->path['space']['score'] <= $this->path['score'])){

      return $this->path['space'];
    }

    $path = $this->path;
    unset($path['space']);

    return $path;
  }
  /**
   * Tries to collect all paths (use it to see all paths if you want to - left public for that matter)
   * @param string $char1
   * @param string $char2
   * @todo make it protected and remove parameters
   */
  public function getAllPaths($char1, $char2)
  {
    $this->path = array();
    $elmOneKey = $this->positionInLayout($char1);
    $elmTwoKey = $this->positionInLayout($char2);

    $this->elmOne = $this->getElement($elmOneKey);
    $this->elmTwo = $this->getElement($elmTwoKey);

    if($this->SPinBetween()){

      $this->spaceShortcut();
    }

    $this->getPath($this->elmOne, $this->elmTwo);
    $this->path['score'] = $this->getScore($this->path);

    return $this->path;
  }
  /**
   * If the space bar is in between we split the path into "to space" and "from space" and evaluate (it could be a shortcut)
   */
  protected function spaceShortcut()
  {

    $spaceKey = $this->positionInLayout(chr(32));
    $space = $this->getElement($spaceKey);
    $elmTwo = $this->elmTwo;

    $this->elmTwo = $space;
    $this->getPath($this->elmOne, $this->elmTwo);

    $this->elmTwo = $elmTwo;
    $pathToSpace = $this->path;

    $this->path = array();

    $elmOne = $this->elmOne;
    $this->elmOne = $space;
    $this->getPath($space, $this->elmTwo);
    $this->elmOne = $elmOne;

    $pathFromSpace = $this->path;

    $this->path = array();
    $this->path['space']['to'] = $pathToSpace;
    $this->path['space']['from'] = $pathFromSpace;
    $this->path['space']['score'] = $this->getScore($pathToSpace)+$this->getScore($pathFromSpace);

  }
  /**
   * Calculates the score of a given path base on the steps and the direction switch value
   * @param array $path
   * @return integer
   */
  protected function getScore($path)
  {
    $score = 0;

    if(isset($path['h']['r'])){
      $score += $path['h']['r'] + self::DIR_SWITCH_WEIGHT;
    }
    if(isset($path['h']['l'])){
      $score += $path['h']['l'] + self::DIR_SWITCH_WEIGHT;
    }
    if(isset($path['v']['u'])){
      $score += $path['v']['u'] + self::DIR_SWITCH_WEIGHT;
    }
    if(isset($path['v']['d'])){
      $score += $path['v']['d'] + self::DIR_SWITCH_WEIGHT;
    }
    return $score;
  }

  /**
   * Performs an initial check on each character and returns its position in the layout
   * @param string $char
   * @throws \UnexpectedValueException   On empty parameter
   * @throws \OutOfBoundsException       If a non-existant character is passed
   */
  protected function positionInLayout($char)
  {
    if(empty($char)){
      throw new \UnexpectedValueException("An empty argument exception", 9901);
    }

    $asci = ord($char);

    if(! in_array($asci, $this->layout)){
      throw new \OutOfBoundsException("The character $char is not available in this keyboard", 9902);
    }
    return array_search($asci, $this->layout);
  }

  /**
   * Decides if we should step vertically first
   * @param array $elmOne
   * @return boolean
   */
  protected function verticalFirst($elmOne)
  {

    if($elmOne['row'] == 4 && ($elmOne['location'] == $this->elmTow['location']) || ($elmOne['space'] && !$this->elmTwo['onspace']) ){

      return false;

    }else{

      return true;

    }
  }
  /**
   * Decides if the space bar is in between
   * @return boolean
   */
  protected function SPinBetween()
  {
    if($this->elmOne['space'] || $this->elmTwo['space']){
      return false;
    }
    if($this->elmOne['location'] != $this->elmTwo['location']){
      if($this->elmOne['onspace'] || $this->elmTwo['onspace']){
        if($this->hdir == 'r' && $this->elmTwo['row'] == 1){
          return false;
        }
      }

      return true;
    }
    return false;

  }
  /**
   * Returns the map of a key given the index of its character
   * @param integer $layoutKey
   * @return array
   */
  protected function getElement($layoutKey)
  {
    $index = $this->index[$layoutKey];
    return $this->map[$index];
  }
  /**
   * Starts the stepping process to get the path from a key to another
   * @param array $elmOne
   * @param array $elmTwo
   */
  protected function getPath($elmOne, $elmTwo)
  {

    $this->directions = $this->hasDirections($elmOne, $elmTwo);
    $this->hdir = $this->horizontalDir($elmOne,$elmTwo);

    if($this->directions['vertical'] ){
      if($this->verticalFirst($this->elmOne) || !$this->directions['horizontal']){

        $this->vdir = $this->verticalDir($elmOne,$elmTwo);

        $this->vsteps = 0;
        $this->walkVertical($elmOne, $elmTwo);

      }else{

        if($this->directions['horizontal']){
          $this->hsteps = 0;
          $this->walkHorizontal($elmOne, $elmTwo);

        }

      }
    }else{
      $this->hsteps = 0;
      $this->walkHorizontal($elmOne, $elmTwo);

    }

  }
  /**
   * Walks horizontally from a given key to a given destination (either the key or its column)
   * @param array $from
   * @param array $to
   */
  protected function walkHorizontal($from, $to)
  {
    if($this->verbose){
      echo 'H:Row('.$from['row'].') From ', key($from), ' AT column ', $from['col'], ' To ', key($to), ' AT column ', $to['col'], self::EOL ;
    }

    $neighbor = $this->getElement($from[$this->hdir]);
    $this->path['h'][$this->hdir] = ++$this->hsteps;

    if($from == $to || $from == $this->elmTwo){
      $this->path['h'][$this->hdir] = --$this->hsteps;
      $this->hsteps = 0;
      return ;
    }
    if(isset($to[4008]) && in_array(key($from),array(4009,4010,4011,4012,4013,4014,4015)) ){
      $this->path['h'][$this->hdir] = --$this->hsteps;
      return ;
    }


    if($from['col'] == $to['col'] || $from['col'] == $this->elmTwo['col']){
      $this->path['h'][$this->hdir] = --$this->hsteps;
      $this->hsteps = 0;
      $this->directions['horizontal'] = false;
      if($this->directions['vertical']){
        $this->vdir = $this->verticalDir($from,$to);
        $neighbor = $this->getElement($from[$this->vdir]);
        if(! $this->verticalFirst($this->elmOne)){
          $this->vsteps = 0;

          $this->walkVertical($from, $to);
        }

        return;
      }
      return ;
    }else{

      $this->walkHorizontal($neighbor, $to);

    }
  }
  /**
   * Walks vertically from a given key to a given destination (either the key or its row)
   * @param array $from
   * @param array $to
   */
  protected function walkVertical($from, $to)
  {
    if($this->verbose){
      echo 'V:Col('.$from['col'].') From ', key($from), ' AT row ', $from['row'], ' To ', key($to), ' AT row ', $to['row'], self::EOL ;
    }
    $neighbor = $this->getElement($from[$this->vdir]);
    $this->path['v'][$this->vdir] = ++$this->vsteps;

    if($from == $to || $from == $this->elmTwo){
     $this->path['v'][$this->vdir] = --$this->vsteps;
      return ;
    }

    if($from['row'] == $to['row'] || $from['row'] == $this->elmTwo['row']){
      $this->path['v'][$this->vdir] = --$this->vsteps;
      $this->directions['vertical'] = false;
      if($this->directions['horizontal']){
        $this->hdir = $this->horizontalDir($from,$to);
        $neighbor = $this->getElement($from[$this->hdir]);
        $this->hsteps = 0;
        $this->walkHorizontal($from, $to);

        return;
      }
      return ;
    }else{

      $this->walkVertical($neighbor, $to);
    }
  }
  /**
   * The horizontal direction we believe the closest path will take
   * @param array $elmOne
   * @param array $elmTwo
   * @return string Direction 'r' for Right and 'l' for Left
   */
  protected function horizontalDir($elmOne,$elmTwo)
  {

    $hdistance = $elmOne['col'] - $elmTwo['col'];
    $direction = 'l';

      if(!($elmOne['space'] || $elmTwo['space']) && ($elmOne['row'] == 4 || $elmTwo['row'] == 4) && !$elmOne['onspace'] && !$elmTwo['onspace'] && $elmOne['location'] != $elmTwo['location']){
        if($hdistance>0){
          $hdistance -= 7;
        }else{
          $hdistance += 7;
        }

      }

    if($hdistance >= 14 || ($hdistance < 0 && $hdistance >= - 13) ){
      $direction = 'r';
    }

    return $direction;
  }
  /**
   * The vertical direction we believe the closest path will take
   * @param unknown_type $elmOne
   * @param unknown_type $elmTwo
   * @return string Direction 'u' for Up and 'd' for Down
   */
  protected function verticalDir($elmOne,$elmTwo)
  {
    $vdistance = $elmOne['row'] - $elmTwo['row'];
    $direction = 'u';

    if(($vdistance >= 2 || $vdistance == - 1) && !($elmOne['space'] && $elmTwo['location'] == 'right')){
      $direction = 'd';
    }
    if($elmOne['onspace'] && $elmOne['row'] == 1 ){
      $direction = 'd';
    }
    if($elmOne['onspace'] && $elmOne['row'] == 1 && $elmTwo['space']){
      $direction = 'u';
    }

    return $direction;
  }
  /**
   * Checks if we'll be moving around or not (vertical and horizontal)
   * @param array $elmOne
   * @param array $elmTwo
   * @return array
   */
  protected function hasDirections($elmOne, $elmTwo)
  {
    $vertical = false;
    $horizontal = false;

    if($elmOne['row'] != $elmTwo['row']){
      $vertical = true;
    }

    if($elmOne['col'] != $elmTwo['col']){
      $horizontal = true;
    }
    return array('horizontal' => $horizontal, 'vertical' => $vertical);
  }
}
?>