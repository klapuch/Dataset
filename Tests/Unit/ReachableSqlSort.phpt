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

final class ReachableSqlSort extends Tester\TestCase {
	public function testSingleReachableDirection() {
		Assert::noError(function() {
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(' bar'),
				['number' => 'desc']
			))->expression('foo');
		});
	}

	public function testMultipleReachableDirections() {
		Assert::same(
			'foo bar',
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(' bar'),
				['name' => 'asc', 'number' => 'desc']
			))->expression('foo')
		);
	}

	public function testManyReachableDirections() {
		Assert::same(
			'foo bar',
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(' bar'),
				['name' => 'asc', 'number' => 'desc', 'foo' => 'asc']
			))->expression('foo')
		);
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnUnknownDirection() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => 'up']
		))->expression('foo');
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnMultipleUnknownDirections() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => 'up', 'number' => 'down']
		))->expression('foo');
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnManyUnknownDirections() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => 'up', 'number' => 'down', 'foo' => 'bar', 'baz' => 'bar']
		))->expression('foo');
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnPartiallyUnknownDirections() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => 'desc', 'number' => 'down']
		))->expression('foo');
	}

	public function testAllowingCaseInsensitiveDirections() {
		Assert::noError(function() {
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(''),
				['name' => 'DesC', 'number' => 'AsC']
			))->expression('foo');
		});
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnDirectionsConsistedFromAscOrDescWords() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => 'ascend', 'number' => 'descend']
		))->expression('foo');
	}

	public function testPassingOnEntirelyEmptySorts() {
		Assert::noError(function() {
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(''),
				[]
			))->expression('foo');
		});
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnEmptyDirections() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => null]
		))->expression('foo');
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnPartiallyEmptyDirections() {
		Assert::noError(function() {
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(),
				['name' => 'asc', 'number' => null]
			))->expression('foo');
		});
	}

	/**
	 * @throws \UnexpectedValueException Allowed directions are ASC, DESC
	 */
	public function testThrowingOnWhiteSpaceOnlyEmptyDirection() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => ' ']
		))->expression('foo');
	}
}


(new ReachableSqlSort())->run();