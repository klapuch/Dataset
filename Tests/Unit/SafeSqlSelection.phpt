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

final class SafeSqlSelection extends Tester\TestCase {
	/**
	 * @dataProvider safeColumns
	 */
	public function testPassingOnSafeColumns(array $columns) {
		$selection = new Dataset\SafeSqlSelection(
			new Dataset\FakeSelection(array_combine($columns, array_fill(0, count($columns), mt_rand())))
		);
		Assert::same($columns, array_keys($selection->criteria()));
	}

	/**
	 * @dataProvider dangerousColumns
	 */
	public function testThrowingOnDangerousColumns(array $columns) {
		$selection = new Dataset\SafeSqlSelection(
			new Dataset\FakeSelection(array_combine($columns, array_fill(0, count($columns), mt_rand())))
		);
		Assert::exception(static function () use ($selection) {
			$selection->criteria();
		}, \UnexpectedValueException::class);
	}

	protected function dangerousColumns(): array {
		return [
			[['']],
			[[' ']],
			[['	']],
			[['0world']],
			[['`world`']],
			[['čworld']],
			[['worldč']],
			[['world*']],
			[['world ']],
			[['world *']],
			[['public.world']],
			[['0world', '1world']], // both invalid
			[['world', '0world']], // first valid, second invalid
			[['0world', 'world']], // first invalid, second valid
			[['world', 'valid', 666]], // first two valid, others invalid
			[[666, 777, 'world']], // first two invalid, others valid
			[[666]],
			[[true]],
			[['world
']],
		];
	}

	protected function safeColumns(): array {
		return [
			[['world']],
			[['World']],
			[['WoRlD']],
			[['world_']],
			[['world_', 'world']], // both valid
			[['world123']],
			[['world_123']],
			[['wORld_123']],
			[['_world']],
		];
	}
}


(new SafeSqlSelection())->run();
