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

final class AllowedSelection extends Tester\TestCase {
	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "foo, bar"
	 */
	public function testThrowingOnNothingAllowed() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['foo', 'bar'],
			[]
		))->expression('');
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "bar"
	 */
	public function testThrowingOnSomeUnknownCriteria() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['foo', 'bar'],
			['foo']
		))->expression('');
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "baz, bar"
	 */
	public function testThrowingOnMultipleUnknownCriteria() {
		(new Dataset\AllowedSelection(
			new Dataset\FakeSelection(),
			['baz', 'bar'],
			['foo']
		))->expression('');
	}

	public function testAllowingExactMatch() {
		Assert::same(
			'XXX',
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection('XXX'),
				['bar', 'foo'],
				['foo', 'bar']
			))->expression('')
		);
	}

	public function testAllowingCaseInsensitiveMatch() {
		Assert::noError(function() {
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection('XXX'),
				['FoO', 'bar'],
				['foo', 'BaR']
			))->expression('');
		});
	}

	public function testAllowingEmptyCriteria() {
		Assert::noError(function() {
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection('XXX'),
				[],
				['foo', 'bar']
			))->expression('');
		});
	}

	public function testNoConstraintOnCriteria() {
		Assert::same(
			[],
			(new Dataset\AllowedSelection(
				new Dataset\FakeSelection(null, []),
				['bar', 'foo'],
				[]
			))->criteria([])
		);
	}
}


(new AllowedSelection())->run();