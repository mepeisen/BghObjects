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
 * common base class for model implementations
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @entity
 * @scope prototype
 */
class Entity extends \F3\BghObjects\Lib\Model\SimpleEntity implements \F3\BghObjects\Lib\Model\EntityInterface
{
    
    /**
     * The uncommited bean holding changes
     * @var array
     * @transient
     */
    protected $_uncommitedBean = false;
    
    /**
     * true if this entity has autoCommit enabled
     * @var boolean
     * @transient
     */
    protected $_autoCommit = false;
    
    
    
    /**
     * @var \F3\FLOW3\Object\ObjectManager
     * @inject
     * @transient
     */
    protected $_objectManager;
    
    
    
    /**
     * @var string
     */
    protected $_repositoryClassName;
    
    /**
     * @var \F3\BghObjects\Lib\Repository\EntityRepositoryInterface
     */
    protected $_repository;
    
    
    
    /**
     * Constructor
     *
     * @param \F3\BghObjects\Lib\Repository\EntityRepositoryInterface $repos
     */
    public function __construct(\F3\BghObjects\Lib\Repository\EntityRepositoryInterface $repos)
    {
        $this->_repository = $repos;
        $this->_repositoryClassName = $repos->getObjectName();
        $this->_autoCommit = $repos->isAutoCommit();
    }
    
    /**
     * Returns the unique object id of this bean
     * 
     * @return string
     */
    public function getObjectId()
    {
        return $this->_entityId;
    }
    
    /**
     * Wakeup
     */
    public function __wakeup()
    {
        if (!is_object($this->_repository))
        {
            $this->_repository = $this->_objectManager->get($this->_repositoryClassName);
            $this->_autoCommit = $this->_repository->isAutoCommit();
        }
    }
    
    /**
     * Returns the property value
     * 
     * @param string $name
     * 
     * @return mixed
     */
    protected function getProperty($name)
    {
        if (!$this->isAutoCommit())
        {
            if (is_array($this->_uncommitedBean))
            {
                return isset($this->_uncommitedBean[$name]) ? $this->_uncommitedBean[$name] : null;
            }
        }
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
        if (!$this->isAutoCommit())
        {
            if (!is_array($this->_uncommitedBean))
            {
                $this->_uncommitedBean = array();
            }
            $this->_uncommitedBean[$name] = $value;
        }
        else
        {
            $name = "_$name";
            $this->$name = $value;
        }
    }
     
    /**
     * Commits the changes made to this object
     */
    public function commit()
    {
        if (is_array($this->_uncommitedBean))
        {
            foreach ($this->_uncommitedBean as $key => $val)
            {
                $name = "_$key";
                $this->$name = $val;
            }
            $this->_uncommitedBean = false;
        }
    }
    
    /**
     * Rolls back the changes made to this object
     */
    public function rollback()
    {
        $this->_uncommitedBean = false;
    }
    
    /**
     * Sets the auto commit flag
     *
     * @param boolean $flag true to automatically make changes to the object
     */
    public function setAutoCommit($flag)
    {
        $this->_autoCommit = $flag;
        if ($flag)
        {
            $this->commit();
        }
    }
    
    /**
     * Returns true if the auto commit is enabled
     *
     * @return boolean
     */
    public function isAutoCommit()
    {
        return $this->_autoCommit;
    }
    
    /**
     * Returns true if there are uncommited changes
     *
     * @return boolean
     */
    public function hasChanges()
    {
        return is_array($this->_uncommitedBean);
    }
    
}
