<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Domain\Model;

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
 * Testcase for a persistent transaction with local child
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class PersistentWithLocalTransactionTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function startChildOnCommitedTxCausesException()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	    $tx->startTx('childServiceName', 'childName', true);
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function startChildOnRolledbackTxCausesException()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->rollback();
	    $tx->startTx('childServiceName', 'childName', true);
	}
	
	/**
	 * @test
	 */
	public function startTxAddsChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName'));
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $returned = $tx->startTx('childServiceName', 'childName', true);
	    self::assertSame($txChild, $returned);
	    
	    $children = $tx->getChildren();
	    self::assertEquals(1, count($children));
	    self::assertSame($txChild, array_pop($children));
	}
	
	/**
	 * @test
	 */
	public function childTxCommitsOnCommit()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('commit'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('commit')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function childTxRollsbackOnRollback()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('rollback'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('rollback')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function childTxDestroyedOnDestroy()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('onDestroy'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('onDestroy')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function childTxVetosCommit()
	{
	    $ex = new \F3\BghObjects\Domain\TransactionConflictException();
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $partMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock->expects($this->never())->method('onPrepareCommit');
	    $partMock->expects($this->never())->method('onPerformCommit');
	    $tx->join($partMock);
	    
	    $txChild = $this->getMock('F3\BghObjects\Domain\Model\LocalTransaction', array(), array(null, null));
	    $txChild->expects($this->once())->method('commit')->will($this->throwException($ex));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->commit();
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function childTxVetosRollback()
	{
	    $ex = new \F3\BghObjects\Domain\TransactionConflictException();
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $partMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock->expects($this->never())->method('onPrepareRollback');
	    $partMock->expects($this->never())->method('onPerformRollback');
	    $tx->join($partMock);
	    
	    $txChild = $this->getMock('F3\BghObjects\Domain\Model\LocalTransaction', array(), array(null, null));
	    $txChild->expects($this->once())->method('rollback')->will($this->throwException($ex));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function startChildSetsChildInService()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    
	    $txsMock->expects($this->once())->method('resume')->with($txChild);
	    $tx->startTx('childServiceName', 'childName', true);
	}
	
	/**
	 * @test
	 */
	public function startChildDoesNotSetChildInService()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx2->_set('FLOW3_Persistence_Entity_UUID', 'parent2');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $tx2->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx2));
	    $tx->_set('txService', $txsMock);
	    
	    $txsMock->expects($this->never())->method('resume');
	    $tx->startTx('childServiceName', 'childName', true);
	}
	
	/**
	 * @test
	 */
	public function commitChildRemovesChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    
	    $txChild->commit();
	    
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function rollbackChildRemovesChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    
	    $txChild->rollback();
	    
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function destroyChildRemovesChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    
	    $txChild->onDestroy();
	    
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function destroyClearsChildren()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(3))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $tx->startTx('childServiceName', 'childName', true);
	    $tx->onDestroy();
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function onChildEndedIgnoresUnknownTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->onChildEnded($tx2);
	}
	
	/**
	 * @test
	 */
	public function startAddsChildNotToRepository()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\LocalTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->never())->method('add');
	    
	    $tx->startTx('childServiceName', 'childName', true);
	}
    	
}
