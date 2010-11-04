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
 * query wrapper for entity repositories
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EntityRepositoryQuery implements \F3\FLOW3\Persistence\QueryInterface
{
    
    /**
     * The original query object
     * @var \F3\FLOW3\Persistence\QueryInterface
     */
    protected $query;
    
    /**
     * The object storage to register objects
     * @var \SplObjectStorage
     */
    protected $storage;
    
    /**
     * Constructor
     * 
     * @param \F3\FLOW3\Persistence\QueryInterface $query
     * @param \SplObjectStorage $storage
     */
    public function __construct(\F3\FLOW3\Persistence\QueryInterface $query, \SplObjectStorage $storage)
    {
        $this->query = $query;
        $this->storage = $storage;
    }
    
    /**
	 * Executes the query against the backend and returns the result
	 *
	 * @return \F3\FLOW3\Persistence\QueryResultInterface The query result
	 * @api
	 */
	public function execute()
	{
	    return new \F3\BghObjects\Lib\Repository\EntityRepositoryQueryResult($this, $this->query->execute(), $this->storage);
	}

	/**
	 * Executes the number of matching objects for the query
	 *
	 * @return integer The number of matching objects
	 * @api
	 */
	public function count()
	{
	    return $this->query->count();
	}

	/**
	 * Sets the property names to order the result by. Expected like this:
	 * array(
	 *  'foo' => \F3\FLOW3\Persistence\QueryInterface::ORDER_ASCENDING,
	 *  'bar' => \F3\FLOW3\Persistence\QueryInterface::ORDER_DESCENDING
	 * )
	 *
	 * @param array $orderings The property names to order by
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function setOrderings(array $orderings)
	{
	    $this->query->setOrderings($orderings);
	    return $this;
	}

	/**
	 * Sets the maximum size of the result set to limit. Returns $this to allow
	 * for chaining (fluid interface)
	 *
	 * @param integer $limit
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function setLimit($limit)
	{
	    $this->query->setLimit($limit);
	    return $this;
	}

	/**
	 * Sets the start offset of the result set to offset. Returns $this to
	 * allow for chaining (fluid interface)
	 *
	 * @param integer $offset
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function setOffset($offset)
	{
	    $this->query->setOffset($offset);
	    return $this;
	}

	/**
	 * The constraint used to limit the result set. Returns $this to allow
	 * for chaining (fluid interface)
	 *
	 * @param object $constraint Some constraint, depending on the backend
	 * @return \F3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function matching($constraint)
	{
	    $this->query->matching($constraint);
	    return $this;
	}

	/**
	 * Performs a logical conjunction of the two given constraints. The method
	 * takes one or more contraints and concatenates them with a boolean AND.
	 * It also accepts a single array of constraints to be concatenated.
	 *
	 * @param mixed $constraint1 The first of multiple constraints or an array of constraints.
	 * @return object
	 * @api
	 */
	public function logicalAnd($constraint1)
	{
	    return $this->query->logicalAnd($constraint1);
	}

	/**
	 * Performs a logical disjunction of the two given constraints. The method
	 * takes one or more contraints and concatenates them with a boolean OR.
	 * It also accepts a single array of constraints to be concatenated.
	 *
	 * @param mixed $constraint1 The first of multiple constraints or an array of constraints.
	 * @return object
	 * @api
	 */
	public function logicalOr($constraint1)
	{
	    return $this->query->logicalOr($constraint1);
	}

	/**
	 * Performs a logical negation of the given constraint
	 *
	 * @param object $constraint Constraint to negate
	 * @return object
	 * @api
	 */
	public function logicalNot($constraint)
	{
	    return $this->query->logicalNot($constraint1);
	}

	/**
	 * Returns an equals criterion used for matching objects against a query.
	 *
	 * It matches if the $operand equals the value of the property named
	 * $propertyName. If $operand is NULL a strict check for NULL is done. For
	 * strings the comparison can be done with or without case-sensitivity.
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @param boolean $caseSensitive Whether the equality test should be done case-sensitive for strings
	 * @return object
	 * @todo Decide what to do about equality on multi-valued properties
	 * @api
	 */
	public function equals($propertyName, $operand, $caseSensitive = TRUE)
	{
	    return $this->query->equals($propertyName, $operand, $caseSensitive);
	}

	/**
	 * Returns a like criterion used for matching objects against a query.
	 * Matches if the property named $propertyName is like the $operand, using
	 * standard SQL wildcards.
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param string $operand The value to compare with
	 * @param boolean $caseSensitive Whether the matching should be done case-sensitive
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a non-string property
	 * @api
	 */
	public function like($propertyName, $operand, $caseSensitive = TRUE)
	{
	    return $this->query->like($propertyName, $operand, $caseSensitive);
	}

	/**
	 * Returns a "contains" criterion used for matching objects against a query.
	 * It matches if the multivalued property contains the given operand.
	 *
	 * If NULL is given as $operand, there will never be a match!
	 *
	 * @param string $propertyName The name of the multivalued property to compare against
	 * @param mixed $operand The value to compare with
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a single-valued property
	 * @api
	 */
	public function contains($propertyName, $operand)
	{
	    return $this->query->contains($propertyName, $operand);
	}

	/**
	 * Returns an "isEmpty" criterion used for matching objects against a query.
	 * It matches if the multivalued property contains no values or is NULL.
	 *
	 * @param string $propertyName The name of the multivalued property to compare against
	 * @return boolean
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a single-valued property
	 * @api
	 */
	public function isEmpty($propertyName)
	{
	    return $this->query->isEmpty($propertyName);
	}

	/**
	 * Returns an "in" criterion used for matching objects against a query. It
	 * matches if the property's value is contained in the multivalued operand.
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with, multivalued
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a multi-valued property
	 * @api
	 */
	public function in($propertyName, $operand)
	{
	    return $this->query->in($propertyName, $operand);
	}

	/**
	 * Returns a less than criterion used for matching objects against a query
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
	 * @api
	 */
	public function lessThan($propertyName, $operand)
	{
	    return $this->query->lessThan($propertyName, $operand);
	}

	/**
	 * Returns a less or equal than criterion used for matching objects against a query
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
	 * @api
	 */
	public function lessThanOrEqual($propertyName, $operand)
	{
	    return $this->query->lessThanOrEqual($propertyName, $operand);
	}

	/**
	 * Returns a greater than criterion used for matching objects against a query
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
	 * @api
	 */
	public function greaterThan($propertyName, $operand)
	{
	    return $this->query->greaterThan($propertyName, $operand);
	}

	/**
	 * Returns a greater than or equal criterion used for matching objects against a query
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @return object
	 * @throws \F3\FLOW3\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
	 * @api
	 */
	public function greaterThanOrEqual($propertyName, $operand)
	{
	    return $this->query->greaterThanOrEqual($propertyName, $operand);
	}
    
}
