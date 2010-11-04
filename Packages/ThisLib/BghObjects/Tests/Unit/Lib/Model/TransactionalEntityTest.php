<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Lib\Model;

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
 * Testcase for a transactional entity
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class TransactionalEntityTest extends \F3\Testing\BaseTestCase
{

	/**
	 * @test
	 */
	public function getObjectId()
	{
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntity', array('dummy'));
	    $entity->_set('FLOW3_Persistence_Entity_UUID', 'myid');
	    self::assertEquals('myid', $entity->getObjectId());
	}
	
	/**
	 * @test
	 */
	public function getProperty()
	{
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntity', array('dummy'));
	    $entity->_set('_foo', 'bar');
	    self::assertEquals('bar', $entity->_call('getProperty', 'foo'));
	}
	
	/**
	 * @test
	 */
	public function setPropertyChangesProperty()
	{
	    $entity = $this->getAccessibleMock('F3\BghObjects\Lib\Model\TransactionalEntity', array('dummy'));
	    $entity->_set('_foo', 'bar');
	    $entity->_call('setProperty', 'foo', 'bar2');
	    self::assertEquals('bar2', $entity->_call('getProperty', 'foo'));
	}
	
}
