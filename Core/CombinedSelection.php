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

	public function criteria(): array {
		return array_merge_recursive(
			...array_map(
				function(Selection $selection): array {
					return $selection->criteria();
				},
				$this->selections
			)
		);
	}
}