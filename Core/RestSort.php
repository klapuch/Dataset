<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Sort for REST API accepting format: -foo,+bar,baz
 */
final class RestSort extends Sort {
	private const DELIMITER = ',';
	private const OPERATOR_LENGTH = 1;
	private const DEFAULT_OPERATOR = '';
	private const OPERATORS = [
		self::DEFAULT_OPERATOR => 'ASC',
		'-' => 'DESC',
		'+' => 'ASC',
	];

	/** @var string */
	private $criteria;

	/** @var mixed[] */
	private $forbiddenCriteria;

	public function __construct(string $criteria, array $forbiddenCriteria = []) {
		$this->criteria = $criteria;
		$this->forbiddenCriteria = $forbiddenCriteria;
	}

	protected function sort(): array {
		$sort = array_reduce(
			array_filter(
				array_map('trim', explode(self::DELIMITER, $this->criteria)),
				static function(string $criteria): bool {
					return strlen($criteria) > self::OPERATOR_LENGTH;
				}
			),
			function(array $sorts, string $field): array {
				$operator = $this->operator($field);
				return $sorts + [
					substr($field, strlen($operator)) => self::OPERATORS[$operator],
				];
			},
			[]
		);
		return (new ForbiddenSelection(
			new FakeSelection($sort),
			$this->forbiddenCriteria
		))->criteria();
	}

	/**
	 * Operator extracted from the field
	 * @param string $field
	 * @return string
	 */
	private function operator(string $field): string {
		$operator = substr($field, 0, self::OPERATOR_LENGTH);
		return array_key_exists($operator, self::OPERATORS)
			? $operator
			: self::DEFAULT_OPERATOR;
	}
}
