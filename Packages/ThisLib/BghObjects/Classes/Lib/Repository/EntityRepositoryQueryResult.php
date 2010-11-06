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
 * query result wrapper for entity repositories
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EntityRepositoryQueryResult implements \F3\FLOW3\Persistence\QueryResultInterface
{
    
    /**
     * The wrapped query object
     * @var \F3\FLOW3\Persistence\QueryInterface
     */
    protected $query;
    
    /**
     * The original query result object
     * @var \F3\FLOW3\Persistence\QueryResultInterface
     */
    protected $queryResult;
    
    /**
     * The object storage to register objects
     * @var \SplObjectStorage
     */
    protected $storage;
    
    /**
     * The object manager
     * @var \F3\FLO3\Object\ObjectManager
     * @inject
     */
    protected $objectManager;
    
    /**
     * Constructor
     * 
     * @param \F3\FLOW3\Persistence\QueryInterface $query
     * @param \F3\FLOW3\Persistence\QueryResultInterface $queryResult
     * @param \SplObjectStorage $storage
     */
    public function __construct(\F3\FLOW3\Persistence\QueryInterface $query, \F3\FLOW3\Persistence\QueryResultInterface $queryResult, \SplObjectStorage $storage)
    {
        $this->query = $query;
        $this->queryResult = $queryResult;
        $this->storage = $storage;
    }
    
    /**
	 * Returns a clone of the query object
	 *
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function getQuery()
	{
	    return $this->objectManager->create('F3\BghObjects\Lib\Repository\EntityRepositoryQuery', $this->queryResult->getQuery(), $this->storage);
	}

	/**
	 * Returns the first object in the result set
	 *
	 * @return object
	 * @api
	 */
	public function getFirst()
	{
	    $res = $this->queryResult->getFirst();
	    if (is_object($res)) $this->storage->attach($res);
	    return $res;
	}

	/**
	 * Returns an array with the objects in the result set
	 *
	 * @return array
	 * @api
	 */
	public function toArray()
	{
	    $res = $this->queryResult->toArray();
	    foreach ($res as $o) $this->storage->attach($o);
	    return $res;
	}
	
	/**
	 * Count elements of an object
	 * @link http://www.php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count()
	{
	    return $this->queryResult->count();
	}
	
    public function current()
    {
        $res = $this->queryResult->current();
	    if (is_object($res)) $this->storage->attach($res);
	    return $res;
    }

	public function next()
	{
	    $this->queryResult->next();
	}

	public function key()
	{
	    return $this->queryResult->key();
	}

	public function valid()
	{
	    return $this->queryResult->valid();
	}

	public function rewind()
	{
	    $this->queryResult->rewind();
	}
	
	/**
	 * @param offset
	 */
	public function offsetExists($offset)
	{
	    return $this->queryResult->offsetExists($offset);
	}

	/**
	 * @param offset
	 */
	public function offsetGet($offset)
	{
	    $res = $this->queryResult->offsetGet($offset);
	    if (is_object($res)) $this->storage->attach($res);
	    return $res;
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet($offset, $value)
	{
	    if (is_object($value)) $this->storage->attach($value);
	    $this->queryResult->offsetSet($offset, $value);
	}

	/**
	 * @param offset
	 */
	public function offsetUnset($offset)
	{
	    $this->queryResult->offsetUnset($offset);
	}
    
}
