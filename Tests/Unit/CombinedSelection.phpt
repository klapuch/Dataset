<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.2
 */
namespace Klapuch\Dataset\Unit;

use Klapuch\Dataset;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class CombinedSelection extends Tester\TestCase {
	public function testCombiningCriteriaInPassedOrder() {
		Assert::same(
			[1, 2, 3],
			(new Dataset\CombinedSelection(
				new Dataset\FakeSelection([1]),
				new Dataset\FakeSelection([2]),
				new Dataset\FakeSelection([3])
			))->criteria()
		);
	}

	public function testCombiningNested() {
		Assert::same(
			['filter' => [1, 3], 'sort' => [2]],
			(new Dataset\CombinedSelection(
				new Dataset\FakeSelection(['filter' => [1]]),
				new Dataset\FakeSelection(['sort' => [2]]),
				new Dataset\FakeSelection(['filter' => [3]])
			))->criteria()
		);
	}
}


(new CombinedSelection())->run();