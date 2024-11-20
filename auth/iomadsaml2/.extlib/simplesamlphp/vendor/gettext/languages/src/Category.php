<?php

namespace Gettext\Languages;

use Exception;

/**
 * A helper class that handles a single category rules (eg 'zero', 'one', ...) and its formula and examples.
 */
class Category
{
    /**
     * The category identifier (eg 'zero', 'one', ..., 'other').
     *
     * @var string
     */
    public $id;

    /**
     * The gettext formula that identifies this category (null if and only if the category is 'other').
     *
     * @var string|null
     */
    public $formula;

    /**
     * The CLDR representation of some exemplar numeric ranges that satisfy this category.
     *
     * @var string|null
     */
    public $examples;

    /**
     * Initialize the instance and parse the formula.
     *
     * @param string $cldrCategoryId the CLDR category identifier (eg 'pluralRule-count-one')
     * @param string $cldrFormulaAndExamples the CLDR formula and examples (eg 'i = 1 and v = 0 @integer 1')
     *
     * @throws \Exception
     */
    public function __construct($cldrCategoryId, $cldrFormulaAndExamples)
    {
        $matches = array();
        if (!preg_match('/^pluralRule-count-(.+)$/', $cldrCategoryId, $matches)) {
            throw new Exception("Invalid CLDR category: '{$cldrCategoryId}'");
        }
        if (!in_array($matches[1], CldrData::$categories)) {
            throw new Exception("Invalid CLDR category: '{$cldrCategoryId}'");
        }
        $this->id = $matches[1];
        $cldrFormulaAndExamplesNormalized = trim(preg_replace('/\s+/', ' ', $cldrFormulaAndExamples));
        if (!preg_match('/^([^@]*)(?:@integer([^@]+))?(?:@decimal(?:[^@]+))?$/', $cldrFormulaAndExamplesNormalized, $matches)) {
            throw new Exception("Invalid CLDR category rule: {$cldrFormulaAndExamples}");
        }
        $cldrFormula = trim($matches[1]);
        $s = isset($matches[2]) ? trim($matches[2]) : '';
        $this->examples = ($s === '') ? null : $s;
        switch ($this->id) {
            case CldrData::OTHER_CATEGORY:
                if ($cldrFormula !== '') {
                    throw new Exception("The '" . CldrData::OTHER_CATEGORY . "' category should not have any formula, but it has '{$cldrFormula}'");
                }
                $this->formula = null;
                break;
            default:
                if ($cldrFormula === '') {
                    throw new Exception("The '{$this->id}' category does not have a formula");
                }
                $this->formula = FormulaConverter::convertFormula($cldrFormula);
                break;
        }
    }

    /**
     * Return a list of numbers corresponding to the $examples value.
     *
     * @throws \Exception throws an Exception if we weren't able to expand the examples
     *
     * @return int[]
     */
    public function getExampleIntegers()
    {
        return self::expandExamples($this->examples);
    }

    /**
     * Expand a list of examples as defined by CLDR.
     *
     * @param string $examples A string like '1, 2, 5...7, …'.
     *
     * @throws \Exception throws an Exception if we weren't able to expand $examples
     *
     * @return int[]
     */
    public static function expandExamples($examples)
    {
        $result = array();
        $m = null;
        if (substr($examples, -strlen(', …')) === ', …') {
            $examples = substr($examples, 0, strlen($examples) - strlen(', …'));
        }
        foreach (explode(',', str_replace(' ', '', $examples)) as $range) {
            if (preg_match('/^(?<num>\d+)((c|e)(?<exp>\d+))?$/', $range, $m)) {
                $result[] = (int) (isset($m['exp']) ? ($m['num'] . str_repeat('0', (int) $m['exp'])) : $range);
            } elseif (preg_match('/^(\d+)~(\d+)$/', $range, $m)) {
                $from = (int) $m[1];
                $to = (int) $m[2];
                $delta = $to - $from;
                $step = (int) max(1, $delta / 100);
                for ($i = $from; $i < $to; $i += $step) {
                    $result[] = $i;
                }
                $result[] = $to;
            } else {
                throw new Exception("Unhandled test range '{$range}' in '{$examples}'");
            }
        }
        if (empty($result)) {
            throw new Exception("No test numbers from '{$examples}'");
        }

        return $result;
    }
}
