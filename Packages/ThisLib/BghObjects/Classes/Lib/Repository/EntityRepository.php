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
 * base class for simple non-transactional entity repositories
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class EntityRepository extends \F3\BghObjects\Lib\Repository\SimpleEntityRepository implements \F3\BghObjects\Lib\Repository\EntityRepositoryInterface
{
    
    /**
     * The object map
     * @var \SplObjectStorage
     */
    protected $objectMap;
    
    /**
     * Auto commit flag
     * @var boolean
     */
    protected $autoCommit = false;
    
    /**
     * object manager
     * @var \F3\FLOW3\Object\ObjectManager
     * @inject
     */
    protected $objectManager;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objectMap = new \SplObjectStorage();
    }
    
    /**
     * Commits the changes made to any object of this repository
     */
    public function commit()
    {
        $this->objectMap->rewind();
        while ($this->objectMap->valid())
        {
            $val = $this->objectMap->key();
            $this->objectMap->next();
            $val->commit();
        }
    }
    
    /**
     * Rolls back the changes made to any object of this repository
     */
    public function rollback()
    {
        $this->objectMap->rewind();
        while ($this->objectMap->valid())
        {
            $val = $this->objectMap->key();
            $this->objectMap->next();
            $val->rollback();
        }
    }
    
    /**
     * Sets the auto commit flag
     *
     * @param boolean $flag true to automatically make changes to the object
     */
    public function setAutoCommit($flag)
    {
        $this->autoCommit = $flag;
        $this->objectMap->rewind();
        while ($this->objectMap->valid())
        {
            $val = $this->objectMap->key();
            $this->objectMap->next();
            $val->setAutoCommit($flag);
        }
    }
    
    /**
     * Returns true if the auto commit is enabled
     *
     * @return boolean
     */
    public function isAutoCommit()
    {
        return $this->autoCommit;
    }
    
    /**
     * Returns true if there are uncommited changes
     *
     * @return boolean
     */
    public function hasChanges()
    {
        $this->objectMap->rewind();
        while ($this->objectMap->valid())
        {
            $val = $this->objectMap->key();
            $this->objectMap->next();
            if ($val->hasChanges()) return true;
        }
        return false;
    }
    
    /**
	 * Adds an object to this repository
	 *
	 * @param object $object The object to add
	 * @return void
	 */
	public function add($object)
	{
	    parent::add($object);
	    $this->objectMap->attach($object);
	}

	/**
	 * Removes an object from this repository. If it is contained in $this->addedObjects
	 * we just remove it there, since this means it has never been persisted yet.
	 *
	 * Else we keep the object around to check if we need to remove it from the
	 * storage layer.
	 *
	 * @param object $object The object to remove
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 * @api
	 */
	public function remove($object)
	{
	    parent::remove($object);
	    $this->objectMap->detach($object);
	}

	/**
	 * Replaces an object by another after checking that existing and new
	 * objects have the right types
	 *
	 * @param object $existingObject The existing object
	 * @param object $newObject The new object
	 * @author Robert Lemke <robert@typo3.org>
	 * @api
	 */
	public function replace($existingObject, $newObject)
	{
	    parent::replace($existingObject, $newObject);
	    $this->objectMap->detach($existingObject);
	    $this->objectMap->attach($newObject);
	}

	/**
	 * Returns a query for objects of this repository
	 *
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @author Robert Lemke <robert@typo3.org>
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 * @api
	 */
	public function createQuery()
	{
	    return $this->objectManager->create('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', parent::createQuery(), $this->objectMap);
	}
	
	/**
     * Returns the object name of this repository
     * @return string
     */
    public function getObjectName()
    {
        throw new \F3\FLOW3\Exception('Method must be implemented by child classes');
    }
    
}
