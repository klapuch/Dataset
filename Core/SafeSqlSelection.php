<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Safe SQL selection aimed to protect against SQL injection
 */
final class SafeSqlSelection implements Selection {
	private $origin;

	public function __construct(Selection $origin) {
		$this->origin = $origin;
	}

	public function criteria(): array {
		$dangerous = $this->dangerous($this->origin->criteria());
		if ($dangerous) {
			throw new \UnexpectedValueException(
				sprintf('There are some dangerous criteria: %s', implode(', ', $dangerous))
			);
		}
		return $this->origin->criteria();
	}

	private function dangerous(array $criteria): array {
		return preg_grep('~^[a-zA-Z_][a-zA-Z0-9_]*\z~', array_keys($criteria), PREG_GREP_INVERT);
	}
}