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
		return $this->put(
			$this->limit,
			$this->offset,
			trim(preg_replace('~\s+~', ' ', $source))
		);
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
		if ($this->constrained($source))
			return $this->replace($source, $limit, $offset);
		return $this->append($source, $limit, $offset);
	}

	/**
	 * Replace source with the given limit and offset
	 * @param string $source
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	private function replace(string $source, int $limit, int $offset): string {
		preg_match(
			'~LIMIT|OFFSET~i',
			$source,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		return substr_replace(
			$source,
			$this->clause($limit, $offset),
			$matches[0][1],
			strlen($source)
		);
	}

	/**
	 * Source with appended LIMIT and OFFSET
	 * @param string $source
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	private function append(string $source, int $limit, int $offset): string {
		return $source . ' ' . $this->clause($limit, $offset);
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
			max($limit, 0),
			max($offset, self::BEGIN)
		);
	}

	/**
	 * Is there already LIMIT or OFFSET?
	 * @param string $source
	 * @return bool
	 */
	private function constrained(string $source): bool {
		return (bool) preg_match('~(LIMIT|OFFSET)\s+\d+$~i', $source);
	}
}