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

final class SqlFilter extends Tester\TestCase {
	public function testMergingCriteria() {
		Assert::same(
			['skill' => 'developer', 'name' => 'Dom', 'number' => '666'],
			(new Dataset\SqlFilter(
				['name' => 'Dom', 'number' => '666']
			))->criteria(['skill' => 'developer'])
		);
	}

	public function testMergingCriteriaWithSourcePrecedence() {
		Assert::same(
			['name' => 'Dom', 'number' => '666'],
			(new Dataset\SqlFilter(
				['name' => 'foo', 'number' => '666']
			))->criteria(['name' => 'Dom'])
		);
	}

	public function testAddingWhereClause() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name',
			(new Dataset\SqlFilter(
				[':name' => 'foo']
			))->expression('SELECT * FROM world')
		);
	}

	public function testReformatting() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name AND skill = :skill ORDER BY foo',
			(new Dataset\SqlFilter(
				[':skill' => 'foo']
			))->expression(
				'SELECT *
		FROM world
					WHERE name
	 = 
			:name            
        ORDER                
		BY
                            foo'
			)
		);
	}

	public function testAddingMultipleConditions() {
		Assert::same(
			'SELECT * FROM world WHERE (name = :name AND number = :number)',
			(new Dataset\SqlFilter(
				[':name' => 'foo', ':number' => 'foo']
			))->expression('SELECT * FROM world')
		);
	}

	public function testAddingConditionToExistingWhereClause() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name AND number = :number',
			(new Dataset\SqlFilter(
				[':number' => 'foo']
			))->expression('SELECT * FROM world WHERE name = :name')
		);
	}

	public function testAddingConditionToMultipleExistingWhereClauses() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name OR number = :number AND skill = :skill',
			(new Dataset\SqlFilter(
				[':skill' => 'foo']
			))->expression(
				'SELECT * FROM world WHERE name = :name OR number = :number'
			)
		);
	}

	public function testAddingMultipleConditionsToMultipleExistingWhereClauses() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name OR number = :number AND (skill = :skill AND mood = :mood)',
			(new Dataset\SqlFilter(
				[':skill' => 'foo', ':mood' => 'foo']
			))->expression(
				'SELECT * FROM world WHERE name = :name OR number = :number'
			)
		);
	}

	public function testPuttingCondition() {
		Assert::same(
			'SELECT * FROM world WHERE number = :number GROUP BY name',
			(new Dataset\SqlFilter(
				[':number' => 'foo']
			))->expression('SELECT * FROM world GROUP BY name')
		);
	}

	public function testPuttingConditionToExistingWhereClause() {
		Assert::same(
			'SELECT * FROM world WHERE id = :id AND number = :number GROUP BY name',
			(new Dataset\SqlFilter(
				[':number' => 'foo']
			))->expression('SELECT * FROM world WHERE id = :id GROUP BY name')
		);
	}

	public function testPuttingMultipleConditionsToExistingWhereClause() {
		Assert::same(
			'SELECT * FROM world WHERE id = :id AND (number = :number AND name = :name) GROUP BY name',
			(new Dataset\SqlFilter(
				[':number' => 'foo', ':name' => 'foo']
			))->expression('SELECT * FROM world WHERE id = :id GROUP BY name')
		);
	}

	public function testPuttingConditionToMultipleEndClauses() {
		Assert::same(
			'SELECT * FROM w_0rld WHERE id = :id AND number = :number GROUP BY name HAVING name > 1 ORDER BY name OFFSET 5 LIMIT 8',
			(new Dataset\SqlFilter(
				[':number' => 'foo']
			))->expression(
				'SELECT * FROM w_0rld
				WHERE id = :id
				GROUP BY name
				HAVING name > 1
				ORDER BY name
				OFFSET 5
				LIMIT 8'
			)
		);
	}

	public function testMultibyteQuery() {
		Assert::same(
			'SELECT koňíček FROM staj WHERE koňíček = :koňíček ORDER BY koňíček',
			(new Dataset\SqlFilter(
				[':koňíček' => 'foo']
			))->expression(
				'SELECT koňíček FROM staj ORDER BY koňíček'
			)
		);
	}

	/** TODO: Desired behavior
	public function testApplyingToOuterQueryWithoutInnerMatch() {
		Assert::same(
			'SELECT * FROM (SELECT * FROM world) WHERE name = :name',
			(new Dataset\SqlFilter(
				[':name' => 'foo']
			))->expression('SELECT * FROM (SELECT * FROM world)')
		);
	}

	public function testApplyingToOuterQueryWithInnerMatch() {
		Assert::same(
			'SELECT * FROM (SELECT * FROM world WHERE number = :number) WHERE name = :name',
			(new Dataset\SqlFilter(
				[':name' => 'foo']
			))->expression('SELECT * FROM (SELECT * FROM world WHERE number = :number)')
		);
	}*/
}


(new SqlFilter())->run();