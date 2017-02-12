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

final class SqlRestSort extends Tester\TestCase {
	public function testNoGivenSort() {
		Assert::same('', (new Dataset\SqlRestSort(''))->expression(''));
		Assert::same('', (new Dataset\SqlRestSort('  '))->expression(''));
	}

	public function testNotAffectingCriteria() {
		Assert::same(
			['FOO'],
			(new Dataset\SqlRestSort('-foo,foo,~bar'))->criteria(['FOO'])
		);
	}

	public function testMinusAsDescend() {
		Assert::same(
			'ORDER BY foo desc',
			(new Dataset\SqlRestSort('-foo'))->expression('')
		);
	}

	public function testPlusAsAscend() {
		Assert::same(
			'ORDER BY foo asc',
			(new Dataset\SqlRestSort('+foo'))->expression('')
		);
	}

	public function testNoUnaryOperatorAsAscendByDefault() {
		Assert::same(
			'ORDER BY foo asc',
			(new Dataset\SqlRestSort('foo'))->expression('')
		);
	}

	public function testCombiningAllOperators() {
		Assert::same(
			'ORDER BY foo asc, bar asc, baz desc',
			(new Dataset\SqlRestSort('foo,+bar,-baz'))->expression('')
		);
	}

	public function testAcceptingFirstPassedValue() {
		Assert::same(
			'ORDER BY foo asc',
			(new Dataset\SqlRestSort('foo,+foo,-foo'))->expression('')
		);
	}

	public function testEmptyOutputOnInvalidOperator() {
		Assert::same('', (new Dataset\SqlRestSort('foo,#bar'))->expression(''));
	}

	public function testTrimSpacesAroundDelimiters() {
		Assert::same(
			'ORDER BY foo asc, bar asc, baz desc',
			(new Dataset\SqlRestSort('foo , +bar , -baz'))->expression('')
		);
	}

	public function testRemovingTrailingDelimited() {
		Assert::same(
			'ORDER BY foo asc, bar asc, baz desc',
			(new Dataset\SqlRestSort('foo,+bar,-baz, '))->expression('')
		);
	}

	public function testEmptyOutputWithPassingOperatorWithoutField() {
		Assert::same('', (new Dataset\SqlRestSort('-'))->expression(''));
		Assert::same('', (new Dataset\SqlRestSort('+'))->expression(''));
		Assert::same('', (new Dataset\SqlRestSort('+ '))->expression(''));
		Assert::same('', (new Dataset\SqlRestSort(' + '))->expression(''));
	}

	public function testMergingWithSource() {
		Assert::same(
			'prev ORDER BY foo asc, bar asc, baz desc',
			(new Dataset\SqlRestSort('foo,+bar,-baz'))->expression('prev')
		);
	}
}


(new SqlRestSort())->run();