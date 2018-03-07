<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.2
 */
namespace Klapuch\Dataset\Unit;

use Klapuch\Dataset;
use Klapuch\Sql;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SelectiveClause extends Tester\TestCase {
	public function testOrderOfStatements() {
		$clause = new Dataset\SelectiveClause(
			new Sql\AnsiWhere(new Sql\FakeClause(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(
				[
					'filter' => ['name' => 'Dom'],
					'sort' => ['age' => 'ASC'],
					'paging' => ['limit' => 10, 'offset' => 4],
				]
			)
		);
		Assert::same(
			' WHERE foo = :bar AND name = :name ORDER BY age ASC LIMIT 10 OFFSET 4',
			$clause->sql()
		);
		Assert::same(['bar' => 10, 'name' => 'Dom'], $clause->parameters()->binds());
	}

	public function testFilterRelatedStatement() {
		$clause = new Dataset\SelectiveClause(
			new Sql\AnsiWhere(new Sql\FakeClause(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['filter' => ['name' => 'Dom', 'age' => 20]])
		);
		Assert::same(' WHERE foo = :bar AND name = :name AND age = :age', $clause->sql());
		Assert::same(['bar' => 10, 'name' => 'Dom', 'age' => 20], $clause->parameters()->binds());
	}

	public function testSortRelatedStatement() {
		$clause = new Dataset\SelectiveClause(
			new Sql\AnsiWhere(new Sql\FakeClause(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['sort' => ['name' => 'ASC', 'age' => 'DESC']])
		);
		Assert::same(' WHERE foo = :bar ORDER BY name ASC, age DESC', $clause->sql());
		Assert::same(['bar' => 10], $clause->parameters()->binds());
	}

	public function testPagingRelatedStatement() {
		$clause = new Dataset\SelectiveClause(
			new Sql\AnsiWhere(new Sql\FakeClause(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['paging' => ['limit' => 20, 'offset' => 5]])
		);
		Assert::same(' WHERE foo = :bar LIMIT 20 OFFSET 5', $clause->sql());
		Assert::same(['bar' => 10], $clause->parameters()->binds());
	}
}


(new SelectiveClause())->run();