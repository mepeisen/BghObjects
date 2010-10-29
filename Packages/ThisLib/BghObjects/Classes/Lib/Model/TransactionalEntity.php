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
 * common base class for model entites (fully transactional)
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @entity
 * @scope prototype
 */
class TransactionalEntity extends \F3\BghObjects\Lib\Model\SimpleEntity implements \F3\BghObjects\Lib\Model\TransactionalEntityInterface
{
    
    /**
     * The persistent transaction data
     * @var \ArrayObject
     * @todo should be introduced by TransactionalEntityAspect as soon as FLOW3 supports introducing variables;
     *       currently we need to store this property in this class so that the persistence sees it and persists
     *       it.
     */
    protected $_BghObjects_transactionData_persistent;
    
    /**
     * The local transaction data
     * @var \ArrayObject
     * @transient
     * @todo should be introduced by TransactionalEntityAspect as soon as FLOW3 supports introducing variables;
     *       currently we need to store this property in this class so that the persistence sees it and persists
     *       it.
     */
    protected $_BghObjects_transactionData_local;
    
	/**
     * Returns the property value
     * 
     * @param string $name
     * 
     * @return mixed
     */
    protected function getProperty($name)
    {
        // note: this method will be overwritten by aspects
        $name = "_$name";
        return $this->$name;
    }
    
    /**
     * Sets the property value
     * 
     * @param string $name
     * @param mixed $value
     */
    protected function setProperty($name, $value)
    {
        // note: this method will be overwritten by aspects
        $name = "_$name";
        $this->$name = $value;
    }
    
}
