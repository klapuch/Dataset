<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Empty selection
 */
final class EmptySelection implements Selection {
	public function criteria(): array {
		return [];
	}
}
