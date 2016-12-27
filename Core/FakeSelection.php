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
		return $source . $this->expression;
	}

	public function criteria(array $source): array {
		return array_merge($source, $this->criteria);
	}
}