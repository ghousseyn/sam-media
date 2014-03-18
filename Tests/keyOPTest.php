<?php
namespace Tests;

use keyOP\keyOP;

require_once 'Tests/PhiberTests.php';
require_once 'keyOP.php';
require_once 'dataSource.php';

class keyOPTest extends \PhiberTests
{
  protected $keyOP = null;

  public function setUp()
  {
    $this->keyOP = new \keyOP\keyOP(new \keyOP\dataSource);
  }
  public function testHasDirections()
  {
    $index1 = 1000;
    $index2 = 3005;
    $elm1 = $this->invokeMethod($this->keyOP, 'getElement',array($index1));
    $elm2 = $this->invokeMethod($this->keyOP, 'getElement',array($index2));

    $ret = $this->invokeMethod($this->keyOP, 'hasDirections',array($elm1,$elm2));

    $this->assertTrue($ret['horizontal']);
    $this->assertTrue($ret['vertical']);
  }
  public function testHasVerticalDirections()
  {
    $index1 = 1005;
    $index2 = 3005;
    $elm1 = $this->invokeMethod($this->keyOP, 'getElement',array($index1));
    $elm2 = $this->invokeMethod($this->keyOP, 'getElement',array($index2));

    $ret = $this->invokeMethod($this->keyOP, 'hasDirections',array($elm1,$elm2));

    $this->assertFalse($ret['horizontal']);
    $this->assertTrue($ret['vertical']);
  }
  public function testHasHorizontalDirections()
  {
    $index1 = 3000;
    $index2 = 3005;
    $elm1 = $this->invokeMethod($this->keyOP, 'getElement',array($index1));
    $elm2 = $this->invokeMethod($this->keyOP, 'getElement',array($index2));

    $ret = $this->invokeMethod($this->keyOP, 'hasDirections',array($elm1,$elm2));

    $this->assertTrue($ret['horizontal']);
    $this->assertFalse($ret['vertical']);
  }
  public function testWalkHorizontal()
  {
    $index1 = 1000;
    $index2 = 1005;
    $elm1 = $this->invokeMethod($this->keyOP, 'getElement',array($index1));
    $elm2 = $this->invokeMethod($this->keyOP, 'getElement',array($index2));
    $this->setProperty($this->keyOP, 'hdir', 'r');

    $this->invokeMethod($this->keyOP, 'walkHorizontal',array($elm1,$elm2));
    $path = $this->getProperty($this->keyOP, 'path');
    $this->assertEquals(5, $path['h']['r']);

  }
  public function testWalkVertical()
  {
    $index1 = 1000;
    $index2 = 3000;
    $elm1 = $this->invokeMethod($this->keyOP, 'getElement',array($index1));
    $elm2 = $this->invokeMethod($this->keyOP, 'getElement',array($index2));
    $this->setProperty($this->keyOP, 'vdir', 'u');

    $this->invokeMethod($this->keyOP, 'walkVertical',array($elm1,$elm2));
    $path = $this->getProperty($this->keyOP, 'path');
    $this->assertEquals(2, $path['v']['u']);

  }
  public function testGetElement()
  {
    $index = 2018;   //key index from layout
    $ret = $this->invokeMethod($this->keyOP, 'getElement',array($index));
    $retIndex = key($ret);

    $this->assertEquals($index, $retIndex);
  }
  public function testVerticalFirst()
  {
    $elmOne1 = array('row' => 1, 'space' => true);
    $elmOne2 = array('row' => 1, 'location' => 'left','space'=>false);

    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'left','onspace'=>true));
    $retTrue1 = $this->invokeMethod($this->keyOP, 'verticalFirst',array($elmOne1));
    $this->assertTrue($retTrue1);

    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'right','onspace'=>false));
    $retTrue1 = $this->invokeMethod($this->keyOP, 'verticalFirst',array($elmOne2));

    $this->assertTrue($retTrue1);
  }
  public function testVerticalNotFirst()
  {
    $elmOne1 = array('row' => 2, 'space' => true);
    $elmOne2 = array('row' => 1, 'location' => 'right','space'=>false);

    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'left','onspace'=>true));
    $retFalse1 = $this->invokeMethod($this->keyOP, 'verticalFirst',array($elmOne1));
    $this->assertTrue($retFalse1);

    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'right','onspace'=>false));
    $retFalse1 = $this->invokeMethod($this->keyOP, 'verticalFirst',array($elmOne2));

    $this->assertTrue($retFalse1);
  }
  public function testSPinBetween()
  {
    $this->setProperty($this->keyOP, 'elmOne', array('location'=>'left','space'=>false,'onspace'=>false));
    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'right','space'=>false,'onspace'=>false));
    $retTrue1 = $this->invokeMethod($this->keyOP, 'SPinBetween');

    $this->assertTrue($retTrue1);
  }
  public function testSPinBetweenNoLeft()
  {
    $this->setProperty($this->keyOP, 'elmOne', array('location'=>'left','space'=>false,'onspace'=>false));
    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'right','space'=>true,'onspace'=>false,'row' => 1));
    $this->setProperty($this->keyOP, 'hdir', 'r');
    $retFalse = $this->invokeMethod($this->keyOP, 'SPinBetween');

    $this->assertFalse($retFalse);
  }
  public function testSPinBetweenSameLocation()
  {

    $this->setProperty($this->keyOP, 'elmOne', array('location'=>'left','space'=>false));
    $this->setProperty($this->keyOP, 'elmTwo', array('location'=>'left','space'=>false));
    $retFalse1 = $this->invokeMethod($this->keyOP, 'SPinBetween');

    $this->assertFalse($retFalse1);

  }
  public function testSPinBetweenFromToSpace()
  {
    $this->setProperty($this->keyOP, 'elmOne', array('space'=>true));
    $retFalse1 = $this->invokeMethod($this->keyOP, 'SPinBetween');

    $this->setProperty($this->keyOP, 'elmTwo', array('space'=>true));
    $retFalse2 = $this->invokeMethod($this->keyOP, 'SPinBetween');


    $this->assertFalse($retFalse1);
    $this->assertFalse($retFalse2);

  }
  public function testGetScore()
  {
    $path1 = array('h' => array('l'=>4),'v'=>array('u'=>2));
    $path2 = array('h' => array('r'=>4),'v'=>array('d'=>2));
    $path3 = array('h' => array('r'=>4),'v'=>array('d'=>0));

    $score1 = $this->invokeMethod($this->keyOP, 'getScore',array($path1));
    $score2 = $this->invokeMethod($this->keyOP, 'getScore',array($path2));
    $score3 = $this->invokeMethod($this->keyOP, 'getScore',array($path3));

    $expectedScore = 4 + keyOP::DIR_SWITCH_WEIGHT*2; //4 steps + (weight of changes * number of changes)

    $this->assertEquals($score1, $score2);
    $this->assertEquals($expectedScore, $score3);

  }
  /**
   * @expectedException OutOfBoundsException
   * @expectedExceptionCode 9902
   */
  public function testPositionInLayoutWrongChar()
  {
    $this->invokeMethod($this->keyOP, 'positionInLayout',array('§'));
  }
   /**
   * @expectedException UnexpectedValueException
   * @expectedExceptionCode 9901
   */
  public function testPositionInLayoutEmptyParam()
  {
    $this->invokeMethod($this->keyOP, 'positionInLayout',array(''));
  }
  public function testPositioninLayout()
  {
    $ret = $this->invokeMethod($this->keyOP, 'positionInLayout',array('a'));

    $this->assertEquals(2000,$ret);
  }
}

?>