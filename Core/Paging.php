<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Paging
 */
abstract class Paging implements Selection {
	private $page;
	private $perPage;
	private $limit;

	final public function __construct(int $page, int $perPage, int $limit = 100) {
		$this->page = $page;
		$this->perPage = $perPage;
		$this->limit = $limit;
	}

	final protected function limit(): int {
		return min($this->perPage, $this->limit);
	}

	final protected function offset(): int {
		return ($this->page - 1) * $this->perPage;
	}
}