<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Paging for SQL
 */
final class SqlPaging extends Paging {
	public function expression(string $source): string {
		return (new SqlRange(
			$this->limit(), $this->offset()
		))->expression($source);
	}

	public function criteria(array $source): array {
		return $source;
	}
}