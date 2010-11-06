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
 * Testcase for entity repository result
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EntityRepositoryQueryResultTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function constructorSetsProperties()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertSame($storage, $qres->_get('storage'));
	    self::assertSame($qMock, $qres->_get('query'));
	    self::assertSame($qResMock, $qres->_get('queryResult'));
	}
	
	/**
	 * @test
	 */
	public function getQueryReturnsClone()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qResMock->expects($this->once())->method('getQuery')->will($this->returnValue('query'));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManager');
	    $omMock->expects($this->once())->method('create')->with('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', 'query', $storage)->will($this->returnValue('result'));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    $qres->_set('objectManager', $omMock);
	    self::assertEquals('result', $qres->getQuery());
	}
	
	/**
	 * @test
	 */
	public function getFirstReturnsAndAttachesObject()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('getFirst')->will($this->returnValue($obj));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertSame($obj, $qres->getFirst());
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj));
	}

	/**
	 * @test
	 */
	public function getFirstReturnsNullAndDoesNotAttachAnything()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qResMock->expects($this->once())->method('getFirst')->will($this->returnValue(null));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertNull($qres->getFirst());
	    self::assertEquals(0, $storage->count());
	}

	/**
	 * @test
	 */
	public function toArrayReturnsArrayAndAttachesObjects()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj1 = new \stdClass();
	    $obj2 = new \stdClass();
	    $qResMock->expects($this->once())->method('toArray')->will($this->returnValue(array($obj1, $obj2)));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertEquals(array($obj1, $obj2), $qres->toArray());
	    self::assertEquals(2, $storage->count());
	    self::assertTrue($storage->contains($obj1));
	    self::assertTrue($storage->contains($obj2));
	}
	
	/**
	 * @test
	 */
	public function countReturnsNumberOfObjects()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qResMock->expects($this->once())->method('count')->will($this->returnValue(7));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertEquals(7, $qres->count());
	}
	
	/**
	 * @test
	 */
	public function currentReturnsObjectAndAttachesIt()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('current')->will($this->returnValue($obj));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertSame($obj, $qres->current());
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj));
	}
	
	/**
	 * @test
	 */
	public function keyReturnsKey()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('key')->will($this->returnValue('mykey'));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertSame('mykey', $qres->key());
	}
		
	/**
	 * @test
	 */
	public function validReturnsValid()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('valid')->will($this->returnValue(true));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertTrue($qres->valid());
	}
	
	/**
	 * @test
	 */
	public function rewindCallsRewind()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('rewind');
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    $qres->rewind();
	}
	
	/**
	 * @test
	 */
	public function nextCallsNext()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('next');
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    $qres->next();
	}
	
	/**
	 * @test
	 */
	public function offsetUnsetCallsOffsetUnset()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('offsetUnset')->with('mykey');
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    $qres->offsetUnset('mykey');
	}
	
	/**
	 * @test
	 */
	public function offsetExistsCallsOffsetExists()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('offsetExists')->with('mykey')->will($this->returnValue(true));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertTrue($qres->offsetExists('mykey'));
	}
	
	/**
	 * @test
	 */
	public function offsetGetReturnsObjectAndAttachesIt()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('offsetGet')->with('mykey')->will($this->returnValue($obj));
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    self::assertSame($obj, $qres->offsetGet('mykey'));
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj));
	}
	
	/**
	 * @test
	 */
	public function offsetSetSetsObjectAndAttachesIt()
	{
	    $storage = new \SplObjectStorage();
	    $qMock = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
	    $qResMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $obj = new \stdClass();
	    $qResMock->expects($this->once())->method('offsetSet')->with('mykey', $obj);
	    $qres = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult', array('dummy'), array($qMock, $qResMock, $storage));
	    $qres->offsetSet('mykey', $obj);
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj));
	}
	
}
