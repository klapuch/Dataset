<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Selection ensuring always reachable sort typical for SQL
 */
final class ReachableSqlSort implements Selection {
	private const DIRECTIONS = [
		'ASC',
		'DESC',
	];
	private $origin;
	private $sorts;

	public function __construct(Selection $origin, array $sorts) {
		$this->origin = $origin;
		$this->sorts = $sorts;
	}

	public function expression(string $source): string {
		if (!$this->reachable($this->sorts)) {
			throw new \UnexpectedValueException(
				sprintf(
					'Allowed directions are %s',
					implode(', ', self::DIRECTIONS)
				)
			);
		}
		return $this->origin->expression($source);
	}

	public function criteria(array $source): array {
		return $this->origin->criteria($source);
	}

	/**
	 * Is it possible to proceed sorting with the given sorts?
	 * @param array $sorts
	 * @return bool
	 */
	private function reachable(array $sorts): bool {
		return !array_udiff($sorts, self::DIRECTIONS, 'strcasecmp');
	}
}