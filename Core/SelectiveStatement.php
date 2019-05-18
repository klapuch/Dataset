<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

use Characterice\Sql\Expression;
use Characterice\Sql\Statement\Select;
use Characterice\Sql\Statement\Statement;

/**
 * Statement using Selection for building SQL with bind parameters
 */
final class SelectiveStatement extends Statement {
	/** @var \Characterice\Sql\Statement\Select\Query */
	private $origin;

	/** @var \Klapuch\Dataset\Selection */
	private $selection;

	public function __construct(Select\Query $origin, Selection $selection) {
		$this->origin = $origin;
		$this->selection = $selection;
	}

	protected function orders(): array {
		return $this->origin->orders();
	}

	public function sql(): string {
		$criteria = $this->selection->criteria();
		return $this->withWhere($criteria['filter'] ?? [])
			->orderBy(new Expression\OrderBy($criteria['sort'] ?? []))
			->limit($criteria['paging']['limit'] ?? \PHP_INT_MAX)
			->offset($criteria['paging']['offset'] ?? 0)
			->sql();
	}

	public function parameters(): array {
		$criteria = $this->selection->criteria();
		return $this->withWhere($criteria['filter'] ?? [])
			->orderBy(new Expression\OrderBy($criteria['sort'] ?? []))
			->parameters();
	}

	private function withWhere(array $filter): Select\Query {
		return array_reduce(
			array_keys($filter),
			static function (Select\Query $sql, string $parameter) use ($filter): Select\Query {
				$value = $filter[$parameter];
				if (is_array($value)) {
					return $sql->where(new Expression\WhereIn($parameter, $value));
				}
				return $sql->where(new Expression\Where($parameter, $value));
			},
			$this->origin
		);
	}
}
