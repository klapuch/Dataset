<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Sort selection for SQL
 */
final class SqlSort implements Selection {
	private const SEPARATOR = ', ';
	private $criteria;

	public function __construct(array $criteria) {
		$this->criteria = $criteria;
	}

	public function expression(string $source): string {
		return $source . $this->clause($source, $this->criteria);
	}

	public function criteria(array $source): array {
		return $source;
	}

	private function clause(string $source, array $criteria): string {
		if($this->sorted($source))
			return self::SEPARATOR . $this->sorts($criteria);
		return sprintf(' ORDER BY %s', $this->sorts($criteria));
	}

	/**
	 * Is the source already sorted?
	 * @param string $source
	 * @return bool
	 */
	private function sorted(string $source): bool {
		return stripos($source, 'ORDER BY') !== false;
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