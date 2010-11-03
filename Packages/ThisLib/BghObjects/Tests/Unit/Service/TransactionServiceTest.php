<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Service;

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
 * Testcase for a transaction service
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class TransactionServiceTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function resumeAndGet()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    self::assertNull($service->get());
	    $service->resume($tx);
	    self::assertSame($tx, $service->get());
	}
	
	/**
	 * @test
	 */
	public function destroyCallsOnDestroy()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $tx->expects($this->once())->method('onDestroy');
	    $service->resume($tx);
	    $service->destroy($tx);
	}

	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function resumeFailsForCommitedTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_COMMITED));
	    $service->resume($tx);
	}

	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function resumeFailsForRollbackedTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_ROLLBACKED));
	    $service->resume($tx);
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function resumeFailsForDestroyedTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_DESTROYED));
	    $service->resume($tx);
	}
	
	/**
	 * @test
	 */
	public function pauseIgnoresNoTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $service->pause();
	}
	
	/**
	 * @test
	 */
	public function pauseSetsToNull()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $service->resume($tx);
	    $service->pause();
	    self::assertNull($service->get());
	}
		
	/**
	 * @test
	 */
	public function pauseSetsToPrevious()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->exactly(2))->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $tx2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx2->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $service->resume($tx);
	    $service->resume($tx2);
	    $service->resume($tx);
	    self::assertSame($tx, $service->get());
	    $service->pause();
	    self::assertSame($tx2, $service->get());
	    $service->pause();
	    self::assertSame($tx, $service->get());
	    $service->pause();
	    self::assertNull($service->get());
	}
	
	/**
	 * @test
	 */
	public function startTxCreatesLocalTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->with('F3\BghObjects\Domain\Model\LocalTransaction', 'serviceName', 'name', null)->will($this->returnValue($tx));
	    $service->_set('objectManager', $omMock);
	    self::assertSame($tx, $service->startTx('serviceName', 'name', true));
	    self::assertSame($tx, $service->get());
	}
	
	/**
	 * @test
	 */
	public function startTxCreatesRemoteTx()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $tx->expects($this->once())->method('getState')->will($this->returnValue(\F3\BghObjects\Domain\Model\TransactionInterface::STATE_RUNNING));
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->once())->method('create')->with('F3\BghObjects\Domain\Model\PersistentTransaction', 'serviceName', 'name', null)->will($this->returnValue($tx));
	    $service->_set('objectManager', $omMock);
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $qresMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qresMock->expects($this->once())->method('count')->will($this->returnValue(0));
	    $txRepMock->expects($this->once())->method('findByServiceNameAndName')->with('serviceName', 'name')->will($this->returnValue($qresMock));
	    $txRepMock->expects($this->once())->method('add')->with($tx);
	    $service->_set('txRepository', $txRepMock);
	    self::assertSame($tx, $service->startTx('serviceName', 'name', false));
	    self::assertSame($tx, $service->get());
	}
	
	/**
	 * @test
	 * @expectedException \F3\BghObjects\Domain\TransactionConflictException
	 */
	public function startTxDeniesRemoteTxWithDuplicateName()
	{
	    $service = $this->getAccessibleMock('F3\BghObjects\Service\TransactionService', array('dummy'));
	    $tx = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $omMock = $this->getMock('F3\FLOW3\Object\ObjectManagerInterface');
	    $omMock->expects($this->never())->method('create');
	    $service->_set('objectManager', $omMock);
	    $txRepMock = $this->getMock('F3\BghObjects\Domain\Repository\TransactionRepositoryInterface');
	    $txRepMock->expects($this->never())->method('add');
	    $qresMock = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
	    $qresMock->expects($this->once())->method('count')->will($this->returnValue(1));
	    $txRepMock->expects($this->once())->method('findByServiceNameAndName')->with('serviceName', 'name')->will($this->returnValue($qresMock));
	    $service->_set('txRepository', $txRepMock);
	    $service->startTx('serviceName', 'name', false);
	}
	
}
