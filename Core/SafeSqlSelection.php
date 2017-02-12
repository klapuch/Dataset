<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Safe SQL selection aimed to protect against SQL injection
 */
final class SafeSqlSelection implements Selection {
	private $origin;
	private $criteria;

	public function __construct(Selection $origin, array $criteria) {
		$this->origin = $origin;
		$this->criteria = $criteria;
	}

	public function expression(string $source): string {
		if($this->safe($this->criteria))
			return $this->origin->expression($source);
		return $source;
	}

	public function criteria(array $source): array {
		if($this->safe($this->criteria))
			return $this->origin->criteria($source);
		return $source;
	}

	private function safe(array $criteria): bool {
		$columns = array_keys($criteria);
		return preg_grep('~^[a-zA-Z_][a-zA-Z0-9_]*\z~', $columns) === $columns;
	}
}