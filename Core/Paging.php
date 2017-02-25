<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Paging
 */
abstract class Paging implements Selection {
	private $page;
	private $perPage;
	private $default;

	final public function __construct(int $page, int $perPage, int $default = 100) {
		$this->page = $page;
		$this->perPage = $perPage;
		$this->default = $default;
	}

	final protected function limit(): int {
		return min($this->perPage, $this->default) ?: $this->default;
	}

	final protected function offset(): int {
		return ($this->page - 1) * $this->perPage;
	}
}