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

final class RestSort extends Tester\TestCase {
	public function testNoGivenSort() {
		Assert::same(['sort' => []], (new Dataset\RestSort(''))->criteria());
		Assert::same(['sort' => []], (new Dataset\RestSort('  '))->criteria());
	}

	public function testMinusAsDescend() {
		Assert::same(
			['sort' => ['foo' => 'DESC']],
			(new Dataset\RestSort('-foo'))->criteria()
		);
	}

	public function testPlusAsAscend() {
		Assert::same(
			['sort' => ['foo' => 'ASC']],
			(new Dataset\RestSort('+foo'))->criteria()
		);
	}

	public function testNoUnaryOperatorAsAscendByDefault() {
		Assert::same(
			['sort' => ['foo' => 'ASC']],
			(new Dataset\RestSort('foo'))->criteria()
		);
	}

	public function testCombiningAllOperators() {
		Assert::same(
			['sort' => ['foo' => 'ASC', 'bar' => 'ASC', 'baz' => 'DESC']],
			(new Dataset\RestSort('foo,+bar,-baz'))->criteria()
		);
	}

	public function testAcceptingFirstPassedValue() {
		Assert::same(
			['sort' => ['foo' => 'ASC']],
			(new Dataset\RestSort('foo,+foo,-foo'))->criteria()
		);
	}

	public function testUnknownOperatorAsNormalValue() {
		Assert::same(['sort' => ['foo' => 'ASC', '#bar' => 'ASC']], (new Dataset\RestSort('foo,#bar'))->criteria());
	}

	public function testTrimSpacesAroundDelimiters() {
		Assert::same(
			['sort' => ['foo' => 'ASC', 'bar' => 'ASC', 'baz' => 'DESC']],
			(new Dataset\RestSort('foo , +bar , -baz'))->criteria()
		);
	}

	public function testRemovingTrailingDelimited() {
		Assert::same(
			['sort' => ['foo' => 'ASC', 'bar' => 'ASC', 'baz' => 'DESC']],
			(new Dataset\RestSort('foo,+bar,-baz, '))->criteria()
		);
	}

	public function testEmptyOutputWithPassingOperatorWithoutField() {
		Assert::same(['sort' => []], (new Dataset\RestSort('-'))->criteria());
		Assert::same(['sort' => []], (new Dataset\RestSort('+'))->criteria());
		Assert::same(['sort' => []], (new Dataset\RestSort('+ '))->criteria());
		Assert::same(['sort' => []], (new Dataset\RestSort(' + '))->criteria());
	}
}


(new RestSort())->run();