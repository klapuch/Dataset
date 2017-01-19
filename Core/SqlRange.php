<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * LIMIT and OFFSET for SQL
 */
final class SqlRange implements Selection {
	private const BEGIN = 0;
	private $limit;
	private $offset;

	public function __construct(int $limit, int $offset = self::BEGIN) {
		$this->limit = $limit;
		$this->offset = $offset;
	}

	public function expression(string $source): string {
		return $this->put($this->limit, $this->offset, $source);
	}

	public function criteria(array $source): array {
		return $source;
	}

	/**
	 * Put limit and offset to the source
	 * @param int $limit
	 * @param int $offset
	 * @param string $source
	 * @return string
	 */
	private function put(int $limit, int $offset, string $source): string {
		if($this->constrained($source))
			return $this->replace($source, $limit, $offset);
		return $source . ' ' . $this->clause($limit, $offset);
	}

	/**
	 * Replace source with the given limit and offset
	 * @param string $source
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	private function replace(string $source, int $limit, int $offset): string {
		return preg_replace(
			'~(limit|offset) \d+\s?~i',
			$this->clause($limit, $offset),
			$source
		);
	}

	/**
	 * Constructed LIMIT and OFFSET clause
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	private function clause(int $limit, int $offset): string {
		return sprintf(
			'LIMIT %d OFFSET %d',
			$limit,
			max($offset, self::BEGIN)
		);
	}

	/**
	 * Is there already LIMIT or OFFSET?
	 * @param string $source
	 * @return bool
	 */
	private function constrained(string $source): bool {
		return stripos($source, 'limit') !== false
		|| stripos($source, 'offset') !== false;
	}
}