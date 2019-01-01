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

final class SelectiveStatement extends Tester\TestCase {
	public function testOrderOfStatements() {
		$statement = new Dataset\SelectiveStatement(
			new Sql\AnsiWhere(new Sql\FakeStatement(), 'foo = :bar', ['bar' => 10]),
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
			$statement->sql()
		);
		Assert::same(['bar' => 10, 'name' => 'Dom'], $statement->parameters()->binds());
	}

	public function testFilterRelatedStatement() {
		$statement = new Dataset\SelectiveStatement(
			new Sql\AnsiWhere(new Sql\FakeStatement(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['filter' => ['name' => 'Dom', 'age' => 20]])
		);
		Assert::same(' WHERE foo = :bar AND name = :name AND age = :age', $statement->sql());
		Assert::same(['bar' => 10, 'name' => 'Dom', 'age' => 20], $statement->parameters()->binds());
	}

	public function testSortRelatedStatement() {
		$statement = new Dataset\SelectiveStatement(
			new Sql\AnsiWhere(new Sql\FakeStatement(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['sort' => ['name' => 'ASC', 'age' => 'DESC']])
		);
		Assert::same(' WHERE foo = :bar ORDER BY name ASC, age DESC', $statement->sql());
		Assert::same(['bar' => 10], $statement->parameters()->binds());
	}

	public function testPagingRelatedStatement() {
		$statement = new Dataset\SelectiveStatement(
			new Sql\AnsiWhere(new Sql\FakeStatement(), 'foo = :bar', ['bar' => 10]),
			new Dataset\FakeSelection(['paging' => ['limit' => 20, 'offset' => 5]])
		);
		Assert::same(' WHERE foo = :bar LIMIT 20 OFFSET 5', $statement->sql());
		Assert::same(['bar' => 10], $statement->parameters()->binds());
	}
}


(new SelectiveStatement())->run();
