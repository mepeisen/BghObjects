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
 * Testcase for entity repository
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EntityRepositoryTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function constructorCreatesStorage()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    self::assertInstanceOf('SplObjectStorage', $rep->_get('objectMap'));
	}
	
	/**
	 * @test
	 */
	public function commitWillCommitChildren()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $storage = $rep->_get('objectMap');
	    $obj1 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj1->expects($this->once())->method('commit');
	    $obj2 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj2->expects($this->once())->method('commit');
	    $storage->attach($obj1);
	    $storage->attach($obj2);
	    $rep->commit();
	}
	
	/**
	 * @test
	 */
	public function rollbackWillRollbackChildren()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $storage = $rep->_get('objectMap');
	    $obj1 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj1->expects($this->once())->method('rollback');
	    $obj2 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj2->expects($this->once())->method('rollback');
	    $storage->attach($obj1);
	    $storage->attach($obj2);
	    $rep->rollback();
	}
	
	/**
	 * @test
	 */
	public function setAutoCommitWillSetAutoCommitChildren()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $storage = $rep->_get('objectMap');
	    $obj1 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj1->expects($this->once())->method('setAutoCommit')->with(true);
	    $obj2 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj2->expects($this->once())->method('setAutoCommit')->with(true);
	    $storage->attach($obj1);
	    $storage->attach($obj2);
	    $rep->setAutoCommit(true);
	}
	
	/**
	 * @test
	 */
	public function isAutoCommit()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    self::assertTrue($rep->isAutoCommit());
	    $rep->setAutoCommit(false);
	    self::assertFalse($rep->isAutoCommit());
	    $rep->setAutoCommit(true);
	    self::assertTrue($rep->isAutoCommit());
	}
	
	/**
	 * @test
	 */
	public function hasChanges()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $storage = $rep->_get('objectMap');
	    $obj1 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj1->expects($this->exactly(2))->method('hasChanges')->will($this->returnValue(false));
	    $obj2 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj2->expects($this->once())->method('hasChanges')->will($this->returnValue(true));
	    self::assertFalse($rep->hasChanges());
	    $storage->attach($obj1);
	    self::assertFalse($rep->hasChanges());
	    $storage->attach($obj2);
	    self::assertTrue($rep->hasChanges());
	}
	
	/**
	 * @test
	 */
	public function addAttachesObject()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $rep->_set('objectType', 'F3\BghObjects\Lib\Model\Entity');
	    $storage = $rep->_get('objectMap');
	    $obj = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $rep->add($obj);
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj));
	}
	
	/**
	 * @test
	 */
	public function removeDetachesObject()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $rep->_set('objectType', 'F3\BghObjects\Lib\Model\Entity');
	    $storage = $rep->_get('objectMap');
	    $obj = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $storage->attach($obj);
	    $rep->remove($obj);
	    self::assertEquals(0, $storage->count());
	}
	
	/**
	 * @test
	 */
	public function replaceReplacesObject()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $rep->_set('objectType', 'F3\BghObjects\Lib\Model\Entity');
	    $pmMock = $this->getMock('F3\FLOW3\Persistence\PersistenceManagerInterface');
	    $pmMock->expects($this->any())->method('getIdentifierByObject')->will($this->returnValue(null));
	    $rep->_set('persistenceManager', $pmMock);
	    $storage = $rep->_get('objectMap');
	    $obj1 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $obj2 = $this->getMock('F3\BghObjects\Lib\Model\Entity', array(), array(), '', false);
	    $rep->_get('addedObjects')->attach($obj1);
	    $storage->attach($obj1);
	    $rep->replace($obj1, $obj2);
	    self::assertEquals(1, $storage->count());
	    self::assertTrue($storage->contains($obj2));
	}
	
	/**
	 * @test
	 */
	public function createQuery()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $storage = $rep->_get('objectMap');
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManager');
	    $omMock->expects($this->once())->method('create')->with('F3\BghObjects\Lib\Repository\EntityRepositoryQuery')->will($this->returnValue('result'));
	    $rep->_set('objectManager', $omMock);
	    $qfMock = $this->getMock('F3\FLOW3\Persistence\QueryFactoryInterface');
	    $rep->_set('queryFactory', $qfMock);
	    self::assertEquals('result', $rep->createQuery());
	}
	
	/**
	 * @test
	 * @expectedException \F3\FLOW3\Exception
	 */
	public function getObjectNameThrowsException()
	{
	    $rep = $this->getAccessibleMock('F3\BghObjects\Lib\Repository\EntityRepository', array('dummy'));
	    $rep->getObjectName();
	}
	
}
