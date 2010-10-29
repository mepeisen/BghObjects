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
 * Interface for transactions. Transactions may be identified by getServiceName() and
 * getName(). Anonymous transactions may not contain a name or service name.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface TransactionInterface
{
    
    /**
     * Transaction is running; that does not mean this transaction is the active one but that the transaction can
     * be still used and that it can become active.
     * @var int
     */
    const STATE_RUNNING = 1;
    
    /**
     * Transaction was commited
     * @var int
     */
    const STATE_COMMITED = 2;
    
    /**
     * Transaction was rolled back
     * @var int
     */
    const STATE_ROLLBACKED = 3;
    
    /**
     * Transaction was destroyed
     * @var int
     */
    const STATE_DESTROYED = 4;
    
    /**
     * Gets the current transaction state; see the STATE constants in this class
     * @return int
     */
    public function getState();
    
    /**
     * Returns the unique object id of this transaction (database uuid)
     * 
     * @return string
     */
    public function getObjectId();
    
    /**
     * Returns the unique name of this transaction or null if this is an anonymous transaction
     * @return string
     */
    public function getName();
    
    /**
     * Returns the service name; the originator of this transaction
     * @return string
     */
    public function getServiceName();
    
    /**
     * Returns true if this is a local transaction (not persistent; limited to the current request)
     * @return boolean
     */
    public function isLocal();
    
    /**
     * Returns true if this is a global transaction (persistent; can be used with multiple requests)
     * @return boolean
     */
    public function isPersistent();
    
    /**
     * Returns the parent transaction or null if there is no parent transaction
     * @return \F3\BghObjects\Domain\Model\TransactionInterface
     */
    public function getParent();
    
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
    public function startTx($serviceName, $name, $isLocal);
    
    /**
     * Returns the child transactions that were started and not commited/rolled back
     * 
     * @return array(\F3\BghObjects\Domain\Model\TransactionInterface)
     */
    public function getChildren();
    
    // **** transaction management (use this methods to do commits and rollbacks)
    
    /**
     * Commits the changes made in this transaction to the parent transaction. If this is the top most
     * transaction the changes will be commited to the database. If this transaction has child transactions
     * they will be commited at first.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function commit();
    
    /**
     * Rolls back the changes made within this transaction. Rollback should always be successful since this means
     * discarding every change without respecting the resulting object state. However this method may throw an
     * exception as well to let listeners implent their own logic.
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function rollback();
    
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
    public function onPrepareCommit();
    
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
    public function onPrepareRollback();
    
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
    public function onPerformCommit();
    
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
    public function onPerformRollback();
    
    /**
     * Destroy this session. Do not call this directly. It is only used to let aspects implement their logic.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction. Destroying transactions must always be valid and must never throw exceptions.
     */
    public function onDestroy();
    
    /**
     * Notifies the parent transaction that a child ended its work
     * @param \F3\BghObjects\Domain\Model\Transaction $tx
     */
    public function onChildEnded(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    // ***** participants
    
    /**
     * Let the participant join the transaction watching for state changes
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionParticipantInterface $participant
     */
    public function join(\F3\BghObjects\Domain\Model\TransactionParticipantInterface $participant);
    
}
