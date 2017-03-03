<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Filter selection for SQL
 */
final class SqlFilter implements Selection {
	private const FROM = 0,
		END_CLAUSES = 1;
	private const MATCH = 0,
		POSITION = 1;
	private const OPERATOR = 'AND',
		SEPARATOR = ' ' . self::OPERATOR . ' ';
	private const CLAUSE = 'WHERE';
	private $criteria;

	public function __construct(array $criteria) {
		$this->criteria = $criteria;
	}

	public function expression(string $source): string {
		return preg_replace(
			'~\s+~',
			' ',
			trim($this->put($this->criteria, $source))
		);
	}

	public function criteria(array $source): array {
		return $source + $this->criteria;
	}

	/**
	 * Put criteria to the source
	 * @param array $criteria
	 * @param string $source
	 * @return string
	 */
	private function put(array $criteria, string $source): string {
		if($this->constrained($source))
			return $this->extend($criteria, $source);
		return $this->foist($criteria, $source);
	}

	/**
	 * Is there already WHERE clause and therefore the source is constrained?
	 * @param string $source
	 * @return bool
	 */
	private function constrained(string $source): bool {
		return stripos($source, self::CLAUSE) !== false;
	}

	/**
	 * Extended source by the criteria
	 * @param array $criteria
	 * @param string $source
	 * @return string
	 */
	private function extend(array $criteria, string $source): string {
		$clauses = $this->clauses($source);
		return substr_replace(
			$source,
			self::SEPARATOR . $this->conditions($criteria),
			$clauses[self::END_CLAUSES][self::POSITION] ?? strlen($source),
			0
		);
	}

	/**
	 * Criteria foisted to the source
	 * @param array $criteria
	 * @param string $source
	 * @return string
	 */
	private function foist(array $criteria, string $source): string {
		$clauses = $this->clauses($source);
		return substr_replace(
			$source,
			' ' . $this->clause($criteria) . ' ',
			strlen($clauses[self::FROM][self::MATCH] ?? $source) + ($clauses[self::FROM][self::POSITION] ?? 0),
			0
		);
	}

	/**
	 * All the clauses extracted from the source
	 * @param string $source
	 * @return array
	 */
	private function clauses(string $source): array {
		$clauses = [
			'FROM [\w\d_]+',
			'GROUP BY',
			'ORDER BY',
			'LIMIT',
			'OFFSET',
		];
		$match = implode(
			'|',
			array_map(function(string $clause) {
				return '\s+' . $clause;
			}, str_replace(' ', '\s+', $clauses))
		);
		preg_match_all('~' . $match . '~', $source, $matches, PREG_OFFSET_CAPTURE);
		return current(array_filter($matches));
	}

	/**
	 * Clause containing sorts
	 * @param array $criteria
	 * @return string
	 */
	private function clause(array $criteria): string {
		return self::CLAUSE . ' ' . $this->conditions($criteria);
	}

	/**
	 * Created all the conditions from the criteria
	 * @param array $criteria
	 * @return string
	 */
	private function conditions(array $criteria): string {
		return $this->wrap(
			implode(
				self::SEPARATOR,
				array_map(
					[$this, 'condition'],
					array_map([$this, 'column'], array_keys($this->criteria)),
					array_keys($this->criteria)
				)
			),
			'()',
			$criteria
		);
	}

	/**
	 * Wrapped source by the given criteria
	 * @param string $source
	 * @param string $wrap
	 * @param array $criteria
	 * @return string
	 */
	private function wrap(string $source, string $wrap, array $criteria): string {
		[$opening, $closing] = str_split($wrap);
		return count($criteria) > 1 ? $opening . $source . $closing : $source;
	}

	/**
	 * Condition in form column = :placeholder
	 * @param string $column
	 * @param string $placeholder
	 * @return string
	 */
	private function condition(string $column, string $placeholder): string {
		return sprintf('%s = %s', $column, $placeholder);
	}

	/**
	 * From parameterized parameter is made regular column
	 * @param string $field
	 * @return string
	 */
	private function column(string $field): string {
		return str_replace(':', '', $field);
	}
}