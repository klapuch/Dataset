<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

/**
 * Selection for partial response
 */
final class PartialSelection implements Selection {
	private $path;
	private $source;

	public function __construct(string $path, array $source) {
		$this->path = $path;
		$this->source = $source;
	}

	public function criteria(): array {
		if (!strlen($this->path))
			return $this->source;
		return $this->intersection(
			$this->source,
			array_merge_recursive(
				...array_reduce(
					array_map('trim', explode(',', $this->path)),
					function (array $parts, string $part): array {
						$parts[] = $this->structure($part);
						return $parts;
					},
					[]
				)
			)
		);
	}

	private function intersection(array $array1, array $array2): array
	{
		$array1 = array_intersect_key($array1, $array2);
		foreach ($array1 as $key => &$value)
			if ($this->isArray($value))
				$value = $this->isArray($array2[$key]) ? $this->intersection($value, $array2[$key]) : $value;
		return $array1;
	}

	private function structure(string $part): array
	{
		$structure = [];
		$current = &$structure;
		foreach (explode('.', $part) as $segment)
			$current = &$current[$segment];
		return $structure;
	}

	private function isArray($value): bool
	{
		return $value === (array) $value;
	}
}
