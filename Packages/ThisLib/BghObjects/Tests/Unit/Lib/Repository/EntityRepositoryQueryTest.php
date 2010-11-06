<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Lib\Repository;

/*                                                                        *
 * This script belongs to the FLOW3 package "BghObjects"                  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Testcase for entity repository query
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EntityRepositoryQueryTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function constructorSetsProperties()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertSame($storage, $query->_get('storage'));
	    self::assertSame($qMock, $query->_get('query'));
	}
	
	/**
	 * @test
	 */
	public function executeCreatesQueryResultObject()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('execute')->will($this->returnValue('query'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManager');
	    $omMock->expects($this->once())->method('create')->with('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', $query, 'query', $storage)->will($this->returnValue('result'));
	    $query->_set('objectManager', $omMock);
	    self::assertEquals('result', $query->execute());
	}
	
	/**
	 * @test
	 */
	public function setOrderingsCallsMethodAndReturnsThis()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('setOrderings')->with(array('foo'=>'bar'))->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertSame($query, $query->setOrderings(array('foo'=>'bar')));
	}
	
	/**
	 * @test
	 */
	public function setLimitCallsMethodAndReturnsThis()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('setLimit')->with(17)->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertSame($query, $query->setLimit(17));
	}
	
	/**
	 * @test
	 */
	public function setOffsetCallsMethodAndReturnsThis()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('setOffset')->with(17)->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertSame($query, $query->setOffset(17));
	}
	
	/**
	 * @test
	 */
	public function matchingCallsMethodAndReturnsThis()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('matching')->with('constraint')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertSame($query, $query->matching('constraint'));
	}
	
	/**
	 * @test
	 */
	public function logicalAndCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('logicalAnd')->with('constraint')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->logicalAnd('constraint'));
	}
	
	/**
	 * @test
	 */
	public function logicalOrCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('logicalOr')->with('constraint')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->logicalOr('constraint'));
	}
	
	/**
	 * @test
	 */
	public function logicalNotCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('logicalNot')->with('constraint')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->logicalNot('constraint'));
	}
	
	/**
	 * @test
	 */
	public function equalsCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('equals')->with('prop', 'val', true)->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->equals('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function likeCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('like')->with('prop', 'val', true)->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->like('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function containsCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('contains')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->contains('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function isEmptyCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('isEmpty')->with('prop')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->isEmpty('prop'));
	}
	
	/**
	 * @test
	 */
	public function inCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('in')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->in('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function lessThanMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('lessThan')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->lessThan('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function lessThanOrEqualCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('lessThanOrEqual')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->lessThanOrEqual('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function greaterThanMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('greaterThan')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->greaterThan('prop', 'val'));
	}
	
	/**
	 * @test
	 */
	public function greaterThanOrEqualCallsMethod()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qMock->expects($this->once())->method('greaterThanOrEqual')->with('prop', 'val')->will($this->returnValue('foo'));
	    $query = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', array('dummy'), array($qMock, $storage));
	    self::assertEquals('foo', $query->greaterThanOrEqual('prop', 'val'));
	}
	
}
