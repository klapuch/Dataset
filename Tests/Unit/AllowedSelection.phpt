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

final class AllowedSelection extends Tester\TestCase {
	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "foo, bar"
	 */
	public function testThrowingOnNothingAllowed() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['foo', 'bar'],
			[]
		))->criteria();
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "bar"
	 */
	public function testThrowingOnSomeUnknownCriteria() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['foo', 'bar'],
			['foo']
		))->criteria();
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "baz, bar"
	 */
	public function testThrowingOnMultipleUnknownCriteria() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['baz', 'bar'],
			['foo']
		))->criteria();
	}

	public function testAllowingExactAsMatch() {
		Assert::same(
			['xxx'],
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection(['xxx']),
				['bar', 'foo'],
				['foo', 'bar']
			))->criteria()
		);
	}

	public function testAllowingCaseInsensitiveMatch() {
		Assert::noError(function() {
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection(['xxx']),
				['FoO', 'bar'],
				['foo', 'BaR']
			))->criteria();
		});
	}

	public function testAllowingEmptyCriteria() {
		Assert::noError(function() {
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection(['XXX']),
				[],
				['foo', 'bar']
			))->criteria();
		});
	}
}


(new AllowedSelection())->run();