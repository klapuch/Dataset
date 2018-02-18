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
			(new Dataset\PartialSelection('name'))->criteria(['name' => 'Dom'])
		);
	}

	public function testGivingSingleFromMultipleValues() {
		Assert::same(
			['name' => 'Dom'],
			(new Dataset\PartialSelection('name'))->criteria(['name' => 'Dom', 'age' => 20])
		);
	}

	public function testGivingMultipleFromMultipleValues() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection('name,age'))->criteria(['name' => 'Dom', 'age' => 20, 'title' => 'developer'])
		);
	}

	public function testIgnoringSpaceAfterComma() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection('name, age'))->criteria(['name' => 'Dom', 'age' => 20, 'title' => 'developer'])
		);
	}

	public function testNoPathMeaningAllToBeReturned() {
		Assert::same(
			['name' => 'Dom', 'age' => 20],
			(new Dataset\PartialSelection(''))->criteria(['name' => 'Dom', 'age' => 20])
		);
	}
//
//	public function testAskingForNested() {
//		Assert::same(
//			['person' => ['name' => 'Dom']],
//			(new Dataset\PartialSelection('person(name)'))->criteria(['person' => ['name' => 'Dom', 'age' => 20]])
//		);
//	}
}


(new PartialSelection())->run();