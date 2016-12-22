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

final class ParameterizedSqlQuery extends Tester\TestCase {
	/**
	 * @throws \UnexpectedValueException Parameters must be either named or bare placeholders
	 */
	public function testMismatch() {
		$statement = 'SELECT * FROM world';
		$parameters = [':name' => 'Dom', 1 => 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	public function testParametersWithouthDoubleDotPrefix() {
		$statement = 'SELECT * FROM world WHERE name = :name AND number = :number';
		$parameters = ['name' => 'Dom', 'number' => 666];
		$query = new Dataset\ParameterizedSqlQuery($statement, $parameters);
		Assert::same([':name' => 'Dom', ':number' => 666], $query->parameters());
		Assert::same($statement, $query->statement());
	}

	public function testParametersPartiallyWithouthDoubleDotPrefix() {
		$statement = 'SELECT * FROM world WHERE name = :name AND number = :number';
		$parameters = ['name' => 'Dom', ':number' => 666];
		$query = new Dataset\ParameterizedSqlQuery($statement, $parameters);
		Assert::equal([':name' => 'Dom', ':number' => 666], $query->parameters());
		Assert::same($statement, $query->statement());
	}

	public function testBarePlaceholders() {
		$statement = 'SELECT * FROM world WHERE name = ? AND number = ?';
		$parameters = ['Dom', 666];
		$query = new Dataset\ParameterizedSqlQuery($statement, $parameters);
		Assert::same($parameters, $query->parameters());
		Assert::same($statement, $query->statement());
	}

	public function testEmptyParametersWithoutNeed() {
		$statement = 'SELECT * FROM world';
		$query = new Dataset\ParameterizedSqlQuery($statement, []);
		Assert::same([], $query->parameters());
	}

	public function testBarePlaceholdersWithMessedUpPositions() {
		$statement = 'SELECT * FROM world WHERE name = ? AND number = ?';
		$parameters = [1 => 'Dom', 4 => 666];
		$query = new Dataset\ParameterizedSqlQuery($statement, $parameters);
		Assert::same([0 => 'Dom', 1 => 666], $query->parameters());
		Assert::same($statement, $query->statement());
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNotEnoughNeededPlaceholderParametersAsEmptySet() {
		$statement = 'SELECT * FROM world WHERE name = ?';
		(new Dataset\ParameterizedSqlQuery($statement, []))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNotEnoughNeededNamedParametersAsEmptySet() {
		$statement = 'SELECT * FROM world WHERE name = :name';
		(new Dataset\ParameterizedSqlQuery($statement, []))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNotEnoughNeededParameters() {
		$statement = 'SELECT * FROM world';
		$parameters = ['Dom', 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testSomeUsedPlaceholderParameters() {
		$statement = 'SELECT * FROM world WHERE name = ?';
		$parameters = ['Dom', 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testSomeUsedNamedParameters() {
		$statement = 'SELECT * FROM world WHERE name = :name';
		$parameters = [':name' => 'Dom', ':number' => 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNotEnoughPlaceholderParameters() {
		$statement = 'SELECT * FROM world WHERE name = ? AND number = ? AND skill = ?';
		$parameters = ['Dom', 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNotEnoughNamedParameters() {
		$statement = 'SELECT * FROM world WHERE name = :name AND number = :number AND skill = :skill';
		$parameters = [':name' => 'Dom', ':number' => 666];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	public function testWeirdFormattedStatement() {
		$statement = 'SELECT * FROM
			world
			WHERE name =               
				:name           
';
		$parameters = [':name' => 'Dom'];
		$query = new Dataset\ParameterizedSqlQuery(
			$statement,
			$parameters
		);
		Assert::same('SELECT * FROM world WHERE name = :name', $query->statement());
		Assert::noError(function() use($query) {
			$query->parameters();
		});
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testDifferentlyNamedParameters() {
		$statement = 'SELECT * FROM world WHERE name = :name';
		$parameters = [':foo' => 'Dom'];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testNamedParametersWithSpaceAfterDoubleDot() {
		$statement = 'SELECT * FROM world WHERE name = : name';
		$parameters = [':name' => 'Dom'];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	public function testNoSpacesWithPlaceholderParameters() {
		$statement = 'INSERT INTO world (?, ?)';
		$parameters = ['foo', 'bar'];
		Assert::noError(function() use($statement, $parameters) {
			(new Dataset\ParameterizedSqlQuery(
				$statement,
				$parameters
			))->parameters();
		});
	}

	public function testNoSpacesWithNamedParameters() {
		$statement = 'INSERT INTO world (:name, :foo)';
		$parameters = [':name' => 'foo', ':foo' => 'name'];
		Assert::noError(function() use($statement, $parameters) {
			(new Dataset\ParameterizedSqlQuery(
				$statement,
				$parameters
			))->parameters();
		});
	}

	/**
	 * @throws \UnexpectedValueException Not all parameters are used
	 */
	public function testCaseInsensitiveParameters() {
		$statement = 'SELECT * FROM world WHERE name = :name';
		$parameters = [':NAME' => 'Dom'];
		(new Dataset\ParameterizedSqlQuery($statement, $parameters))->parameters();
	}

	public function testMultipleNamedParametersInStatement() {
		$statement = 'SELECT :name FROM world WHERE name = :name';
		$parameters = [':name' => 'Dom'];
		Assert::noError(function() use($statement, $parameters) {
			(new Dataset\ParameterizedSqlQuery(
				$statement,
				$parameters
			))->parameters();
		});
	}
}


(new ParameterizedSqlQuery())->run();