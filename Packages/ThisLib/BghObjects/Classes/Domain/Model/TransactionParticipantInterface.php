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
 * Interface for transaction participants. Transaction participants are used within transactions as soon
 * as the transaction state changes.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface TransactionParticipantInterface
{

    /**
     * Prepares a two-phase commit (pre commit). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase commits.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPrepareCommit(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Prepares a two-phase rollback (pre rollback). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase rollbacks.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPrepareRollback(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Performs a two-phase commit (do the commit). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase commits.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be commited
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPerformCommit(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Performs a two-phase rollback (do the rollback). Do not call this directly. It is only used to let aspects implement
     * their logic of two phase rollbacks.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     * 
     * @throws \F3\BghObjects\Domain\TransactionConflictException thrown if the transaction could not be rolled back
     * because of conflicts, for example merge conflicts with previous commited transactions.
     */
    public function onPerformRollback(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Destroy this session. Do not call this directly. It is only used to let aspects implement their logic.
     * 
     * Note: Listeners must expect that this transaction does not have any children any more but may have a parent
     * transaction. Destroying transactions must always be valid and must never throw exceptions.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     */
    public function onDestroy(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Returns true if this is a local participant (not persisted); false if this participant is persistent.
     * 
     * Note: Local participants may join persistent transactions but they must always rejoin in the next request
     *       or more pragmatically they must rejoin within the __wakeup method of the transaction.
     */
    public function isLocal();
    
}
