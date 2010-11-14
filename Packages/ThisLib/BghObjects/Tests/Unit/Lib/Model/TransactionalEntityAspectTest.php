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
 * Testcase for the transactional aspect test
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class TransactionalEntityAspectTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function txOnPrepareCommit()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->txOnPrepareCommit();
	}

	/**
	 * @test
	 */
	public function txOnPrepareRollback()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->txOnPrepareRollback();
	}

	/**
	 * @test
	 */
	public function txOnPerformCommit()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->txOnPerformCommit();
	}

	/**
	 * @test
	 */
	public function txOnPerformRollback()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->txOnPerformRollback();
	}

	/**
	 * @test
	 */
	public function txOnDestroy()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->txOnDestroy();
	}

	/**
	 * @test
	 */
	public function entityGetProperty()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->entityGetProperty();
	}

	/**
	 * @test
	 */
	public function entitySetProperty()
	{
	    // only for code coverage
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->entitySetProperty();
	}

	/**
	 * @test
	 */
	public function onPrepareCommit()
	{
	    // only for code coverage
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->onPrepareCommit($jpMock);
	}

	/**
	 * @test
	 */
	public function onPrepareRollback()
	{
	    // only for code coverage
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->onPrepareRollback($jpMock);
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithoutTransaction()
	{
	    // $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\TxEntity');
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue(null));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getAdviceChain')->will($this->returnValue($chainMock));
	    $chainMock->expects($this->once())->method('proceed')->with($jpMock)->will($this->returnValue('result'));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('result', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithLocalTransactionAndNoChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $entity->expects($this->once())->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_local')->will($this->returnValue(false));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $txMock->expects($this->once())->method('getParent')->will($this->returnValue(null));
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getAdviceChain')->will($this->returnValue($chainMock));
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    $chainMock->expects($this->once())->method('proceed')->with($jpMock)->will($this->returnValue('result'));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('result', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithLocalTransactionAndChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $storage = new \ArrayObject();
	    $storage['txid'] = new \ArrayObject();
	    $storage['txid']['foo'] = 'changed';
	    $entity->expects($this->once())->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_local')->will($this->returnValue($storage));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $txMock->expects($this->never())->method('getParent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('changed', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithLocalTransactionAndParentChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $storage = new \ArrayObject();
	    $storage['txid'] = new \ArrayObject();
	    $storage['txid']['foo'] = 'changed';
	    $entity->expects($this->exactly(2))->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_local')->will($this->returnValue($storage));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $txMock->expects($this->never())->method('getParent');
	    $txMock2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock2->expects($this->once())->method('getObjectId')->will($this->returnValue('childtxid'));
	    $txMock2->expects($this->once())->method('isLocal')->will($this->returnValue(true));
	    $txMock2->expects($this->once())->method('getParent')->will($this->returnValue($txMock));
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock2));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('changed', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithRemoteTransactionAndNoChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $entity->expects($this->once())->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_global')->will($this->returnValue(false));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $txMock->expects($this->once())->method('getParent')->will($this->returnValue(null));
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getAdviceChain')->will($this->returnValue($chainMock));
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    $chainMock->expects($this->once())->method('proceed')->with($jpMock)->will($this->returnValue('result'));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('result', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithRemoteTransactionAndChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $storage = new \ArrayObject();
	    $storage['txid'] = new \ArrayObject();
	    $storage['txid']['foo'] = 'changed';
	    $entity->expects($this->once())->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_global')->will($this->returnValue($storage));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $txMock->expects($this->never())->method('getParent');
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('changed', $aspect->getProperty($jpMock));
	}
	
	/**
	 * @test
	 */
	public function getPropertyWithRemoteTransactionAndParentChange()
	{
	    require_once __DIR__.'/Fixtures/TxEntityInterface.php';
	    $entity = $this->getMock('F3\BghObjects\Tests\Unit\Lib\Model\Fixtures\TxEntityInterface');
	    $storage = new \ArrayObject();
	    $storage['txid'] = new \ArrayObject();
	    $storage['txid']['foo'] = 'changed';
	    $entity->expects($this->exactly(2))->method('FLOW3_AOP_Proxy_getProperty')->with('BghObjects_transactionData_global')->will($this->returnValue($storage));
	    $txMock = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock->expects($this->once())->method('getObjectId')->will($this->returnValue('txid'));
	    $txMock->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $txMock->expects($this->never())->method('getParent');
	    $txMock2 = $this->getMock('F3\BghObjects\Domain\Model\TransactionInterface');
	    $txMock2->expects($this->once())->method('getObjectId')->will($this->returnValue('childtxid'));
	    $txMock2->expects($this->once())->method('isLocal')->will($this->returnValue(false));
	    $txMock2->expects($this->once())->method('getParent')->will($this->returnValue($txMock));
	    
	    $txsMock = $this->getMock('F3\BghObjects\Service\TransactionServiceInterface');
	    $txsMock->expects($this->once())->method('get')->will($this->returnValue($txMock2));
	    $chainMock = $this->getMock('F3\FLOW3\AOP\Advice\AdviceChain', array(), array(), '', false);
	    $jpMock = $this->getMock('F3\FLOW3\AOP\JoinPointInterface');
	    $jpMock->expects($this->once())->method('getMethodArguments')->will($this->returnValue(array('foo')));
	    $jpMock->expects($this->once())->method('getProxy')->will($this->returnValue($entity));
	    
	    $aspect = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntityAspect', array('dummy'));
	    $aspect->_set('txService', $txsMock);
	    self::assertEquals('changed', $aspect->getProperty($jpMock));
	}
	
}
