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
 * Testcase for a persistent transactions
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class PersistentTransactionTest extends \F3\Testing\BaseTestCase
{
    
	/**
	 * @test
	 */
	public function constructorSetsProperties()
	{
		$tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
		
		self::assertEquals('serviceName', $tx->getServiceName());
		self::assertEquals('name', $tx->getName());
	}
	
	/**
	 * @test
	 */
	public function newTxSetsCorrectState()
	{
	    $tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_RUNNING, $tx->getState());
	}

	/**
	 * @test
	 */
	public function txIsNotLocal()
	{
	    $tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    
	    self::assertFalse($tx->isLocal());
	}

	/**
	 * @test
	 */
	public function txIsPersistent()
	{
	    $tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    
	    self::assertTrue($tx->isPersistent());
	}
	
	/**
	 * @test
	 */
	public function newTxHasNoParent()
	{
	    $tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    
	    self::assertNull($tx->getParent());
	}
	
	/**
	 * @test
	 */
	public function txHasExpectedParent()
	{
	    $tx1 = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    $tx2 = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name', $tx1);
	    
	    self::assertNotNull($tx2->getParent());
	    self::assertSame($tx1, $tx2->getParent());
	}
	
	/**
	 * @test
	 */
	public function commitTxSetsCorrectState()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_RUNNING, $tx->getState());
	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_COMMITED, $tx->getState());
	}
			
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function commitTx2TimesCausesException()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_RUNNING, $tx->getState());
	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function rollbackTxSetsCorrectState()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_RUNNING, $tx->getState());
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->rollback();
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_ROLLBACKED, $tx->getState());
	}
			
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function rollbackTx2TimesCausesException()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    
	    self::assertEquals(\F3\BghObjects\Domain\Model\PersistentTransaction::STATE_RUNNING, $tx->getState());
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->rollback();
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function newTxHasNoChildren()
	{
	    $tx = new \F3\BghObjects\Domain\Model\PersistentTransaction('serviceName', 'name');
	    
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function txCommitCallsTwoPhaseCommits()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $partMock1 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock1->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $partMock1->expects($this->once())->method('onPrepareCommit');
	    $partMock1->expects($this->once())->method('onPerformCommit');
	    $tx->join($partMock1);
	    $partMock2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock2->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $partMock2->expects($this->once())->method('onPrepareCommit');
	    $partMock2->expects($this->once())->method('onPerformCommit');
	    $tx->join($partMock2);
	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function txCommitCallsTwoPhaseRollbacks()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $partMock1 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock1->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $partMock1->expects($this->once())->method('onPrepareRollback');
	    $partMock1->expects($this->once())->method('onPerformRollback');
	    $tx->join($partMock1);
	    $partMock2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock2->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $partMock2->expects($this->once())->method('onPrepareRollback');
	    $partMock2->expects($this->once())->method('onPerformRollback');
	    $tx->join($partMock2);
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->rollback();
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function txCommitAbortsTwoPhaseCommits()
	{
	    $ex = new \F3\BghObjects\Domain\TransactionConflictException();
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->never())->method('get');
	    $tx->_set('txService', $txsMock);
	    $partMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock->expects($this->once())->method('onPrepareCommit')->will($this->throwException($ex));
	    $partMock->expects($this->never())->method('onPerformCommit');
	    $tx->join($partMock);
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function txCommitAbortsTwoPhaseRollbacks()
	{
	    $ex = new \F3\BghObjects\Domain\TransactionConflictException();
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->never())->method('get');
	    $tx->_set('txService', $txsMock);
	    $partMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock->expects($this->once())->method('onPrepareRollback')->will($this->throwException($ex));
	    $partMock->expects($this->never())->method('onPerformRollback');
	    $tx->join($partMock);
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->rollback();
	}
	
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
	    $tx->startTx('childServiceName', 'childName', false);
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
	    $tx->startTx('childServiceName', 'childName', false);
	}
	
	/**
	 * @test
	 */
	public function startTxAddsChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName'));
	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $returned = $tx->startTx('childServiceName', 'childName', false);
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
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('commit'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('commit')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
	    	    	    
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function childTxRollsbackOnRollback()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('rollback'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('rollback')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
	    	    	    	    
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function childTxDestroyedOnDestroy()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('onDestroy'), array(null, null, $tx));
	    $txChild->expects($this->once())->method('onDestroy')->will($this->returnCallback(function() use ($tx, $txChild) {$tx->onChildEnded($txChild);}));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    	    	    	    
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
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
	    
	    $txChild = $this->getMock('F3\BghObjects\Domain\Model\PersistentTransaction', array(), array(null, null));
	    $txChild->expects($this->once())->method('commit')->will($this->throwException($ex));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', false);
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
	    
	    $txChild = $this->getMock('F3\BghObjects\Domain\Model\PersistentTransaction', array(), array(null, null));
	    $txChild->expects($this->once())->method('rollback')->will($this->throwException($ex));
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function startChildSetsChildInService()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    
	    $txsMock->expects($this->once())->method('resume')->with($txChild);
	    $tx->startTx('childServiceName', 'childName', false);
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
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
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
	    $tx->startTx('childServiceName', 'childName', false);
	}
	
	/**
	 * @test
	 */
	public function commitChildRemovesChild()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    
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
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    
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
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName', $tx));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    
	    $txChild->onDestroy();
	    
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function commitNotifiesService()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    $txsMock->expects($this->once())->method('pause');
	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function destroySetsState()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->onDestroy();
	    self::assertEquals(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_DESTROYED, $tx->getState());
	}
	
	/**
	 * @test
	 */
	public function destroySetsStateAfterCommit()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $tx->commit();
	    $tx->onDestroy();
	    self::assertEquals(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_DESTROYED, $tx->getState());
	}
		
	/**
	 * @test
	 */
	public function destroySetsStateAfterRollback()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->exactly(2))->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);

	    $tx->rollback();
	    $tx->onDestroy();
	    self::assertEquals(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_DESTROYED, $tx->getState());
	}
	
	/**
	 * @test
	 */
	public function destroyClearsChildren()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
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
	    
	    $tx->startTx('childServiceName', 'childName', false);
	    $tx->onDestroy();
	    self::assertEquals(0, count($tx->getChildren()));
	}
	
	/**
	 * @test
	 */
	public function destroyInvokesParticipants()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $partMock1 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock1->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $partMock1->expects($this->once())->method('onDestroy');
	    $tx->join($partMock1);
	    $partMock2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock2->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $partMock2->expects($this->once())->method('onDestroy');
	    $tx->join($partMock2);
	    
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 */
	public function destroyInvokesParticipantsAndIgnoresException()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $partMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionParticipantInterface');
	    $partMock->expects($this->once())->method('onDestroy')->will($this->throwException(new \Exception()));
	    $tx->join($partMock);
	    
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 */
	public function commitPausesTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->once())->method('pause');
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function rollbackPausesTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->once())->method('pause');
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function destroyPausesTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->once())->method('pause');
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 */
	public function commitDoesNotPauseTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx2->_set('FLOW3_Persistence_Entity_UUID', 'parent2');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx2));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->never())->method('pause');
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function rollbackDoesNotPauseTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx2->_set('FLOW3_Persistence_Entity_UUID', 'parent2');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx2));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->never())->method('pause');
	    $tx->rollback();
	}
	
	/**
	 * @test
	 */
	public function destroyDoesNotPauseTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx2->_set('FLOW3_Persistence_Entity_UUID', 'parent2');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($tx2));
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    
	    $txsMock->expects($this->never())->method('pause');
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 */
	public function onChildEndedIgnoresUnknownTx()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    $tx2 = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx2->_set('FLOW3_Persistence_Entity_UUID', 'parent2');
	    $tx->onChildEnded($tx2);
	}
	
	/**
	 * @test
	 */
	public function startAddsChildToRepository()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txChild = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('childServiceName', 'childName'));
	    $txChild->_set('FLOW3_Persistence_Entity_UUID', 'child');
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->will($this->returnValue($txChild));
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $tx->_set('txService', $txsMock);
	    $txChild->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txChild->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->once())->method('add')->with($txChild);
	    
	    $tx->startTx('childServiceName', 'childName', false);
	}
	
	/**
	 * @test
	 */
	public function commitRemovesTxFromRepository()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->once())->method('remove')->with($tx);
	    
	    $tx->commit();
	}
	
	/**
	 * @test
	 */
	public function rollbackRemovesTxFromRepository()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->once())->method('remove')->with($tx);
	    
	    $tx->rollback();
	}
		
	/**
	 * @test
	 */
	public function destroyRemovesTxFromRepository()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $tx->_set('txService', $txsMock);
	    	    	    	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->once())->method('remove')->with($tx);
	    
	    $tx->onDestroy();
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function startDeniesDuplicateName()
	{
	    $tx = $this->getAccessibleMock('F3\BghObjects\Domain\Model\PersistentTransaction', array('dummy'), array('serviceName', 'name'));
	    $tx->_set('FLOW3_Persistence_Entity_UUID', 'parent');
	    
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->never())->method('create');
	    $tx->_set('objectManager', $omMock);
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $tx->_set('txService', $txsMock);
	    
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $tx->_set('txRepository', $txRepMock);
	    $txRepMock->expects($this->once())->method('findByServiceNameAndName')->with('serviceName', 'name')->will($this->returnValue($tx));
	    
	    $tx->startTx('serviceName', 'name', false);
	}
	
}
