<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Parameterized query for SQL databases
 */
final class ParameterizedSqlQuery implements Query {
	private const NAME_APPROACH = 'string';
	private const PLACEHOLDER_APPROACH = 'integer';
	private const UNKNOWN_APPROACH = 'null';
	private const PLACEHOLDER = '?';
	private const NAME_PREFIX = ':';
	private $statement;
	private $parameters;

	public function __construct(string $statement, array $parameters) {
		$this->statement = $statement;
		$this->parameters = $parameters;
	}

	public function statement(): string {
		return trim(preg_replace('~\s+~', ' ', $this->statement));
	}

	public function parameters(): array {
		if($this->mismatched($this->parameters)) {
			throw new \UnexpectedValueException(
				'Parameters must be either named or bare placeholders'
			);
		} elseif(!$this->used($this->statement(), $this->adjustment($this->parameters))) {
			throw new \UnexpectedValueException(
				'Not all parameters are used'
			);
		}
		return $this->adjustment($this->parameters);
	}

	/**
	 * Are the parameters mismatched?
	 * @param array $parameters
	 * @return bool
	 */
	private function mismatched(array $parameters): bool {
		return count(array_unique(array_map('gettype', array_keys($parameters)))) > 1;
	}

	/**
	 * Are the parameters used inside statement?
	 * @param array $parameters
	 * @param string $statement
	 * @return bool
	 */
	private function used(string $statement, array $parameters): bool {
		$statementParameters = $this->statementParameters($statement, $parameters);
		$matched = $this->matched($statementParameters, $parameters);
		if($matched && $this->approach($parameters) === self::NAME_APPROACH) {
			return count($parameters) === count(
				array_intersect($statementParameters, array_keys($parameters))
			);
		}
		return $matched;
	}

	/**
	 * Parameters extracted from the statement
	 * @param string $statement
	 * @param array $parameters
	 * @return array
	 */
	private function statementParameters(string $statement, array $parameters): array {
		$approaches = [
			self::UNKNOWN_APPROACH => ['placeholders', 'names'],
			self::PLACEHOLDER_APPROACH => ['placeholders'],
			self::NAME_APPROACH => ['names'],
		];
		return array_reduce(
			$approaches[$this->approach($parameters)],
			function(array $parameters, string $type) use($statement): array {
				return array_merge(
					call_user_func_array([$this, $type], [$statement]),
					$parameters
				);
			},
			[]
		);
	}

	/**
	 * Do the statement parameters match with parameters?
	 * @param array $statementParameters
	 * @param array $parameters
	 * @return bool
	 */
	private function matched(array $statementParameters, array $parameters): bool {
		return count($parameters) === count($statementParameters);
	}

	/**
	 * All the placeholders extracted from the statement
	 * @param string $statement
	 * @return array
	 */
	private function placeholders(string $statement): array {
		return array_fill(
			0,
			substr_count($statement, self::PLACEHOLDER),
			self::PLACEHOLDER
		);
	}

	/**
	 * All the names extracted from the statement
	 * @param string $statement
	 * @return array
	 */
	private function names(string $statement): array {
		return preg_grep(
			'~^:[\w\d]+$~',
			array_unique(explode(' ', $statement))
		);
	}

	/**
	 * What approach is used for parameterized query?
	 * @param array $parameters
	 * @return string
	 */
	private function approach(array $parameters): string {
		return strtolower(gettype(key($parameters)));
	}

	/**
	 * Adjusted parameters
	 * @param array $parameters
	 * @return array
	 */
	private function adjustment(array $parameters): array {
		if($this->approach($parameters) === self::PLACEHOLDER_APPROACH)
			return array_values($parameters);
		return array_reduce(
			array_keys(
				array_filter(
					$parameters,
					function(string $name): bool {
						return substr($name, 0, 1) !== self::NAME_PREFIX;
					},
					ARRAY_FILTER_USE_KEY
				)
			),
			function(array $names, string $name) use($parameters): array {
				unset($names[$name]);
				$names[self::NAME_PREFIX . $name] = $parameters[$name];
				return $names;
			},
			$parameters
		);
	}
}