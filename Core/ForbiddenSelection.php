<?php
declare(strict_types = 1);

namespace Klapuch\Dataset;

/**
 * Selection consisted only from the desired criteria
 */
final class ForbiddenSelection implements Selection {
	/** @var \Klapuch\Dataset\Selection */
	private $origin;

	/** @var mixed[] */
	private $forbiddenCriteria;

	public function __construct(Selection $origin, array $forbiddenCriteria) {
		$this->origin = $origin;
		$this->forbiddenCriteria = $forbiddenCriteria;
	}

	public function criteria(): array {
		if (!$this->forbidden(array_keys($this->origin->criteria()), $this->forbiddenCriteria))
			return $this->origin->criteria();
		throw new \UnexpectedValueException(
			sprintf(
				'Following criteria are not allowed: "%s"',
				implode(', ', $this->match(array_keys($this->origin->criteria()), $this->forbiddenCriteria))
			)
		);
	}

	/**
	 * Is any of the criteria forbidden?
	 * @param array $criteria
	 * @param array $forbiddenCriteria
	 * @return bool
	 */
	private function forbidden(array $criteria, array $forbiddenCriteria): bool {
		return (bool) $this->match($criteria, $forbiddenCriteria);
	}

	/**
	 * All forbidden criteria, if any
	 * @param array $criteria
	 * @param array $forbiddenCriteria
	 * @return array
	 */
	private function match(array $criteria, array $forbiddenCriteria): array {
		return array_uintersect($forbiddenCriteria, $criteria, 'strcasecmp');
	}
}
