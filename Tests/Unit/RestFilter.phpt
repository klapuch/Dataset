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

final class RestFilter extends Tester\TestCase {
	public function testNoGivenFilter() {
		Assert::same(['filter' => []], (new Dataset\RestFilter([], []))->criteria());
	}

	public function testAppliedFilter() {
		Assert::same(
			['filter' => ['name' => 'bar']],
			(new Dataset\RestFilter(['name' => 'bar'], ['name']))->criteria()
		);
	}

	public function testTakingOnlyAllowed() {
		Assert::same(
			['filter' => ['name' => 'bar']],
			(new Dataset\RestFilter(['name' => 'bar', 'foo' => 'no'], ['name']))->criteria()
		);
	}

	public function testNoMatchingAsEmpty() {
		Assert::same(['filter' => []], (new Dataset\RestFilter(['foo' => 'no'], ['name']))->criteria());
	}

	public function testNoAllowedCriteriaAsEmptyArray() {
		Assert::same(['filter' => []], (new Dataset\RestFilter(['foo' => 'no'], []))->criteria());
	}
}


(new RestFilter())->run();