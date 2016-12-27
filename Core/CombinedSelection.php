<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Multiple selections combined together acting like a huge single one
 */
final class CombinedSelection implements Selection {
	private $selections;

	public function __construct(Selection ...$selections) {
		$this->selections = $selections;
	}

	public function expression(string $source): string {
		return array_reduce(
			$this->selections,
			function(string $expression, Selection $selection): string {
				return $selection->expression($expression);
			},
			$source
		);
	}

	public function criteria(array $source): array {
		return array_reduce(
			$this->selections,
			function(array $criteria, Selection $selection): array {
				return $selection->criteria($criteria);
			},
			$source
		);
	}
}