<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Sort selection for SQL
 */
final class SqlSort implements Selection {
	private const SEPARATOR = ', ';
	private const CLAUSE = 'ORDER BY';
	private $criteria;

	public function __construct(array $criteria) {
		$this->criteria = $criteria;
	}

	public function expression(string $source): string {
		return $this->put(
			$this->criteria,
			preg_replace('~\s+~', ' ', $source)
		);
	}

	public function criteria(array $source): array {
		return $source;
	}

	/**
	 * Put the criteria to the source
	 * @param array $criteria
	 * @param string source
	 * @return string
	 */
	private function put(array $criteria, string $source): string {
		if($this->sorted($source)) {
			return preg_replace(
				sprintf('~%s.+?(?=$| LIMIT)~i', self::CLAUSE),
				$this->clause($criteria),
				$source
			);
		}
		return $source . ' ' . $this->clause($this->criteria);
	}

	/**
	 * Clause containing sorts
	 * @param array $criteria
	 * @return string
	 */
	private function clause(array $criteria): string {
		return self::CLAUSE . ' ' . $this->sorts($criteria);
	}

	/**
	 * Is the source already sorted?
	 * @param string $source
	 * @return bool
	 */
	private function sorted(string $source): bool {
		return preg_match(sprintf('~%s~i', self::CLAUSE), $source) === 1;
	}

	/**
	 * All sorts extracted from the criteria
	 * @param array $criteria
	 * @return string
	 */
	private function sorts(array $criteria): string {
		return implode(
			self::SEPARATOR,
			array_map(
				[$this, 'sort'],
				array_keys($criteria),
				$criteria
			)
		);
	}

	/**
	 * Single sort
	 * @param string $column
	 * @param string $direction
	 * @return string
	 */
	private function sort(string $column, string $direction): string {
		return sprintf('%s %s', $column, $direction);
	}
}