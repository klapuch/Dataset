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

final class SafeSqlSelection extends Tester\TestCase {
	/**
	 * @dataProvider validColumns
	 */
	public function testValidColumnsAffectingSources($columns) {
		$selection = new Dataset\SafeSqlSelection(
			new Dataset\FakeSelection('world', ['OK']),
			array_combine($columns, array_fill(0, count($columns), 'foo'))
		);
		Assert::same(
			'SELECT * FROM world',
			$selection->expression('SELECT * FROM ')
		);
		Assert::same(['FOO', 'OK'], $selection->criteria(['FOO']));
	}

	/**
	 * @dataProvider invalidColumns
	 */
	public function testInvalidColumnsNotAffectingSources($columns) {
		[$sqlSource, $criteriaSource] = ['SELECT * FROM ', []];
		$selection = new Dataset\SafeSqlSelection(
			new Dataset\FakeSelection('world', ['OK']),
			array_combine($columns, array_fill(0, count($columns), 'foo'))
		);
		Assert::same($sqlSource, $selection->expression($sqlSource));
		Assert::same($criteriaSource, $selection->criteria($criteriaSource));
	}

	protected function invalidColumns(): array {
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

	protected function validColumns(): array {
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