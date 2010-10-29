<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Service;

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
 * The transaction service to handle transactions.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface TransactionServiceInterface
{
    
    /**
     * Returns the current/active transaction
     * 
     * @return \F3\BghObjects\Domain\Model\TransactionInterface active transaction or null if there is no active
     * transaction
     */
    public function get();
    
    /**
     * Resumes the given transaction; the previous transaction will be placed on a stack and will be resumed as soon
     * as this transaction will be commited, resumed or paused.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     */
    public function resume(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
    /**
     * Pauses the current transaction and falls back to the previous one being active right before the current
     * transaction was created/resumed.
     */
    public function pause();
    
    /**
     * Starts a new top most transaction. The new transaction will be replacing the current transaction (see method
     * resume for details) automatically.
     * 
     * @param string  $serviceName Name of the service class/ originator
     * @param string  $name        Name of the transaction or null for anonymous transactions
     * @param boolean $isLocal     true to create a local transaction; false to create a persistent transaction
     * 
     * @return \F3\BghObjects\Domain\Model\TransactionInterface
     */
    public function startTx($serviceName, $name, $isLocal);
    
    /**
     * Destroys an existing transaction discarding every change; this method will always succeed and never
     * throw exceptions. It should be used if a transaction could not be roll backed due to failures.
     * 
     * @param \F3\BghObjects\Domain\Model\TransactionInterface $tx
     */
    public function destroy(\F3\BghObjects\Domain\Model\TransactionInterface $tx);
    
}
