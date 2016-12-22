<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Fake
 */
final class FakeSelection implements Selection {
	private $expression;
	private $criteria;

	public function __construct(
		string $expression = null,
		array $criteria = null
	) {
		$this->expression = $expression;
		$this->criteria = $criteria;
	}
	public function expression(string $source): string {
		return $this->expression;
	}

	public function criteria(array $source): array {
		return $this->criteria;
	}
}