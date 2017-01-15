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
		Assert::same([], (new Dataset\SqlSort([]))->criteria([]));
	}

	public function testAddingOrderClause() {
		$source = 'SELECT * FROM world';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testAddingMultipleOrderClauses() {
		$source = 'SELECT * FROM world';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC, number ASC',
			(new Dataset\SqlSort(
				['name' => 'DESC', 'number' => 'ASC']
			))->expression($source)
		);
	}

	public function testReplacingSingleOrderClauseByPassedOne() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testReplacingWithInvertedOne() {
		$source = 'SELECT * FROM world ORDER BY name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY name ASC',
			(new Dataset\SqlSort(
				['name' => 'ASC']
			))->expression($source)
		);
	}

	public function testNoDefectWithSamePassedAsStated() {
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

	public function testReplacingStatedLowerCaseOrderClause() {
		$source = 'SELECT * FROM world order by name DESC';
		Assert::same(
			'SELECT * FROM world ORDER BY number ASC',
			(new Dataset\SqlSort(
				['number' => 'ASC']
			))->expression($source)
		);
	}

	public function testReformatting() {
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
		$source = 'SELECT * FROM world ORDER BY number DESC limit 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC limit 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testReplacingOrderClauseWithoutAffectingOffset() {
		$source = 'SELECT * FROM world ORDER BY number DESC offset 5';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC offset 5',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testReplacingOrderClauseWithoutAffectingLimitAndOffset() {
		$source = 'SELECT * FROM world ORDER BY number DESC LIMIT 5 OFFSET 10';
		Assert::same(
			'SELECT * FROM world ORDER BY name DESC LIMIT 5 OFFSET 10',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testReplacingOrderClauseBetweenOthers() {
		$source = 'SELECT * FROM world WHERE x = y ORDER BY number DESC LIMIT 5 OFFSET 10';
		Assert::same(
			'SELECT * FROM world WHERE x = y ORDER BY name DESC LIMIT 5 OFFSET 10',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

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

	public function testApplyingToOuterQueryWithoutInnerMatch() {
		$source = 'SELECT * FROM world WHERE name = (SELECT * FROM world WHERE number = 1)';
		Assert::same(
			'SELECT * FROM world WHERE name = (SELECT * FROM world WHERE number = 1) ORDER BY name DESC',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	/** TODO: Desired behavior
	public function testApplyingToOuterQueryWithInnerLimitMatch() {
		$source = 'SELECT * FROM world WHERE name = (SELECT * FROM world WHERE number = 1 LIMIT 4)';
		Assert::same(
			'SELECT * FROM world WHERE name = (SELECT * FROM world WHERE number = 1 LIMIT 4) ORDER BY name DESC',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}

	public function testApplyingToOuterQueryWithInnerMatch() {
		$source = 'SELECT * FROM world WHERE name = (SELECT * FROM world ORDER BY foo)';
		Assert::same(
			'SELECT * FROM world WHERE name = (SELECT * FROM world ORDER BY foo) ORDER BY name DESC',
			(new Dataset\SqlSort(['name' => 'DESC']))->expression($source)
		);
	}**/
}


(new SqlSort())->run();