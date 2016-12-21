<?php
declare(strict_types = 1);
namespace Klapuch\Dataset;

interface Query {
	/**
	 * Statement of the query
	 * @return string
	 */
	public function statement(): string;

	/**
	 * All the parameters related with the statement
	 * @return array
	 */
	public function parameters(): array;
}