<?php
/*
 * Name: Your Combinations PHP
 * Author: Max Base
 * Date: 2022/10/05
 * Repository: https://github.com/basemax/YourCombinationsPHP
 */

class YourCombinations {
	private array $elements = [];
	private int $count_elements = 0;

	public function __construct(array $elements) {
		$this->elements = array_values($elements);
		$this->count_elements = count($this->elements);
	}
	
	public function PowerSet() {
		$powerSetCount = pow(2, $this->count_elements);
		for ($i = 0; $i < $powerSetCount; $i++) {
			$set = [];
			for ($j = 0; $j < $this->count_elements; $j++) {
				if ($i & (1 << $j)) $set[] = $this->elements[$j];
			}
			yield $set;
		}
	}

	public function Combinations(int $length, bool $with_repetition = false, int $position = 0, array $elements = []) {
		for ($i = $position; $i < $this->count_elements; $i++) {
			$elements[] = $this->elements[$i];
			if (count($elements) == $length) yield $elements;
			else foreach ($this->Combinations($length, $with_repetition, ($with_repetition == true ? $i : $i + 1), $elements) as $value2) yield $value2;
			array_pop($elements);
		}
	}

	public function Permutations(int $length, bool $with_repetition = false, array $elements = [], array $keys = []) {
		foreach($this->elements as $key => $value) {
			if ($with_repetition == false) if (in_array($key, $keys)) continue;
			$keys[] = $key;
			$elements[] = $value;
			if (count($elements) == $length) yield $elements;
			else foreach ($this->Permutations($length, $with_repetition, $elements, $keys) as $value2) yield $value2;
			array_pop($keys);
			array_pop($elements);
		}
	}
}
