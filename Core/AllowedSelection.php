<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Selection consisted only from the allowed criteria
 */
final class AllowedSelection implements Selection {
	private $origin;
	private $criteria;
	private $allowedCriteria;

	public function __construct(
		Selection $origin,
		array $criteria,
		array $allowedCriteria
	) {
		$this->origin = $origin;
		$this->criteria = $criteria;
		$this->allowedCriteria = $allowedCriteria;
	}

	public function criteria(): array {
		if ($this->allowed($this->criteria, $this->allowedCriteria))
			return $this->origin->criteria();
		throw new \UnexpectedValueException(
			sprintf(
				'Following criteria are not allowed: "%s"',
				implode(', ', $this->diff($this->criteria, $this->allowedCriteria))
			)
		);
	}

	/**
	 * Are the criteria consisted from the allowed one?
	 * @param array $criteria
	 * @param array $allowedCriteria
	 * @return bool
	 */
	private function allowed(array $criteria, array $allowedCriteria): bool {
		return !$this->diff($criteria, $allowedCriteria);
	}

	/**
	 * Diff, if any between criteria and the allowed one
	 * @param array $criteria
	 * @param array $allowedCriteria
	 * @return array
	 */
	private function diff(array $criteria, array $allowedCriteria): array {
		return array_udiff($criteria, $allowedCriteria, 'strcasecmp');
	}
}