<?php

declare(strict_types=1);

namespace Phpml\Association;

use Phpml\Helper\Predictable;
use Phpml\Helper\Trainable;

class Apriori implements Associator
{
    use Trainable, Predictable;

    const ARRAY_KEY_ANTECEDENT = 'antecedent';

    const ARRAY_KEY_CONFIDENCE = 'confidence';

    const ARRAY_KEY_CONSEQUENT = 'consequent';

    const ARRAY_KEY_SUPPORT = 'support';

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
    private $large;

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
    private $rules;

    /**
     * Apriori constructor.
     *
     * @param float $support
     * @param float $confidence
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
    public function getRules() : array
    {
        if (!$this->large) {
            $this->large = $this->apriori();
        }

        if ($this->rules) {
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
    public function apriori() : array
    {
        $L = [];
        $L[1] = $this->items();
        $L[1] = $this->frequent($L[1]);

        for ($k = 2; !empty($L[$k - 1]); ++$k) {
            $L[$k] = $this->candidates($L[$k - 1]);
            $L[$k] = $this->frequent($L[$k]);
        }

        return $L;
    }

    /**
     * @param mixed[] $sample
     *
     * @return mixed[][]
     */
    protected function predictSample(array $sample) : array
    {
        $predicts = array_values(array_filter($this->getRules(), function ($rule) use ($sample) {
            return $this->equals($rule[self::ARRAY_KEY_ANTECEDENT], $sample);
        }));

        return array_map(function ($rule) {
            return $rule[self::ARRAY_KEY_CONSEQUENT];
        }, $predicts);
    }

    /**
     * Generate rules for each k-length frequent item set.
     */
    private function generateAllRules()
    {
        for ($k = 2; !empty($this->large[$k]); ++$k) {
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
    private function generateRules(array $frequent)
    {
        foreach ($this->antecedents($frequent) as $antecedent) {
            if ($this->confidence <= ($confidence = $this->confidence($frequent, $antecedent))) {
                $consequent = array_values(array_diff($frequent, $antecedent));
                $this->rules[] = [
                    self::ARRAY_KEY_ANTECEDENT => $antecedent,
                    self::ARRAY_KEY_CONSEQUENT => $consequent,
                    self::ARRAY_KEY_SUPPORT => $this->support($consequent),
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
    private function powerSet(array $sample) : array
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
    private function antecedents(array $sample) : array
    {
        $cardinality = count($sample);
        $antecedents = $this->powerSet($sample);

        return array_filter($antecedents, function ($antecedent) use ($cardinality) {
            return (count($antecedent) != $cardinality) && ($antecedent != []);
        });
    }

    /**
     * Calculates frequent k = 1 item sets.
     *
     * @return mixed[][]
     */
    private function items() : array
    {
        $items = [];

        foreach ($this->samples as $sample) {
            foreach ($sample as $item) {
                if (!in_array($item, $items, true)) {
                    $items[] = $item;
                }
            }
        }

        return array_map(function ($entry) {
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
    private function frequent(array $samples) : array
    {
        return array_filter($samples, function ($entry) {
            return $this->support($entry) >= $this->support;
        });
    }

    /**
     * Calculates frequent k item sets, where count($samples) == $k - 1.
     *
     * @param mixed[][] $samples
     *
     * @return mixed[][]
     */
    private function candidates(array $samples) : array
    {
        $candidates = [];

        foreach ($samples as $p) {
            foreach ($samples as $q) {
                if (count(array_merge(array_diff($p, $q), array_diff($q, $p))) != 2) {
                    continue;
                }

                $candidate = array_unique(array_merge($p, $q));

                if ($this->contains($candidates, $candidate)) {
                    continue;
                }

                foreach ((array) $this->samples as $sample) {
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
     *
     * @return float
     */
    private function confidence(array $set, array $subset) : float
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
     *
     * @return float
     */
    private function support(array $sample) : float
    {
        return $this->frequency($sample) / count($this->samples);
    }

    /**
     * Counts occurrences of $sample as subset in data pool.
     *
     * @see \Phpml\Association\Apriori::samples
     *
     * @param mixed[] $sample
     *
     * @return int
     */
    private function frequency(array $sample) : int
    {
        return count(array_filter($this->samples, function ($entry) use ($sample) {
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
     *
     * @return bool
     */
    private function contains(array $system, array $set) : bool
    {
        return (bool) array_filter($system, function ($entry) use ($set) {
            return $this->equals($entry, $set);
        });
    }

    /**
     * Returns true if subset is a (proper) subset of set by its items string representation.
     *
     * @param mixed[] $set
     * @param mixed[] $subset
     *
     * @return bool
     */
    private function subset(array $set, array $subset) : bool
    {
        return !array_diff($subset, array_intersect($subset, $set));
    }

    /**
     * Returns true if string representation of items does not differ.
     *
     * @param mixed[] $set1
     * @param mixed[] $set2
     *
     * @return bool
     */
    private function equals(array $set1, array $set2) : bool
    {
        return array_diff($set1, $set2) == array_diff($set2, $set1);
    }
}
