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

	public function testStatedOrderClauseWithPrecedence() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC, number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testAlreadyStatedLowerCaseOrderClause() {
		$source = 'SELECT * FROM world order by name DESC';
		Assert::same(
			'SELECT * FROM world order by name DESC, number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}
}


(new SqlSort())->run();