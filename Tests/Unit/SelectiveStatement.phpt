<?php
declare(strict_types = 1);

/**
 * @testCase
 * @phpVersion > 7.2
 */

namespace Klapuch\Dataset\Unit;

use Characterice\Sql\Expression;
use Characterice\Sql\Statement\Select;
use Klapuch\Dataset;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SelectiveStatement extends Tester\TestCase {
	public function testApplying() {
		$statement = new Dataset\SelectiveStatement(
			(new Select\Query())->select(new Expression\Select(['firstname']))->from(new Expression\From(['world'])),
			new Dataset\FakeSelection(
				[
					'filter' => ['name' => 'Dom'],
					'sort' => ['age' => 'ASC'],
					'paging' => ['limit' => 10, 'offset' => 4],
				]
			)
		);
		Assert::same(
			'SELECT firstname FROM world WHERE name = :name ORDER BY age ASC LIMIT 10 OFFSET 4',
			$statement->sql()
		);
		Assert::same(['name' => 'Dom'], $statement->parameters());
	}

	public function testArrayValueToInClause() {
		$statement = new Dataset\SelectiveStatement(
			(new Select\Query())->select(new Expression\Select(['firstname']))->from(new Expression\From(['world'])),
			new Dataset\FakeSelection(['filter' => ['name' => 'Dom', 'age' => [10, 20]]])
		);
		Assert::same('SELECT firstname FROM world WHERE name = :name AND age IN (:age__1, :age__2)', $statement->sql());
		Assert::same(['name' => 'Dom', ':age__1' => 10, ':age__2' => 20], $statement->parameters()->binds());
	}
}


(new SelectiveStatement())->run();
