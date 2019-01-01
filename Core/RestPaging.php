<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Paging for REST API
 */
final class RestPaging extends Paging {
	/** @var int */
	private $page;

	/** @var int */
	private $perPage;

	/** @var int */
	private $default;

	public function __construct(int $page, int $perPage, int $default = 100) {
		$this->page = $page;
		$this->perPage = $perPage;
		$this->default = $default;
	}

	public function paging(): array {
		return [
			'limit' => $this->limit($this->perPage, $this->default),
			'offset' => $this->offset($this->page, $this->perPage),
		];
	}

	private function limit(int $perPage, int $default): int {
		return min($perPage, $default) ?: $default;
	}

	private function offset(int $page, int $perPage): int {
		return ($page - 1) * $perPage;
	}
}
