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

final class SqlPaging extends Tester\TestCase {
	public function testExpressionWithAppliedPaging() {
		Assert::same(
			'SELECT * FROM world LIMIT 30 OFFSET 0',
			(new Dataset\SqlPaging(1, 30))->expression('SELECT * FROM world')
		);
		Assert::same(
			'SELECT * FROM world LIMIT 30 OFFSET 30',
			(new Dataset\SqlPaging(2, 30))->expression('SELECT * FROM world')
		);
	}

	public function testLimitedLimit() {
		Assert::same(
			'SELECT * FROM world LIMIT 30 OFFSET 0',
			(new Dataset\SqlPaging(1, 300, 30))->expression('SELECT * FROM world')
		);
	}

	public function testArbitraryOffset() {
		Assert::same(
			'SELECT * FROM world LIMIT 10 OFFSET 2990',
			(new Dataset\SqlPaging(300, 10, 30))->expression('SELECT * FROM world')
		);
	}

	public function testUnrealPerPageWithDefaultFallback() {
		Assert::same(
			'SELECT * FROM world LIMIT 30 OFFSET 0',
			(new Dataset\SqlPaging(1, 0, 30))->expression('SELECT * FROM world')
		);
	}
}


(new SqlPaging())->run();