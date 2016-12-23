<?php
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Dataset\Unit;

use Tester;
use Tester\Assert;
use Klapuch\Dataset;

require __DIR__ . '/../bootstrap.php';

final class SqlSort extends Tester\TestCase {
	public function testEmptySourceCriteria() {
		Assert::same(
			[],
			(new Dataset\SqlSort(['name' => 'desc']))->criteria([])
		);
	}

	public function testEmptySortCriteria() {
		Assert::same(
			['name' => 'desc'],
			(new Dataset\SqlSort([]))->criteria(['name' => 'desc'])
		);
	}

	public function testEmptyCriteria() {
		Assert::same(
			[],
			(new Dataset\SqlSort([]))->criteria([])
		);
	}

	public function testAddedOrderClause() {
		$source = 'SELECT * FROM world';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testMultipleAddedOrderClauses() {
		$source = 'SELECT * FROM world';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC, number ASC',
			(new Dataset\SqlSort(
				['name' => 'DESC', 'number' => 'ASC']
			))->expression($source)
		);
	}

	public function testReplacingSingleOrderClauseByPassedSort() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testAlreadyStatedOrderClauseWithInvertedDirection() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY name ASC',
			(new Dataset\SqlSort(
				['name' => 'ASC']
			))->expression($source)
		);
	}

	public function testSameSortAsOrderClause() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC',
			(new Dataset\SqlSort(
				['name' => 'DESC']
			))->expression($source)
		);
	}

	public function testReplacingMultipleOrderClausesByPassedSort() {
		$source = 'SELECT * FROM world ORDER BY name DESC, skill ASC';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testAlreadyStatedLowerCaseOrderClause() {
		$source = 'SELECT * FROM world order by name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testWeirdFormattedOrderClause() {
		$source = 'SELECT * FROM world
								order
					by
							name DESC
			  ';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testReplacingOrderClauseWithoutAffectingLimit() {
		$source = 'SELECT * FROM world ORDER BY number DESC LimiT 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC LimiT 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testReplacingOrderClauseWithoutAffectingOffset() {
		$source = 'SELECT * FROM world ORDER BY number DESC OffseT 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC OffseT 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	/* TODO: Desired behavior
	public function testPuttingOrderClauseBeforeLimit() {
		$source = 'SELECT * FROM world LIMIT 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC LIMIT 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testPuttingOrderClauseBeforeOffset() {
		$source = 'SELECT * FROM world OFFSET 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC OFFSET 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testPuttingOrderClauseBeforeLimitAndOffset() {
		$source = 'SELECT * FROM world LIMIT 5 OFFSET 10';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC LIMIT 5 OFFSET 10',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testPuttingOrderClauseBeforeOffsetAndLimit() {
		$source = 'SELECT * FROM world OFFSET 10 LIMIT 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC OFFSET 10 LIMIT 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testPuttingOrderClauseBeforeExplicitLimitAndOffset() {
		$source = 'SELECT * FROM world LIMIT 5, 10';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC LIMIT 5, 10',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}
	*/
}


(new SqlSort())->run();