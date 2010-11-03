<?php
declare(ENCODING = 'utf-8');
namespace F3\BghObjects\Tests\Unit\Domain\Repository;

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
 * Testcase for the transaction repository
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class TransactionRepositoryTest extends \F3\Testing\BaseTestCase
{

    /**
     * @test
     */
	public function findAnonymous()
	{
	    $expectedResult = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
		
	    $mockQuery = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
		$mockQuery->expects($this->once())->method('execute')->with()->will($this->returnValue($expectedResult));
		$mockQuery->expects($this->exactly(2))->method('equals')->will($this->returnValue('Null'));
		$mockQuery->expects($this->once())->method('logicalOr')->with(array('Null', 'Null'))->will($this->returnValue('matches'));
		$mockQuery->expects($this->once())->method('matching')->with('matches');
		
		$repository = $this->getAccessibleMock('F3\BghObjects\Domain\Repository\TransactionRepository', array('createQuery'));
		$repository->expects($this->once())->method('createQuery')->will($this->returnValue($mockQuery));

		$this->assertSame($expectedResult, $repository->findAllAnonymous());
	}
    /**
     * @test
     */
	public function findByServiceNameAndName()
	{
	    $expectedResult = $this->getMock('F3\FLOW3\Persistence\QueryResultInterface');
		
	    $mockQuery = $this->getMock('F3\FLOW3\Persistence\QueryInterface');
		$mockQuery->expects($this->once())->method('execute')->with()->will($this->returnValue($expectedResult));
		$mockQuery->expects($this->exactly(2))->method('equals')->will($this->returnValue('Null'));
		$mockQuery->expects($this->once())->method('logicalAnd')->with(array('Null', 'Null'))->will($this->returnValue('matches'));
		$mockQuery->expects($this->once())->method('matching')->with('matches');
		
		$repository = $this->getAccessibleMock('F3\BghObjects\Domain\Repository\TransactionRepository', array('createQuery'));
		$repository->expects($this->once())->method('createQuery')->will($this->returnValue($mockQuery));

		$this->assertSame($expectedResult, $repository->findByServiceNameAndName('foo', 'bar'));
	}
	
}
