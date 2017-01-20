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
	public function testPassingSourceCriteriaWithoutChange() {
		Assert::same(['a'], (new Dataset\SqlRange(10, 5))->criteria(['a']));
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

	public function testArbitraryLimit() {
		Assert::same(
			'SELECT * FROM world LIMIT -10 OFFSET 5',
			(new Dataset\SqlRange(-10, 5))->expression('SELECT * FROM world')
		);
	}

	public function testReplacingStatedLimitAndOffset() {
		$limitOffsetSource = 'SELECT * FROM world LIMIT 10 OFFSET 5';
		$offsetLimitSource = 'SELECT * FROM world OFFSET 10 LIMIT 5';
		$replacement = 'SELECT * FROM world LIMIT 20 OFFSET 1';
		$range = new Dataset\SqlRange(20, 1);
		Assert::same($replacement, $range->expression($limitOffsetSource));
		Assert::same($replacement, $range->expression($offsetLimitSource));
	}

	public function testReplacingStatedLimitOrOffset() {
		$limitSource = 'SELECT * FROM world LIMIT 10';
		$offsetSource = 'SELECT * FROM world OFFSET 5';
		$replacement = 'SELECT * FROM world LIMIT 20 OFFSET 1';
		$range = new Dataset\SqlRange(20, 1);
		Assert::same($replacement, $range->expression($limitSource));
		Assert::same($replacement, $range->expression($offsetSource));
	}

	public function testAppendingToNotLimitedQuery() {
		Assert::same(
			'SELECT * FROM world LIMIT 10 OFFSET 5',
			(new Dataset\SqlRange(10, 5))->expression('SELECT * FROM world')
		);
	}

	public function testReplacingAsCaseInsensitiveMatch() {
		$source = 'SELECT * FROM world limit 10 offset 5';
		Assert::same(
			'SELECT * FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($source)
		);
	}

	public function testAppendingToOuterQuery() {
		$source = 'SELECT (SELECT * FROM world) FROM world';
		Assert::same(
			'SELECT (SELECT * FROM world) FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($source)
		);
	}

	public function testReformatting() {
		$source = 'SELECT * FROM world
				LIMIT                  10
					OFFSET  		20        ';
		
		Assert::same(
			'SELECT * FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($source)
		);
	}

	public function testAppendingToOuterQueryWithinStatedLimitationInInnerQuery() {
		$limitSource = 'SELECT (SELECT * FROM world LIMIT 5) FROM world';
		$offsetSource = 'SELECT (SELECT * FROM world OFFSET 5) FROM world';
		$range = new Dataset\SqlRange(20, 1);
		Assert::same(
			'SELECT (SELECT * FROM world LIMIT 5) FROM world LIMIT 20 OFFSET 1',
			$range->expression($limitSource)
		);
		Assert::same(
			'SELECT (SELECT * FROM world OFFSET 5) FROM world LIMIT 20 OFFSET 1',
			$range->expression($offsetSource)
		);
	}

	/** TODO: Desired behavior
	public function testReplacingOuterLimitAndOffset() {
		$source = 'SELECT (SELECT * FROM world LIMIT 6) FROM world LIMIT 10 OFFSET 5';
		Assert::same(
			'SELECT (SELECT * FROM world LIMIT 6) FROM world LIMIT 20 OFFSET 1',
			(new Dataset\SqlRange(20, 1))->expression($source)
		);
	}*/
}


(new SqlRange())->run();