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

final class ForbiddenSelection extends Tester\TestCase {
	public function testPassingOnNoForbiddenCriteria() {
		Assert::same(
			['foo' => 'a', 'bar' => 'b'],
			(new Dataset\ForbiddenSelection(
				new Dataset\FakeSelection(['foo' => 'a', 'bar' => 'b']),
				[]
			))->criteria()
		);
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "foo"
	 */
	public function testThrowingOnSomeForbiddenCriteria() {
		(new Dataset\ForbiddenSelection(
			new Dataset\FakeSelection(['foo' => 'a', 'bar' => 'b']),
			['foo']
		))->criteria();
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "foo, bar"
	 */
	public function testThrowingOnManyForbiddenCriteria() {
		(new Dataset\ForbiddenSelection(
			new Dataset\FakeSelection(['foo' => 'a', 'bar' => 'b', 'baz' => 'c']),
			['foo', 'bar']
		))->criteria();
	}

	/**
	 * @throws \UnexpectedValueException Following criteria are not allowed: "FOO, bar"
	 */
	public function testThrowingWithIgnoredCases() {
		(new Dataset\ForbiddenSelection(
			new Dataset\FakeSelection(['foo' => 'a', 'bar' => 'b', 'baz' => 'c']),
			['FOO', 'bar']
		))->criteria();
	}
}


(new ForbiddenSelection())->run();