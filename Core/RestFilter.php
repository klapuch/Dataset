<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Filter for GET parameters
 */
final class RestFilter extends Filter {
	private $criteria;
	private $allowedCriteria;

	public function __construct(array $criteria, array $allowedCriteria) {
		$this->criteria = $criteria;
		$this->allowedCriteria = $allowedCriteria;
	}

	protected function filter(): array {
		return array_intersect_key($this->criteria, array_flip($this->allowedCriteria));
	}
}