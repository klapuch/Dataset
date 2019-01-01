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

final class RestPaging extends Tester\TestCase {
	public function testAppliedPaging() {
		Assert::same(
			['paging' => ['limit' => 30, 'offset' => 0]],
			(new Dataset\RestPaging(1, 30))->criteria()
		);
		Assert::same(
			['paging' => ['limit' => 30, 'offset' => 30]],
			(new Dataset\RestPaging(2, 30))->criteria()
		);
	}

	public function testLimitedLimit() {
		Assert::same(
			['paging' => ['limit' => 30, 'offset' => 0]],
			(new Dataset\RestPaging(1, 300, 30))->criteria()
		);
	}

	public function testArbitraryOffset() {
		Assert::same(
			['paging' => ['limit' => 10, 'offset' => 2990]],
			(new Dataset\RestPaging(300, 10, 30))->criteria()
		);
	}

	public function testUnrealPerPageWithDefaultFallback() {
		Assert::same(
			['paging' => ['limit' => 30, 'offset' => 0]],
			(new Dataset\RestPaging(1, 0, 30))->criteria()
		);
	}
}


(new RestPaging())->run();
