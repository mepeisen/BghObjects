<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Lib\Repository;

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
 * Interface for transactional entity repositories
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface EntityRepositoryInterface extends \F3\BghObjects\Lib\Repository\SimpleEntityRepositoryInterface
{
    
    /**
     * Commits the changes made to any object of this repository
     */
    public function commit();
    
    /**
     * Rolls back the changes made to any object of this repository
     */
    public function rollback();
    
    /**
     * Sets the auto commit flag
     *
     * @param boolean $flag true to automatically make changes to the object
     */
    public function setAutoCommit($flag);
    
    /**
     * Returns true if the auto commit is enabled
     *
     * @return boolean
     */
    public function isAutoCommit();
    
    /**
     * Returns true if there are uncommited changes
     *
     * @return boolean
     */
    public function hasChanges();
    
    /**
     * Returns the object name of this repository
     * @return string
     */
    public function getObjectName();
    
}
