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

final class RestFilter extends Tester\TestCase {
	public function testNoGivenFilter() {
		Assert::same(['filter' => []], (new Dataset\RestFilter([], []))->criteria());
	}

	public function testAppliedFilter() {
		Assert::same(
			['filter' => ['name' => 'bar']],
			(new Dataset\RestFilter(['name' => 'bar']))->criteria()
		);
	}

	public function testSkippingIgnored() {
		Assert::same(
			['filter' => ['name' => 'bar']],
			(new Dataset\RestFilter(['name' => 'bar', 'title' => 'baz'], ['title']))->criteria()
		);
	}

	public function testSkippingAllLeadingToEmptyFilter() {
		Assert::same(['filter' => []], (new Dataset\RestFilter(['name' => 'bar'], ['name']))->criteria());
	}

	public function testIgnoringUnknownKeys() {
		Assert::same(
			['filter' => ['name' => 'bar']],
			(new Dataset\RestFilter(['name' => 'bar'], ['foo']))->criteria()
		);
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "name"
	 */
	public function testThrowingOnForbiddenCriteria() {
		(new Dataset\RestFilter(['name' => 'bar'], [], ['name']))->criteria();
	}

	public function testSkippingBeforeForbidden() {
		Assert::noError(static function() {
			(new Dataset\RestFilter(['name' => 'bar'], ['name'], ['name']))->criteria();
		});
	}
}


(new RestFilter())->run();
