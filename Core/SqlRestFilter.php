<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Sort working directly with REST filter format but exposing the SQL one
 */
final class SqlRestFilter implements Selection {
	private $criteria;
	private $allowedCriteria;

	public function __construct(array $criteria, array $allowedCriteria) {
		$this->criteria = $criteria;
		$this->allowedCriteria = $allowedCriteria;
	}

	public function expression(string $source): string {
		return $this->selection($this->filtered($this->criteria, $this->allowedCriteria))->expression($source);
	}

	public function criteria(array $source): array {
		return $this->selection($this->filtered($this->criteria, $this->allowedCriteria))->criteria($source);
	}

	/**
	 * Created selection
	 * @param array $criteria
	 * @return \Klapuch\Dataset\Selection
	 */
	private function selection(array $criteria): Selection {
		return new SafeSqlSelection(
			$criteria ? new SqlFilter($criteria) : new EmptySelection(),
			$criteria
		);
	}

	private function filtered(array $criteria, array $allowedCriteria): array {
		return array_intersect_key($criteria, array_flip($allowedCriteria));
	}
}