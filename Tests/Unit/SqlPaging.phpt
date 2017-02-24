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
}


(new SqlPaging())->run();