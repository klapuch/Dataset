<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Sort with pre-set name
 */
abstract class Sort implements Selection {
	abstract protected function sort(): array;

	final public function criteria(): array {
		return ['sort' => $this->sort()];
	}
}
