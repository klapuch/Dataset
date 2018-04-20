<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Filter for GET parameters
 */
final class RestFilter extends Filter {
	private $criteria;
	private $ignoredCriteria;
	private $forbiddenCriteria;

	public function __construct(
		array $criteria,
		array $ignoredCriteria = [],
		array $forbiddenCriteria = []
	) {
		$this->criteria = $criteria;
		$this->ignoredCriteria = $ignoredCriteria;
		$this->forbiddenCriteria = $forbiddenCriteria;
	}

	protected function filter(): array {
		return (new ForbiddenSelection(
			new FakeSelection(
				array_diff_key(
					$this->criteria,
					array_flip($this->ignoredCriteria)
				)
			),
			$this->forbiddenCriteria
		))->criteria();
	}
}