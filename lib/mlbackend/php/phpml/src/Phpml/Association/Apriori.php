<?php

declare(strict_types=1);

namespace Phpml\Association;

use Phpml\Helper\Predictable;
use Phpml\Helper\Trainable;

class Apriori implements Associator
{
    use Trainable;
    use Predictable;

    public const ARRAY_KEY_ANTECEDENT = 'antecedent';

    public const ARRAY_KEY_CONFIDENCE = 'confidence';

    public const ARRAY_KEY_CONSEQUENT = 'consequent';

    public const ARRAY_KEY_SUPPORT = 'support';

    /**
     * Minimum relative probability of frequent transactions.
     *
     * @var float
     */
    private $confidence;

    /**
     * The large set contains frequent k-length item sets.
     *
     * @var mixed[][][]
     */
    private $large = [];

    /**
     * Minimum relative frequency of transactions.
     *
     * @var float
     */
    private $support;

    /**
     * The generated Apriori association rules.
     *
     * @var mixed[][]
     */
    private $rules = [];

    /**
     * Apriori constructor.
     */
    public function __construct(float $support = 0.0, float $confidence = 0.0)
    {
        $this->support = $support;
        $this->confidence = $confidence;
    }

    /**
     * Get all association rules which are generated for every k-length frequent item set.
     *
     * @return mixed[][]
     */
    public function getRules(): array
    {
        if (count($this->large) === 0) {
            $this->large = $this->apriori();
        }

        if (count($this->rules) > 0) {
            return $this->rules;
        }

        $this->rules = [];

        $this->generateAllRules();

        return $this->rules;
    }

    /**
     * Generates frequent item sets.
     *
     * @return mixed[][][]
     */
    public function apriori(): array
    {
        $L = [];

        $items = $this->frequent($this->items());
        for ($k = 1; isset($items[0]); ++$k) {
            $L[$k] = $items;
            $items = $this->frequent($this->candidates($items));
        }

        return $L;
    }

    /**
     * @param mixed[] $sample
     *
     * @return mixed[][]
     */
    protected function predictSample(array $sample): array
    {
        $predicts = array_values(array_filter($this->getRules(), function ($rule) use ($sample): bool {
            return $this->equals($rule[self::ARRAY_KEY_ANTECEDENT], $sample);
        }));

        return array_map(static function ($rule) {
            return $rule[self::ARRAY_KEY_CONSEQUENT];
        }, $predicts);
    }

    /**
     * Generate rules for each k-length frequent item set.
     */
    private function generateAllRules(): void
    {
        for ($k = 2; isset($this->large[$k]); ++$k) {
            foreach ($this->large[$k] as $frequent) {
                $this->generateRules($frequent);
            }
        }
    }

    /**
     * Generate confident rules for frequent item set.
     *
     * @param mixed[] $frequent
     */
    private function generateRules(array $frequent): void
    {
        foreach ($this->antecedents($frequent) as $antecedent) {
            $confidence = $this->confidence($frequent, $antecedent);
            if ($this->confidence <= $confidence) {
                $consequent = array_values(array_diff($frequent, $antecedent));
                $this->rules[] = [
                    self::ARRAY_KEY_ANTECEDENT => $antecedent,
                    self::ARRAY_KEY_CONSEQUENT => $consequent,
                    self::ARRAY_KEY_SUPPORT => $this->support($frequent),
                    self::ARRAY_KEY_CONFIDENCE => $confidence,
                ];
            }
        }
    }

    /**
     * Generates the power set for given item set $sample.
     *
     * @param mixed[] $sample
     *
     * @return mixed[][]
     */
    private function powerSet(array $sample): array
    {
        $results = [[]];
        foreach ($sample as $item) {
            foreach ($results as $combination) {
                $results[] = array_merge([$item], $combination);
            }
        }

        return $results;
    }

    /**
     * Generates all proper subsets for given set $sample without the empty set.
     *
     * @param mixed[] $sample
     *
     * @return mixed[][]
     */
    private function antecedents(array $sample): array
    {
        $cardinality = count($sample);
        $antecedents = $this->powerSet($sample);

        return array_filter($antecedents, static function ($antecedent) use ($cardinality): bool {
            return (count($antecedent) != $cardinality) && ($antecedent != []);
        });
    }

    /**
     * Calculates frequent k = 1 item sets.
     *
     * @return mixed[][]
     */
    private function items(): array
    {
        $items = [];

        foreach ($this->samples as $sample) {
            foreach ($sample as $item) {
                if (!in_array($item, $items, true)) {
                    $items[] = $item;
                }
            }
        }

        return array_map(static function ($entry): array {
            return [$entry];
        }, $items);
    }

    /**
     * Returns frequent item sets only.
     *
     * @param mixed[][] $samples
     *
     * @return mixed[][]
     */
    private function frequent(array $samples): array
    {
        return array_values(array_filter($samples, function ($entry): bool {
            return $this->support($entry) >= $this->support;
        }));
    }

    /**
     * Calculates frequent k item sets, where count($samples) == $k - 1.
     *
     * @param mixed[][] $samples
     *
     * @return mixed[][]
     */
    private function candidates(array $samples): array
    {
        $candidates = [];

        foreach ($samples as $p) {
            foreach ($samples as $q) {
                if (count(array_merge(array_diff($p, $q), array_diff($q, $p))) != 2) {
                    continue;
                }

                $candidate = array_values(array_unique(array_merge($p, $q)));

                if ($this->contains($candidates, $candidate)) {
                    continue;
                }

                foreach ($this->samples as $sample) {
                    if ($this->subset($sample, $candidate)) {
                        $candidates[] = $candidate;

                        continue 2;
                    }
                }
            }
        }

        return $candidates;
    }

    /**
     * Calculates confidence for $set. Confidence is the relative amount of sets containing $subset which also contain
     * $set.
     *
     * @param mixed[] $set
     * @param mixed[] $subset
     */
    private function confidence(array $set, array $subset): float
    {
        return $this->support($set) / $this->support($subset);
    }

    /**
     * Calculates support for item set $sample. Support is the relative amount of sets containing $sample in the data
     * pool.
     *
     * @see \Phpml\Association\Apriori::samples
     *
     * @param mixed[] $sample
     */
    private function support(array $sample): float
    {
        return $this->frequency($sample) / count($this->samples);
    }

    /**
     * Counts occurrences of $sample as subset in data pool.
     *
     * @see \Phpml\Association\Apriori::samples
     *
     * @param mixed[] $sample
     */
    private function frequency(array $sample): int
    {
        return count(array_filter($this->samples, function ($entry) use ($sample): bool {
            return $this->subset($entry, $sample);
        }));
    }

    /**
     * Returns true if set is an element of system.
     *
     * @see \Phpml\Association\Apriori::equals()
     *
     * @param mixed[][] $system
     * @param mixed[]   $set
     */
    private function contains(array $system, array $set): bool
    {
        return (bool) array_filter($system, function ($entry) use ($set): bool {
            return $this->equals($entry, $set);
        });
    }

    /**
     * Returns true if subset is a (proper) subset of set by its items string representation.
     *
     * @param mixed[] $set
     * @param mixed[] $subset
     */
    private function subset(array $set, array $subset): bool
    {
        return count(array_diff($subset, array_intersect($subset, $set))) === 0;
    }

    /**
     * Returns true if string representation of items does not differ.
     *
     * @param mixed[] $set1
     * @param mixed[] $set2
     */
    private function equals(array $set1, array $set2): bool
    {
        return array_diff($set1, $set2) == array_diff($set2, $set1);
    }
}
