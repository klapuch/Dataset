<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Fake
 */
final class FakeSelection implements Selection {
	/** @var mixed[]|null */
	private $criteria;

	public function __construct(?array $criteria = null) {
		$this->criteria = $criteria;
	}

	public function criteria(): array {
		return $this->criteria;
	}
}
