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

final class SqlRestFilter extends Tester\TestCase {
	public function testNoGivenFilter() {
		Assert::same('SELECT * FROM world', (new Dataset\SqlRestFilter([], []))->expression('SELECT * FROM world'));
		Assert::same([], (new Dataset\SqlRestFilter([], []))->criteria([]));
	}

	public function testAppliedFilter() {
		$filter = new Dataset\SqlRestFilter(['name' => 'bar'], ['name']);
		Assert::same(
			'SELECT * FROM world WHERE name = :name',
			$filter->expression('SELECT * FROM world')
		);
		Assert::same(['name' => 'bar'], $filter->criteria([]));
	}

	public function testTakingOnlyAllowed() {
		$filter = new Dataset\SqlRestFilter(['name' => 'bar', 'foo' => 'no'], ['name']);
		Assert::same(
			'SELECT * FROM world WHERE name = :name',
			$filter->expression('SELECT * FROM world')
		);
		Assert::same(['name' => 'bar'], $filter->criteria([]));
	}

	public function testNoMatchingAsEmpty() {
		$filter = new Dataset\SqlRestFilter(['foo' => 'no'], ['name']);
		Assert::same(
			'SELECT * FROM world',
			$filter->expression('SELECT * FROM world')
		);
		Assert::same([], $filter->criteria([]));
	}

	public function testNoAllowedCriteriaAsEmptyArray() {
		$filter = new Dataset\SqlRestFilter(['foo' => 'no'], []);
		Assert::same(
			'SELECT * FROM world',
			$filter->expression('SELECT * FROM world')
		);
		Assert::same([], $filter->criteria([]));
	}
}


(new SqlRestFilter())->run();