<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Paging with pre-set name
 */
abstract class Paging implements Selection {
	abstract protected function paging(): array;

	final public function criteria(): array {
		return ['paging' => $this->paging()];
	}
}