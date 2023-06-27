<?php

namespace Gettext\Languages;

use Exception;

/**
 * Main class to convert the plural rules of a language from CLDR to gettext.
 */
class Language
{
    /**
     * The language ID.
     *
     * @var string
     */
    public $id;

    /**
     * The language name.
     *
     * @var string
     */
    public $name;

    /**
     * If this language is deprecated: the gettext code of the new language.
     *
     * @var string|null
     */
    public $supersededBy;

    /**
     * The script name.
     *
     * @var string|null
     */
    public $script;

    /**
     * The territory name.
     *
     * @var string|null
     */
    public $territory;

    /**
     * The name of the base language.
     *
     * @var string|null
     */
    public $baseLanguage;

    /**
     * The list of categories.
     *
     * @var \Gettext\Languages\Category[]
     */
    public $categories;

    /**
     * The gettext formula to decide which category should be applied.
     *
     * @var string
     */
    public $formula;

    /**
     * Initialize the instance and parse the language code.
     *
     * @param array $info The result of CldrData::getLanguageInfo()
     *
     * @throws \Exception throws an Exception if $fullId is not valid
     */
    private function __construct($info)
    {
        $this->id = $info['id'];
        $this->name = $info['name'];
        $this->supersededBy = isset($info['supersededBy']) ? $info['supersededBy'] : null;
        $this->script = isset($info['script']) ? $info['script'] : null;
        $this->territory = isset($info['territory']) ? $info['territory'] : null;
        $this->baseLanguage = isset($info['baseLanguage']) ? $info['baseLanguage'] : null;
        // Let's build the category list
        $this->categories = array();
        foreach ($info['categories'] as $cldrCategoryId => $cldrFormulaAndExamples) {
            $category = new Category($cldrCategoryId, $cldrFormulaAndExamples);
            foreach ($this->categories as $c) {
                if ($category->id === $c->id) {
                    throw new Exception("The category '{$category->id}' is specified more than once");
                }
            }
            $this->categories[] = $category;
        }
        if (empty($this->categories)) {
            throw new Exception("The language '{$info['id']}' does not have any plural category");
        }
        // Let's sort the categories from 'zero' to 'other'
        usort($this->categories, function (Category $category1, Category $category2) {
            return array_search($category1->id, CldrData::$categories) - array_search($category2->id, CldrData::$categories);
        });
        // The 'other' category should always be there
        if ($this->categories[count($this->categories) - 1]->id !== CldrData::OTHER_CATEGORY) {
            throw new Exception("The language '{$info['id']}' does not have the '" . CldrData::OTHER_CATEGORY . "' plural category");
        }
        $this->checkAlwaysTrueCategories();
        $this->checkAlwaysFalseCategories();
        $this->checkAllCategoriesWithExamples();
        $this->formula = $this->buildFormula();
    }

    /**
     * Return a list of all languages available.
     *
     * @throws \Exception
     *
     * @return \Gettext\Languages\Language[]
     */
    public static function getAll()
    {
        $result = array();
        foreach (array_keys(CldrData::getLanguageNames()) as $cldrLanguageId) {
            $result[] = new self(CldrData::getLanguageInfo($cldrLanguageId));
        }

        return $result;
    }

    /**
     * Return a Language instance given the language id.
     *
     * @param string $id
     *
     * @return \Gettext\Languages\Language|null
     */
    public static function getById($id)
    {
        $result = null;
        $info = CldrData::getLanguageInfo($id);
        if (isset($info)) {
            $result = new self($info);
        }

        return $result;
    }

    /**
     * Returns a clone of this instance with all the strings to US-ASCII.
     *
     * @return \Gettext\Languages\Language
     */
    public function getUSAsciiClone()
    {
        $clone = clone $this;
        self::asciifier($clone->name);
        self::asciifier($clone->formula);
        $clone->categories = array();
        foreach ($this->categories as $category) {
            $categoryClone = clone $category;
            self::asciifier($categoryClone->examples);
            $clone->categories[] = $categoryClone;
        }

        return $clone;
    }

    /**
     * Build the formula starting from the currently defined categories.
     *
     * @param bool $withoutParenthesis TRUE to build a formula in standard gettext format, FALSE (default) to build a PHP-compatible formula
     *
     * @return string
     */
    public function buildFormula($withoutParenthesis = false)
    {
        $numCategories = count($this->categories);
        switch ($numCategories) {
            case 1:
                // Just one category
                return '0';
            case 2:
                return self::reduceFormula(self::reverseFormula($this->categories[0]->formula));
            default:
                $formula = (string) ($numCategories - 1);
                for ($i = $numCategories - 2; $i >= 0; $i--) {
                    $f = self::reduceFormula($this->categories[$i]->formula);
                    if (!$withoutParenthesis && !preg_match('/^\([^()]+\)$/', $f)) {
                        $f = "({$f})";
                    }
                    $formula = "{$f} ? {$i} : {$formula}";
                    if (!$withoutParenthesis && $i > 0) {
                        $formula = "({$formula})";
                    }
                }

                return $formula;
        }
    }

    /**
     * Let's look for categories that will always occur.
     * This because with decimals (CLDR) we may have more cases, with integers (gettext) we have just one case.
     * If we found that (single) category we reduce the categories to that one only.
     *
     * @throws \Exception
     */
    private function checkAlwaysTrueCategories()
    {
        $alwaysTrueCategory = null;
        foreach ($this->categories as $category) {
            if ($category->formula === true) {
                if (!isset($category->examples)) {
                    throw new Exception("The category '{$category->id}' should always occur, but it does not have examples (so for CLDR it will never occur for integers!)");
                }
                $alwaysTrueCategory = $category;
                break;
            }
        }
        if (isset($alwaysTrueCategory)) {
            foreach ($this->categories as $category) {
                if (($category !== $alwaysTrueCategory) && isset($category->examples)) {
                    throw new Exception("The category '{$category->id}' should never occur, but it has some examples (so for CLDR it will occur!)");
                }
            }
            $alwaysTrueCategory->id = CldrData::OTHER_CATEGORY;
            $alwaysTrueCategory->formula = null;
            $this->categories = array($alwaysTrueCategory);
        }
    }

    /**
     * Let's look for categories that will never occur.
     * This because with decimals (CLDR) we may have more cases, with integers (gettext) we have some less cases.
     * If we found those categories we strip them out.
     *
     * @throws \Exception
     */
    private function checkAlwaysFalseCategories()
    {
        $filtered = array();
        foreach ($this->categories as $category) {
            if ($category->formula === false) {
                if (isset($category->examples)) {
                    throw new Exception("The category '{$category->id}' should never occur, but it has examples (so for CLDR it may occur!)");
                }
            } else {
                $filtered[] = $category;
            }
        }
        $this->categories = $filtered;
    }

    /**
     * Let's look for categories that don't have examples.
     * This because with decimals (CLDR) we may have more cases, with integers (gettext) we have some less cases.
     * If we found those categories, we check that they never occur and we strip them out.
     *
     * @throws \Exception
     */
    private function checkAllCategoriesWithExamples()
    {
        $allCategoriesIds = array();
        $goodCategories = array();
        $badCategories = array();
        $badCategoriesIds = array();
        foreach ($this->categories as $category) {
            $allCategoriesIds[] = $category->id;
            if (isset($category->examples)) {
                $goodCategories[] = $category;
            } else {
                $badCategories[] = $category;
                $badCategoriesIds[] = $category->id;
            }
        }
        if (empty($badCategories)) {
            return;
        }
        $removeCategoriesWithoutExamples = false;
        switch (implode(',', $badCategoriesIds) . '@' . implode(',', $allCategoriesIds)) {
            case CldrData::OTHER_CATEGORY . '@one,few,many,' . CldrData::OTHER_CATEGORY:
                switch ($this->buildFormula()) {
                    case '(n % 10 == 1 && n % 100 != 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : ((n % 10 == 0 || n % 10 >= 5 && n % 10 <= 9 || n % 100 >= 11 && n % 100 <= 14) ? 2 : 3))':
                        // Numbers ending with 0                 => case 2 ('many')
                        // Numbers ending with 1 but not with 11 => case 0 ('one')
                        // Numbers ending with 11                => case 2 ('many')
                        // Numbers ending with 2 but not with 12 => case 1 ('few')
                        // Numbers ending with 12                => case 2 ('many')
                        // Numbers ending with 3 but not with 13 => case 1 ('few')
                        // Numbers ending with 13                => case 2 ('many')
                        // Numbers ending with 4 but not with 14 => case 1 ('few')
                        // Numbers ending with 14                => case 2 ('many')
                        // Numbers ending with 5                 => case 2 ('many')
                        // Numbers ending with 6                 => case 2 ('many')
                        // Numbers ending with 7                 => case 2 ('many')
                        // Numbers ending with 8                 => case 2 ('many')
                        // Numbers ending with 9                 => case 2 ('many')
                        // => the 'other' case never occurs: use 'other' for 'many'
                        $removeCategoriesWithoutExamples = true;
                        break;
                    case '(n == 1) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : ((n != 1 && (n % 10 == 0 || n % 10 == 1) || n % 10 >= 5 && n % 10 <= 9 || n % 100 >= 12 && n % 100 <= 14) ? 2 : 3))':
                        // Numbers ending with 0                  => case 2 ('many')
                        // Numbers ending with 1 but not number 1 => case 2 ('many')
                        // Number 1                               => case 0 ('one')
                        // Numbers ending with 2 but not with 12  => case 1 ('few')
                        // Numbers ending with 12                 => case 2 ('many')
                        // Numbers ending with 3 but not with 13  => case 1 ('few')
                        // Numbers ending with 13                 => case 2 ('many')
                        // Numbers ending with 4 but not with 14  => case 1 ('few')
                        // Numbers ending with 14                 => case 2 ('many')
                        // Numbers ending with 5                  => case 2 ('many')
                        // Numbers ending with 6                  => case 2 ('many')
                        // Numbers ending with 7                  => case 2 ('many')
                        // Numbers ending with 8                  => case 2 ('many')
                        // Numbers ending with 9                  => case 2 ('many')
                        // => the 'other' case never occurs: use 'other' for 'many'
                        $removeCategoriesWithoutExamples = true;
                        break;
                }
        }
        if (!$removeCategoriesWithoutExamples) {
            throw new Exception("Unhandled case of plural categories without examples '" . implode(', ', $badCategoriesIds) . "' out of '" . implode(', ', $allCategoriesIds) . "'");
        }
        if ($badCategories[count($badCategories) - 1]->id === CldrData::OTHER_CATEGORY) {
            // We're removing the 'other' cagory: let's change the last good category to 'other'
            $lastGood = $goodCategories[count($goodCategories) - 1];
            $lastGood->id = CldrData::OTHER_CATEGORY;
            $lastGood->formula = null;
        }
        $this->categories = $goodCategories;
    }

    /**
     * Reverse a formula.
     *
     * @param string $formula
     *
     * @throws \Exception
     *
     * @return string
     */
    private static function reverseFormula($formula)
    {
        if (preg_match('/^n( % \d+)? == \d+(\.\.\d+|,\d+)*?$/', $formula)) {
            return str_replace(' == ', ' != ', $formula);
        }
        if (preg_match('/^n( % \d+)? != \d+(\.\.\d+|,\d+)*?$/', $formula)) {
            return str_replace(' != ', ' == ', $formula);
        }
        if (preg_match('/^\(?n == \d+ \|\| n == \d+\)?$/', $formula)) {
            return trim(str_replace(array(' == ', ' || '), array(' != ', ' && '), $formula), '()');
        }
        $m = null;
        if (preg_match('/^(n(?: % \d+)?) == (\d+) && (n(?: % \d+)?) != (\d+)$/', $formula, $m)) {
            return "{$m[1]} != {$m[2]} || {$m[3]} == {$m[4]}";
        }
        switch ($formula) {
            case '(n == 1 || n == 2 || n == 3) || n % 10 != 4 && n % 10 != 6 && n % 10 != 9':
                return 'n != 1 && n != 2 && n != 3 && (n % 10 == 4 || n % 10 == 6 || n % 10 == 9)';
            case '(n == 0 || n == 1) || n >= 11 && n <= 99':
                return 'n >= 2 && (n < 11 || n > 99)';
        }
        throw new Exception("Unable to reverse the formula '{$formula}'");
    }

    /**
     * Reduce some excessively complex formulas.
     *
     * @param string $formula
     *
     * @return string
     */
    private static function reduceFormula($formula)
    {
        $map = array(
            'n != 0 && n != 1' => 'n > 1',
            '(n == 0 || n == 1) && n != 0' => 'n == 1',
        );

        return isset($map[$formula]) ? $map[$formula] : $formula;
    }

    /**
     * Take one variable and, if it's a string, we transliterate it to US-ASCII.
     *
     * @param mixed $value the variable to work on
     *
     * @throws \Exception
     */
    private static function asciifier(&$value)
    {
        if (is_string($value) && $value !== '') {
            // Avoid converting from 'Ÿ' to '"Y', let's prefer 'Y'
            $value = strtr($value, array(
                'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
                'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
                'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
                'Ñ' => 'N',
                'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
                'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
                'Ÿ' => 'Y', 'Ý' => 'Y',
                'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
                'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
                'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
                'ý' => 'y', 'ÿ' => 'y',
                '…' => '...',
                'ʼ' => "'", '’' => "'",
            ));
        }
    }
}
