<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Empty selection
 */
final class EmptySelection implements Selection {
	public function expression(string $source): string {
		return $source;
	}

	public function criteria(array $source): array {
		return $source;
	}
}
