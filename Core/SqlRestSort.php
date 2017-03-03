<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Sort working directly with REST sort format but exposing the SQL one
 */
final class SqlRestSort extends RestSort {
	private $allowedCriteria;

	public function __construct(string $criteria, array $allowedCriteria) {
		parent::__construct($criteria);
		$this->allowedCriteria = $allowedCriteria;
	}

	public function expression(string $source): string {
		return $this->selection($this->sorts())->expression($source);
	}

	public function criteria(array $source): array {
		return $this->selection($this->sorts())->criteria($source);
	}

	/**
	 * Created selection
	 * @param array $sorts
	 * @return Selection
	 */
	private function selection(array $sorts): Selection {
		return new SafeSqlSelection(
			new AllowedSelection(
				new SafeSqlSelection(
					new ReachableSqlSort(new SqlSort($sorts), $sorts),
					$sorts
				),
				array_keys($sorts),
				$this->allowedCriteria
			),
			$sorts
		);
	}
}