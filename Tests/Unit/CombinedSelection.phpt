<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Dataset\Unit;

use Klapuch\Dataset;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class CombinedSelection extends Tester\TestCase {
	public function testCombiningExpressionInPassedOrder() {
		Assert::same(
			'SELECT * FROM world WHERE name = :name ORDER BY name DESC',
			(new Dataset\CombinedSelection(
				new Dataset\FakeSelection(' WHERE name = :name'),
				new Dataset\FakeSelection(' ORDER BY name DESC')
			))->expression('SELECT * FROM world')
		);
	}

	public function testCombibningCriteriaInPassedOrder() {
		Assert::same(
			[1, 2, 3],
			(new Dataset\CombinedSelection(
				new Dataset\FakeSelection(null, [2]),
				new Dataset\FakeSelection(null, [3])
			))->criteria([1])
		);
	}
}


(new CombinedSelection())->run();