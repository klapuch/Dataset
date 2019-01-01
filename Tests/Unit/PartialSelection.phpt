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

final class PartialSelection extends Tester\TestCase {
	public function testGivingSingle() {
		Assert::same(
			['name' => 'Dom'],
			(new Dataset\PartialSelection('name', ['name' => 'Dom']))->criteria()
		);
	}

	public function testGivingSingleFromMultipleValues() {
		Assert::same(
			['name' => 'Dom'],
			(new Dataset\PartialSelection('name', ['name' => 'Dom', 'age' => 20]))->criteria()
		);
	}

	public function testGivingMultipleFromMultipleValues() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection('name,age', ['name' => 'Dom', 'age' => 20, 'title' => 'developer']))->criteria()
		);
	}

	public function testIgnoringSpaceAfterComma() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection('name, age', ['name' => 'Dom', 'age' => 20, 'title' => 'developer']))->criteria()
		);
	}

	public function testNoPathMeaningAllToBeReturned() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection('', ['name' => 'Dom', 'age' => 20]))->criteria()
		);
	}

	public function testAskingForNested() {
		Assert::same(
			['person' => ['name' => 'Dom']],
			(new Dataset\PartialSelection('person.name', ['person' => ['name' => 'Dom', 'age' => 20]]))->criteria()
		);
	}

	public function testAskingForRepetitive() {
		Assert::same(
			['person' => ['name' => ['first' => 'Dom'], 'age' => 20]],
			(new Dataset\PartialSelection('person.name.first,person.age', ['person' => ['name' => ['first' => 'Dom', 'last' => 'Me'], 'age' => 20]]))->criteria()
		);
	}

}


(new PartialSelection())->run();
