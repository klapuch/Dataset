<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Paging
 */
abstract class Paging implements Selection {
	private $page;
	private $perPage;

	final public function __construct(int $page, int $perPage) {
		$this->page = $page;
		$this->perPage = $perPage;
	}

	final protected function limit(): int {
		return $this->perPage;
	}

	final protected function offset(): int {
		return ($this->page - 1) * $this->perPage;
	}
}