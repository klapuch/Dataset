<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

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
		return $this->statement;
	}

	public function parameters(): array {
		if($this->mismatched($this->parameters)) {
			throw new \UnexpectedValueException(
				'Parameters must be either named or bare placeholders'
			);
		} elseif($this->unused($this->parameters)) {
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
	 * Are some of the parameters unused?
	 * @param array $parameters
	 * @return bool
	 */
	private function unused(array $parameters): bool {
		$approaches = [
			self::UNKNOWN_APPROACH => ['placeholders', 'names'],
			self::PLACEHOLDER_APPROACH => ['placeholders'],
			self::NAME_APPROACH => ['names'],
		];
		return count($parameters) !== array_sum(
			array_map(function(string $type): int {
				return count(call_user_func([$this, $type]));
			},
			$approaches[$this->approach($parameters)])
		);
	}

	/**
	 * All the placeholders extracted from the statement
	 * @return array
	 */
	private function placeholders(): array {
		return array_fill(
			0,
			substr_count($this->statement(), self::PLACEHOLDER),
			self::PLACEHOLDER
		);
	}

	/**
	 * All the names extracted from the statement
	 * @return array
	 */
	private function names(): array {
		return preg_grep(
			'~^:[\w\d]+\z~',
			array_map(
				'trim',
				array_unique(preg_split('~[\s]+~', $this->statement()))
			)
		);
	}

	/**
	 * What approach is used for parameterized query
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