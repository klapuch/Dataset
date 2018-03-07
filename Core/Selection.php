<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

interface Selection {
	/**
	 * All the criteria related with need of a user
	 * @return array
	 */
	public function criteria(): array;
}