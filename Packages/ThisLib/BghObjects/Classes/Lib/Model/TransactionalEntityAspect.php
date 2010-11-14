<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Lib\Model;

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
 * An aspect realizing the transactional features of transactional entites
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @aspect
 * 
 * @todo introduce variables:
 *           TransactionEntity->BghObjects_transactionData_persistent
 *           TransactionEntity->BghObjects_transactionData_local
 *           Transaction->BghObjects_entities
 */
class TransactionalEntityAspect
{
    
    /**
     * @var \F3\BghObjects\Service\TransactionServiceInterface
     * @inject
     */
    protected $txService;
    
    /**
     * Pointcut to TransactionInterface->onPrepareCommit()
     * @pointcut method(.*->onPrepareCommit()) && within(F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function txOnPrepareCommit() {}
    
    /**
     * Pointcut to TransactionInterface->onPrepareRollback()
     * @pointcut method(.*->onPrepareRollback()) && within(F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function txOnPrepareRollback() {}
    
    /**
     * Pointcut to TransactionInterface->onPerformCommit()
     * @pointcut method(.*->onPerformCommit()) && within(F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function txOnPerformCommit() {}
    
    /**
     * Pointcut to TransactionInterface->onPerformRollback()
     * @pointcut method(.*->onPerformRollback()) && within(F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function txOnPerformRollback() {}
    
    /**
     * Pointcut to TransactionInterface->onDestroy()
     * @pointcut method(.*->onDestroy()) && within(F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function txOnDestroy() {}
    
    /**
     * Pointcut to TransactionalEntity->getProperty()
     * @pointcut method(.*->getProperty()) && within(F3\BghObjects\Lib\Model\TransactionalEntityInterface)
     */
    public function entityGetProperty() {}
    
    /**
     * Pointcut to TransactionalEntity->setProperty()
     * @pointcut method(.*->setProperty()) && within(F3\BghObjects\Lib\Model\TransactionalEntityInterface)
     */
    public function entitySetProperty() {}
    
    /**
     * Prepares a commit
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @before F3\BghObjects\Lib\Model\TransactionalEntityAspect->txOnPrepareCommit
     */
    public function onPrepareCommit(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        // will always be good since we simply overwrite conflicting changes
        // return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
    
    /**
     * Prepares a rollback
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @before F3\BghObjects\Lib\Model\TransactionalEntityAspect->txOnPrepareRollback
     */
    public function onPrepareRollback(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        // will always be good since we simply discard everything
        // return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
    
    /**
     * performs a commit
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @before F3\BghObjects\Lib\Model\TransactionalEntityAspect->txOnPerformCommit
     */
    public function onPerformCommit(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        $entities = $joinPoint->getProxy()->FLOW3_AOP_Proxy_getProperty('BghObjects_entities');
        if (is_object($entities))
        {
            /* @var $entities \SplObjectStorage */
            $entities->rewind();
            while ($entities->valid())
            {
                $val = $entities->key();
                $entities->next();
                if ($val instanceof \F3\FLOW3\AOP\ProxyInterface && $val instanceof \F3\BghObjects\Lib\Model\TransactionalEntity)
                {
                    $txdata = $val->FLOW3_AOP_Proxy_getProperty($joinPoint->getProxy()->isLocal() ? 'BghObjects_transactionData_local' : 'BghObjects_transactionData_global');
                    $txid = $joinPoint->getProxy()->getObjectId();
                    if (is_object($txdata) && isset($txdata[$txid]))
                    {
                        $txarr = $txdata[$txid];
                        $parent = $joinPoint->getProxy()->getParent();
                        if (is_object($parent))
                        {
                            // Set into parent transaction
                            $parentid = $parent->getObjectId();
                            if (!isset($txdata[$parentid]))
                            {
                                $txdata[$parentid] = $txarr;
                            }
                            else
                            {
                                foreach ($txarr as $name => $prop)
                                {
                                    $txdata[$parentid][$name] = $prop;
                                }
                            }
                        }
                        else
                        {
                            // no more parents; set the data into the object itself
                            foreach ($txarr as $name => $prop)
                            {
                                $val->FLOW3_AOP_Proxy_setProperty("_$name", $prop);
                            }
                        }
                        unset($txdata[$txid]);
                    }
                }
                else
                {
                    throw new \F3\BghObjects\Domain\Model\TransactionConflictException('Illegal entity registered with this transaction', 1287411829);
                }
            }
        }
        // return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
    
    /**
     * performs a rollback
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @before F3\BghObjects\Lib\Model\TransactionalEntityAspect->txOnPerformRollback
     */
    public function onPerformRollback(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        $entities = $joinPoint->getProxy()->FLOW3_AOP_Proxy_getProperty('BghObjects_entities');
        if (is_object($entities))
        {
            /* @var $entities \SplObjectStorage */
            $entities->rewind();
            while ($entities->valid())
            {
                $val = $entities->key();
                $entities->next();
                if ($val instanceof \F3\FLOW3\AOP\ProxyInterface && $val instanceof \F3\BghObjects\Lib\Model\TransactionalEntity)
                {
                    $txdata = $val->FLOW3_AOP_Proxy_getProperty($joinPoint->getProxy()->isLocal() ? 'BghObjects_transactionData_local' : 'BghObjects_transactionData_global');
                    $txid = $joinPoint->getProxy()->getObjectId();
                    if (is_object($txdata) && isset($txdata[$txid]))
                    {
                        unset($txdata[$txid]);
                    }
                }
                else
                {
                    throw new \F3\BghObjects\Domain\Model\TransactionConflictException('Illegal entity registered with this transaction', 1287411829);
                }
            }
        }
        // return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
        
    /**
     * performs a destroy
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @before F3\BghObjects\Lib\Model\TransactionalEntityAspect->txOnDestroy
     */
    public function onDestroy(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        $entities = $joinPoint->getProxy()->FLOW3_AOP_Proxy_getProperty('BghObjects_entities');
        if (is_object($entities))
        {
            /* @var $entities \SplObjectStorage */
            $entities->rewind();
            while ($entities->valid())
            {
                $val = $entities->key();
                $entities->next();
                if ($val instanceof \F3\FLOW3\AOP\ProxyInterface && $val instanceof \F3\BghObjects\Lib\Model\TransactionalEntity)
                {
                    $txdata = $val->FLOW3_AOP_Proxy_getProperty($joinPoint->getProxy()->isLocal() ? 'BghObjects_transactionData_local' : 'BghObjects_transactionData_global');
                    $txid = $joinPoint->getProxy()->getObjectId();
                    if (is_object($txdata) && isset($txdata[$txid]))
                    {
                        unset($txdata[$txid]);
                    }
                }
                else
                {
                    throw new \F3\BghObjects\Domain\Model\TransactionConflictException('Illegal entity registered with this transaction', 1287411829);
                }
            }
        }
        // return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
        
    /**
     * getProperty
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @around F3\BghObjects\Lib\Model\TransactionalEntityAspect->entityGetProperty
     */
    public function getProperty(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        $tx = $this->txService->get();
        if (is_object($tx))
        {
            $args = $joinPoint->getMethodArguments();
            $key = array_pop($args);
            $entity = $joinPoint->getProxy();
            while (is_object($tx))
            {
                $txid = $tx->getObjectId();
                $txdata = $entity->FLOW3_AOP_Proxy_getProperty($tx->isLocal() ? 'BghObjects_transactionData_local' : 'BghObjects_transactionData_global');
                if (is_object($txdata) && isset($txdata[$txid]) && isset($txdata[$txid][$key]))
                {
                    return $txdata[$txid][$key];
                }
                $tx = $tx->getParent();
            }
        }
        
        // default implementation
        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
        
    /**
     * setProperty
     * 
     * @param \F3\FLOW3\AOP\JoinPointInterface $joinPoint
     * 
     * @around F3\BghObjects\Lib\Model\TransactionalEntityAspect->entitySetProperty
     */
    public function setProperty(\F3\FLOW3\AOP\JoinPointInterface $joinPoint)
    {
        $tx = $this->txService->get();
        if (is_object($tx))
        {
            $args = $joinPoint->getMethodArguments();
            $key = array_pop($args);
            $val = array_pop($args);
            $entity = $joinPoint->getProxy();
            $txid = $tx->getObjectId();
            $txprop = $tx->isLocal() ? 'BghObjects_transactionData_local' : 'BghObjects_transactionData_global';
            $txdata = $entity->FLOW3_AOP_Proxy_getProperty($txprop);
            if (!is_object($txdata))
            {
                $txdata = new \ArrayObject();
                $entity->FLOW3_AOP_Proxy_setProperty($txprop, $txdata);
                $entities = $tx->FLOW3_AOP_Proxy_getProperty('BghObjects_entities');
                if (!is_object($entities))
                {
                    $entities = new \SplObjectStorage();
                    $tx->FLOW3_AOP_Proxy_setProperty('BghObjects_entities', $entities);
                }
                $entities->attach($entity);
            }
            if (!isset($txdata[$txid]))
            {
                $txdata[$txid] = new \ArrayObject();
            }
            $txdata[$txid][$key] = $val;
            return;
        }
        
        // default implementation
        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
    
}
