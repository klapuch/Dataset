<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Filter with pre-set name
 */
abstract class Filter implements Selection {
	abstract protected function filter(): array;

	final public function criteria(): array {
		return ['filter' => $this->filter()];
	}
}
