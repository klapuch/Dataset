<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

interface Selection {
	/**
	 * Expression of the selection
	 * @param string $source
	 * @return string
	 */
	public function expression(string $source): string;

	/**
	 * All the criteria related with the expression
	 * @param array $source
	 * @return array
	 */
	public function criteria(array $source): array;
}