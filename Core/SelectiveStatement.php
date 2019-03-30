<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

use Klapuch\Sql;

/**
 * Statement using Selection for building SQL with bind parameters
 */
final class SelectiveStatement implements Sql\Statement {

	/** @var \Klapuch\Sql\Selection */
	private $origin;

	/** @var \Klapuch\Dataset\Selection */
	private $selection;

	public function __construct(Sql\Selection $origin, Selection $selection) {
		$this->origin = $origin;
		$this->selection = $selection;
	}

	public function sql(): string {
		return $this->statement($this->selection->criteria())->sql();
	}

	public function parameters(): Sql\Parameters {
		return $this->statement($this->selection->criteria())->parameters();
	}

	private function statement(array $criteria): Sql\Statement {
		[$filter, $sort, $paging] = [
			$criteria['filter'] ?? [],
			$criteria['sort'] ?? [],
			$criteria['paging'] ?? [],
		];

		return array_reduce(
			array_keys($filter),
			static function (Sql\Selection $sql, string $parameter) use ($filter): Sql\Where {
				$value = $filter[$parameter];
				if (is_array($value)) {
					return $sql->whereIn($parameter, [$parameter => $value]);
				}
				return $sql->where(sprintf('%1$s = :%1$s', $parameter), [$parameter => $value]);
			},
			$this->origin
		)->orderBy($sort)->limit($paging['limit'] ?? \PHP_INT_MAX)->offset($paging['offset'] ?? 0);
	}
}
