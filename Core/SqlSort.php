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
		return trim(
			$this->put(
				$this->criteria,
				preg_replace('~\s+~', ' ', $source)
			)
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
		if($this->sorted($source))
			return $this->replace($criteria, $source);
		return $this->foist($criteria, $source);
	}

	/**
	 * Replaced old ORDER BY clause in source by the new given one in criteria
	 * @param array $criteria
	 * @param string $source
	 * @return string
	 */
	private function replace(array $criteria, string $source): string {
		return preg_replace(
			sprintf('~%s.+?(?=$|\s+LIMIT|\s+OFFSET)~i', self::CLAUSE),
			$this->clause($criteria),
			$source
		);
	}

	/**
	 * Criteria foisted to the source
	 * @param array $criteria
	 * @param string $source
	 * @return string
	 */
	private function foist(array $criteria, string $source): string {
		return preg_replace(
			'~\s+(?=$|LIMIT|OFFSET)~i',
			' ' . $this->clause($this->criteria) . ' ',
			$source . ' ',
			1
		);
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
	 * Clause containing sorts
	 * @param array $criteria
	 * @return string
	 */
	private function clause(array $criteria): string {
		return self::CLAUSE . ' ' . $this->sorts($criteria);
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