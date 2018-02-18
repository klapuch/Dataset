<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Selection for partial response
 */
final class PartialSelection implements Selection {
	private $path;

	public function __construct(string $path) {
		$this->path = $path;
	}

	public function expression(string $source): string {
		return $source;
	}

	public function criteria(array $source): array {
		return array_intersect_key(
			$source,
			array_flip(array_map('trim', array_filter(explode(',', $this->path)))) ?: $source
		);
	}
}
