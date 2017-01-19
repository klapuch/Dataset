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

final class SqlRange extends Tester\TestCase {
	public function testEmptySourceCriteria() {
		Assert::same([], (new Dataset\SqlRange(10, 5))->criteria([]));
	}

	public function testAppendingToNotLimitedQuery() {
		Assert::same(
			'SELECT * FROM world LIMIT 10 OFFSET 5',
			(new Dataset\SqlRange(10, 5))->expression('SELECT * FROM world')
		);
	}

	public function testNotSpecifiedOffsetConsideredAsBegin() {
		Assert::same(
			'SELECT * FROM world LIMIT 10 OFFSET 0',
			(new Dataset\SqlRange(10))->expression('SELECT * FROM world')
		);
	}

	public function testRealOffset() {
		Assert::same(
			'SELECT * FROM world LIMIT 10 OFFSET 0',
			(new Dataset\SqlRange(10, -5))->expression('SELECT * FROM world')
		);
	}

	public function testNotConstrainedLimit() {
		Assert::same(
			'SELECT * FROM world LIMIT -10 OFFSET 5',
			(new Dataset\SqlRange(-10, 5))->expression('SELECT * FROM world')
		);
	}

	/*public function testReplacingStatedLimitAndOffset() {
		$source = 'SELECT * FROM world LIMIT 10 OFFSET 5';
		Assert::same(
			'SELECT * FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($source)
		);
	}*/

	public function testReplacingStatedLimitOrOffset() {
		$limitSource = 'SELECT * FROM world LIMIT 10';
		$offsetSource = 'SELECT * FROM world OFFSET 5';
		Assert::same(
			'SELECT * FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($limitSource)
		);
		Assert::same(
			'SELECT * FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($offsetSource)
		);
	}
}


(new SqlRange())->run();