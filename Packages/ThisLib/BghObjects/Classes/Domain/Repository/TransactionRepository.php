<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Domain\Repository;

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
 * Interface for transaction repositories.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope singleton
 */
class TransactionRepository extends \F3\FLOW3\Persistence\Repository implements \F3\BghObjects\Domain\Repository\TransactionRepositoryInterface
{
    
    /**
     * The object type
     * @var string
     */
    protected $objectType = 'F3\BghObjects\Domain\Model\PersistentTransaction';
    
    /**
     * Finds a single transaction by service name and transaction name
     * 
     * @param string $serviceName
     * @param string $name
     * 
     * @return \F3\FLOW3\Persistence\QueryResultInterface the query result
     */
    public function findByServiceNameAndName($serviceName, $name)
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(array($query->equals('serviceName', $serviceName), $query->equals('name', $name))));
        return $query->execute();
    }
    
    /**
     * Finds all anonymous transactions
     * 
     * @return \F3\FLOW3\Persistence\QueryResultInterface the query result
     */
    public function findAllAnonymous()
    {
        $query = $this->createQuery();
        $query->matching($query->logicalOr(array($query->equals('serviceName', null), $query->equals('name', null))));
        return $query->execute();
    }
    
}
