<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Lib\Model;

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
 * Testcase for a standard entity
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EntityTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function getObjectId()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('FLOW3_Persistence_Entity_UUID', 'myid');
	    self::assertEquals('myid', $entity->getObjectId());
	}
	
	/**
	 * @test
	 */
	public function entityTakesAutocommitTrueDuringConstruction()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    self::assertTrue($entity->isAutoCommit());
	}
	
	/**
	 * @test
	 */
	public function entityTakesAutocommitFalseDuringConstruction()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    self::assertFalse($entity->isAutoCommit());
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithAutocommitTrue()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithAutocommitFalse()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithAutocommitFalseAndNotChanged()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_set('uncommitedBean', array('foo2', 'bar2'));
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithAutocommitFalseAndChanged()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_set('uncommitedBean', array('foo' => 'bar2'));
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function setPropertyChangesPropertyWithAutocommitTrue()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function setPropertyChangesPropertyWithAutocommitFalse()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	    self::assertEquals('bar', $entity->_get('_foo'));
	}
	
	/**
	 * @test
	 */
	public function commitChangesValues()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->commit();
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	    self::assertEquals('bar2', $entity->_get('_foo'));
	}
	
	/**
	 * @test
	 */
	public function rollbackResetsValues()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->rollback();
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	    self::assertEquals('bar', $entity->_get('_foo'));
	}
	
	/**
	 * @test
	 */
	public function setAutoCommitFalseChangesBehaviourOfSetProperty()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->setAutoCommit(false);
	    self::assertFalse($entity->isAutoCommit());
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->rollback();
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	    self::assertEquals('bar', $entity->_get('_foo'));
	}
	
	/**
	 * @test
	 */
	public function setAutoCommitTrueCommitsAutomatically()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->setAutoCommit(true);
	    self::assertTrue($entity->isAutoCommit());
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	    self::assertEquals('bar2', $entity->_get('_foo'));
	}
	
	/**
	 * @test
	 */
	public function hasChangesWithAutocommitTrue()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    self::assertFalse($entity->hasChanges());
	}
	
	/**
	 * @test
	 */
	public function hasChangesWithAutocommitFalse()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    self::assertTrue($entity->hasChanges());
	}
		
	/**
	 * @test
	 */
	public function hasChangesWithAutocommitFalseAfterCommit()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->commit();
	    self::assertFalse($entity->hasChanges());
	}
		
	/**
	 * @test
	 */
	public function hasChangesWithAutocommitFalseAfterRollback()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->once())->method('isAutoCommit')->will($this->returnValue(false));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    $entity->rollback();
	    self::assertFalse($entity->hasChanges());
	}
	
	/**
	 * @test
	 */
	public function entityTakesAutocommitTrueDuringWakeup()
	{
	    $reposMock = $this->getMock('F3\BghObjects\Lib\Repository\EntityRepositoryInterface');
	    $reposMock->expects($this->at(1))->method('isAutoCommit')->will($this->returnValue(false));
	    $reposMock->expects($this->at(2))->method('isAutoCommit')->will($this->returnValue(true));
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\Entity', array('dummy'), array($reposMock));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManager');
	    $omMock->expects($this->once())->method('get')->with('reposClass')->will($this->returnValue($reposMock));
	    $entity->_set('objectManager', $omMock);
	    $entity->_set('repository', false);
	    $entity->_set('repositoryClassName', false);
	    $entity->__wakeup();
	    self::assertTrue($entity->isAutoCommit());
	}
	
}
