<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Sort for REST API accepting format: -foo,+bar,baz
 */
abstract class RestSort implements Selection {
	private const DELIMITER = ',';
	private const DEFAULT_OPERATOR = '';
	private const OPERATORS = [
		self::DEFAULT_OPERATOR => 'asc',
		'-' => 'desc',
		'+' => 'asc',
	];
	private $criteria;

	public function __construct(string $criteria) {
		$this->criteria = $criteria;
	}

	/**
	 * All the transformed sorts
	 * @return array
	 */
	final protected function sorts(): array {
		return array_reduce(
			array_filter(
				array_map('trim', explode(self::DELIMITER, $this->criteria)),
				function(string $criteria): bool {
					return strlen($criteria) > 1;
				}
			),
			function(array $sorts, string $field): array {
				$operator = $this->operator($field);
				return $sorts + [
					substr($field, strlen($operator)) => self::OPERATORS[$operator]
				];
			},
			[]
		);
	}

	/**
	 * Operator extracted from the field
	 * @param string $field
	 * @return string
	 */
	private function operator(string $field): string {
		$operator = substr($field, 0, 1);
		return array_key_exists($operator, self::OPERATORS)
			? $operator
			: self::DEFAULT_OPERATOR;
	}
}