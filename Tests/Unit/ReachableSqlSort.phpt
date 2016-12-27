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

final class ReachableSqlSort extends Tester\TestCase {
	public function testReachableDirections() {
		Assert::same(
			'foo bar',
			(new Dataset\ReachableSqlSort(
				new Dataset\FakeSelection(' bar'),
				['name' => 'asc', 'number' => 'desc']
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

	public function testEntirelyEmptySorts() {
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
	public function testThrowingOnWhiteSpaceEmptyDirection() {
		(new Dataset\ReachableSqlSort(
			new Dataset\FakeSelection(),
			['name' => ' ']
		))->expression('foo');
	}
}


(new ReachableSqlSort())->run();