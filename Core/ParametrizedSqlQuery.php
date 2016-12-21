<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

final class ParametrizedSqlQuery implements Query {
	private const NAMED = 'string';
	private const PLACEHOLDER = 'integer';
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
	 * Approach to the parameters
	 * @param array $parameters
	 * @return string
	 */
	private function approach(array $parameters): string {
		return gettype(key($parameters));
	}

	/**
	 * Adjusted parameters
	 * @param array $parameters
	 * @return array
	 */
	private function adjustment(array $parameters): array {
		if($this->approach($parameters) === self::PLACEHOLDER)
			return array_values($parameters);
		return array_reduce(
			array_keys(
				array_filter(
					$parameters,
					function(string $name): bool {
						return substr($name, 0, 1) !== ':';
					},
					ARRAY_FILTER_USE_KEY
				)
			),
			function(array $names, string $name) use($parameters): array {
				unset($names[$name]);
				$names[':' . $name] = $parameters[$name];
				return $names;
			},
			$parameters
		);
	}

	/**
	 * Are some of the parameters unused?
	 * @param array $parameters
	 * @return bool
	 */
	private function unused(array $parameters): bool {
		if($this->approach($parameters) === self::PLACEHOLDER)
			return count($parameters) !== substr_count($this->statement(), '?');
		return count(
			preg_grep(
				'~^:[\w\d]+\z~',
				array_map(
					'trim',
					array_unique(preg_split('~[\s]+~', $this->statement()))
				)
			)
		) !== count($parameters);
	}
}