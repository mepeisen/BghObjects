<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Domain\Model;

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
 * A local non-persistent transaction
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class LocalTransaction implements \F3\BghObjects\Domain\Model\TransactionInterface
{
    
    /**
     * The current state
     * @var int
     */
    protected $state;
    
    /**
     * The object id of this transaction
     * @var string
     */
    protected $objectId;
    
    /**
     * The transaction counter
     * @var int
     */
    protected static $txCounter = 0;
    
    /**
     * The transaction name
     * @var string
     */
    protected $name;
    
    /**
     * The service name
     * @var string
     */
    protected $serviceName;
    
    /**
     * The parent
     * @var \F3\BghObjects\Domain\Model\TransactionInterface
     */
    protected $parent;
    
    /**
     * Child transactions
     * @var array(\F3\BghObjects\Domain\Model\TransactionInterface)
     */
    protected $children = array();
    
    /**
     * The object manager
     * @var \F3\FLOW3\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;
    
     /**
     * @var \F3\BghObjects\Service\TransactionServiceInterface
     * @inject
     */
    protected $txService;
    
    /**
     * The transaction participants
     * @var array(\F3\BghObjects\Domain\Model\TransactionParticipantInterface)
     */
    protected $participants = array();
    
    /**
     * The spl storage to holde the entity objects
     * @var \SplObjectStorage
     * @todo should be introduced by TransactionalEntityAspect as soon as FLOW3 supports introducing variables;
     *       currently we need to store this property in this class so that the persistence sees it and persists
     *       it.
     */
    protected $BghObjects_entities = null;
    
    /**
     * Constructor
     * 
     * @param string                                           $serviceName
     * @param string                                           $name
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $parent
     */
    public function __construct($serviceName, $name, $parent = null)
    {
        $this->state = self::STATE_RUNNING;
        $this->objectId = "{local:#".(self::$txCounter++)."}";
        $this->serviceName = $serviceName;
        $this->name = $name;
        $this->parent = $parent;
    }
    
    /**
     * Gets the current transaction state; see the STATE constants in this class
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Returns the unique object id of this transaction (database uuid)
     * 
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }
    
    /**
     * Returns the unique name of this transaction or null if this is an anonymous transaction
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the service name; the originator of this transaction
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
    
    /**
     * Returns true if this is a local transaction (not persistent; limited to the current request)
     * @return boolean
     */
    public function isLocal()
    {
        return true;
    }
    
    /**
     * Returns true if this is a global transaction (persistent; can be used with multiple requests)
     * @return boolean
     */
    public function isPersistent()
    {
        return false;
    }
    
    /**
     * Returns the parent transaction or null if there is no parent transaction
     * @return \F3\BghObjects\Domain\Model\TransactionInterface
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Starts a new transaction as a child of the current one; lets the new child transaction become the active transaction
     * if and only if the current transaction is this one (the parent of the new child).
     * 
     * @param string  $serviceName Name of the service class/ originator
     * @param string  $name        Name of the transaction or null for anonymous transactions
     * @param boolean $isLocal     true to create a local transaction; false to create a persistent transaction
     * 
     * @return \F3\BghObjects\Domain\Model\TransactionInterface
     */
    public function startTx($serviceName, $name, $isLocal)
    {
        if ($this->state != self::STATE_RUNNING)
        {
            throw new \F3\BghObjects\Domain\TransactionConflictException('Transaction not running.', 1287477177);
        }
        if (!$isLocal)
        {
            throw new \F3\BghObjects\Domain\TransactionConflictException('Cannot created nested persistent exception for local transaction', 1287477173);
        }
        $child = $this->objectManager->create('F3\BghObjects\Domain\Model\LocalTransaction', $serviceName, $name, $this);
        $this->children[] = $child;
        $cur = $this->txService->get();
        if (is_object($cur) && $cur->getObjectId() == $this->getObjectId())
        {
            $this->txService->resume($child);
        }
        return $child;
    }
    
    /**
     * Returns the child transactions that were started and not commited/rolled back
     * 
     * @return array(\F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    // **** transaction management (use this methods to do commits and rollbacks)
    
    /**
     * Commits the changes made in this transaction to the parent transaction. If this is the top most
     * transaction the changes will be commited to the database. If this transaction has child transactions
     * they will be commited at first.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function commit()
    {
        if ($this->state != self::STATE_RUNNING)
        {
            throw new \F3\BghObjects\Domain\TransactionConflictException('Transaction not running.', 1287477174);
        }
        while (count($this->children) > 0)
        {
            $child = $this->children[0];
            $child->commit(); // will remove the child from children array by calling onChildEnded
        }
        
        // start two-phase-commit
        $this->onPrepareCommit();
        
        // notify parent
        $this->state = self::STATE_COMMITED;
        if (is_object($this->parent))
        {
            $this->parent->onChildEnded($this);
        }
        
        // do the commit
        $this->onPerformCommit();
        
        $cur = $this->txService->get();
        if (is_object($cur) && $cur->getObjectId() == $this->getObjectId())
        {
            $this->txService->pause();
        }
    }
    
    /**
     * Rolls back the changes made within this transaction. Rollback should always be successful since this means
     * discarding every change without respecting the resulting object state. However this method may throw an
     * exception as well to let listeners implent their own logic.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function rollback()
    {
        if ($this->state != self::STATE_RUNNING)
        {
            throw new \F3\BghObjects\Domain\TransactionConflictException('Transaction not running.', 1287477175);
        }
        while (count($this->children) > 0)
        {
            $child = $this->children[0];
            $child->rollback(); // will remove the child from children array by calling onChildEnded
        }
        
        // start two-phase-rollback
        $this->onPrepareRollback();
        
        // notify parent
        $this->state = self::STATE_ROLLBACKED;
        if (is_object($this->parent))
        {
            $this->parent->onChildEnded($this);
        }
        
        // do the rollback
        $this->onPerformRollback();
        
        $cur = $this->txService->get();
        if (is_object($cur) && $cur->getObjectId() == $this->getObjectId())
        {
            $this->txService->pause();
        }
    }
    
    // ***** internal methods (use the following in aspects for transaction listeners)
    
    /**
     * Prepares a two-phase commit (pre commit). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase commits.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPrepareCommit()
    {
        foreach ($this->participants as $p)
        {
            $p->onPrepareCommit($this);
        }
    }
    
    /**
     * Prepares a two-phase rollback (pre rollback). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase rollbacks.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPrepareRollback()
    {
        foreach ($this->participants as $p)
        {
            $p->onPrepareRollback($this);
        }
    }
    
    /**
     * Performs a two-phase commit (do the commit). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase commits.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPerformCommit()
    {
        foreach ($this->participants as $p)
        {
            $p->onPerformCommit($this);
        }
        $this->participants = array();
    }
    
    /**
     * Performs a two-phase rollback (do the rollback). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase rollbacks.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPerformRollback()
    {
        foreach ($this->participants as $p)
        {
            $p->onPerformRollback($this);
        }
        $this->participants = array();
    }
    
    /**
     * Destroy this session. Do not call this directly. It is only used to let aspects implement their logic.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction. Destroying transactions must always be valid and must never throw exceptions.
     */
    public function onDestroy()
    {
        // clear the children
        $this->state = self::STATE_DESTROYED;
        foreach ($this->children as $child)
        {
            try
            {
                $child->onDestroy();
            }
            catch (\Exception $e)
            {
                // simply ignore any of the exceptions
            }
        }
        $this->children = array();
        if (is_object($this->parent))
        {
            try
            {
                $this->parent->onChildEnded($this);
            }
            catch (\Exception $e)
            {
                // simply ignore any of the exceptions
            }
        }
        foreach ($this->participants as $p)
        {
            try
            {
                $p->onDestroy($this);
            }
            catch (\Exception $e)
            {
                // simply ignore any of the exceptions
            }
        }
        $this->participants = array();
        
        $cur = $this->txService->get();
        if (is_object($cur) && $cur->getObjectId() == $this->getObjectId())
        {
            $this->txService->pause();
        }
    }

    /**
     * Notifies the parent transaction that a child ended its work
     * @param \F3\BghObjects\Domain\Model\Transaction $tx
     */
    public function onChildEnded(\F3\BghObjects\Domain\Model\TransactionInterface $tx)
    {
        // remove the child
        foreach ($this->children as $key => $val)
        {
            if ($val->getObjectId() == $tx->getObjectId())
            {
                unset($this->children[$key]);
                // reindex
                $this->children = array_values($this->children);
                return;
            }
        }
    }
        
    // ***** participants
    
    /**
     * Let the participant join the transaction watching for state changes
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionParticipantInterface $participant
     */
    public function join(\F3\BghObjects\Domain\Model\TransactionParticipantInterface $participant)
    {
        $this->participants[] = $participant;
    }
    
}
