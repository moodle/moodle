<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Autoprefixer.
 *
 * This autoprefixer has been developed to satisfy the basic needs of the
 * theme IOMAD Boost when working with Bootstrap 4 alpha. We do not recommend
 * that this tool is shared, nor used outside of this theme.
 *
 * @package    theme_iomadboost
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @based on theme_boost by Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_iomadboost;
defined('MOODLE_INTERNAL') || die();

use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Property\AtRule;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\AtRuleSet;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\RuleSet\RuleSet;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\PrimitiveValue;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Size;
use Sabberworm\CSS\Value\ValueList;


/**
 * Autoprefixer class.
 *
 * Very basic implementation covering simple needs for Bootstrap 4.
 *
 * @package    theme_iomadboost
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autoprefixer {

    /** @var object The CSS tree. */
    protected $tree;

    /** @var string Pseudo classes regex. */
    protected $pseudosregex;

    /** @var array At rules prefixes. */
    protected static $atrules = [
        'keyframes' => ['-webkit-', '-o-']
    ];

    /** @var array Pseudo classes prefixes. */
    protected static $pseudos = [
        '::placeholder' => ['::-webkit-input-placeholder', '::-moz-placeholder', ':-ms-input-placeholder']
    ];

    /** @var array Rule properties prefixes. */
    protected static $rules = [
        'animation' => ['-webkit-'],
        'appearance' => ['-webkit-', '-moz-'],
        'backface-visibility' => ['-webkit-'],
        'box-sizing' => ['-webkit-'],
        'box-shadow' => ['-webkit-'],
        'background-clip' => ['-webkit-'],
        'background-size' => ['-webkit-'],
        'box-shadow' => ['-webkit-'],
        'column-count' => ['-webkit-', '-moz-'],
        'column-gap' => ['-webkit-', '-moz-'],
        'perspective' => ['-webkit-'],
        'touch-action' => ['-ms-'],
        'transform' => ['-webkit-', '-moz-', '-ms-', '-o-'],
        'transition' => ['-webkit-', '-o-'],
        'transition-timing-function' => ['-webkit-', '-o-'],
        'transition-duration' => ['-webkit-', '-o-'],
        'transition-property' => ['-webkit-', '-o-'],
        'user-select' => ['-webkit-', '-moz-', '-ms-'],
    ];

    /**
     * Constructor.
     *
     * @param Document $tree The CSS tree.
     */
    public function __construct(Document $tree) {
        debugging('theme_iomadboost\autoprefixer() is deprecated. Required prefixes for Bootstrap ' .
            'are now in theme/iomadboost/scss/moodle/prefixes.scss', DEBUG_DEVELOPER);
        $this->tree = $tree;

        $pseudos = array_map(function($pseudo) {
            return '(' . preg_quote($pseudo) . ')';
        }, array_keys(self::$pseudos));
        $this->pseudosregex = '(' . implode('|', $pseudos) . ')';
    }

    /**
     * Manipulate an array of rules to adapt their values.
     *
     * @param array $rules The rules.
     * @return New array of rules.
     */
    protected function manipulateRuleValues(array $rules) {
        $finalrules = [];

        foreach ($rules as $rule) {
            $property = $rule->getRule();
            $value = $rule->getValue();

            if ($property === 'position' && $value === 'sticky') {
                $newrule = clone $rule;
                $newrule->setValue('-webkit-sticky');
                $finalrules[] = $newrule;

            } else if ($property === 'background-image' &&
                    $value instanceof CSSFunction &&
                    $value->getName() === 'linear-gradient') {

                foreach (['-webkit-', '-o-'] as $prefix) {
                    $newfunction = clone $value;
                    $newfunction->setName($prefix . $value->getName());
                    $newrule = clone $rule;
                    $newrule->setValue($newfunction);
                    $finalrules[] = $newrule;
                }
            }

            $finalrules[] = $rule;
        }

        return $finalrules;
    }

    /**
     * Prefix all the things!
     */
    public function prefix() {
        $this->processBlock($this->tree);
    }

    /**
     * Process block.
     *
     * @param object $block A block.
     * @param object $parent The parent of the block.
     */
    protected function processBlock($block) {
        foreach ($block->getContents() as $node) {
            if ($node instanceof AtRule) {

                $name = $node->atRuleName();
                if (isset(self::$atrules[$name])) {
                    foreach (self::$atrules[$name] as $prefix) {
                        $newname = $prefix . $name;
                        $newnode = clone $node;

                        if ($node instanceof KeyFrame) {
                            $newnode->setVendorKeyFrame($newname);
                            $block->insert($newnode, $node);

                        } else {
                            debugging('Unhandled atRule prefixing.', DEBUG_DEVELOPER);
                        }
                    }
                }
            }

            if ($node instanceof CSSList) {
                $this->processBlock($node);

            } else if ($node instanceof RuleSet) {
                $this->processDeclaration($node, $block);
            }
        }
    }

    /**
     * Process declaration.
     *
     * @param object $node The declaration block.
     * @param object $parent The parent.
     */
    protected function processDeclaration($node, $parent) {
        $rules = [];

        foreach ($node->getRules() as $key => $rule) {
            $name = $rule->getRule();
            $seen[$name] = true;

            if (!isset(self::$rules[$name])) {
                $rules[] = $rule;
                continue;
            }

            foreach (self::$rules[$name] as $prefix) {
                $newname = $prefix . $name;
                if (isset($seen[$newname])) {
                    continue;
                }
                $newrule = clone $rule;
                $newrule->setRule($newname);
                $rules[] = $newrule;
            }

            $rules[] = $rule;
        }

        $node->setRules($this->manipulateRuleValues($rules));

        if ($node instanceof DeclarationBlock) {
            $selectors = $node->getSelectors();
            foreach ($selectors as $key => $selector) {

                $matches = [];
                if (preg_match($this->pseudosregex, $selector->getSelector(), $matches)) {

                    $newnode = clone $node;
                    foreach (self::$pseudos[$matches[1]] as $newpseudo) {
                        $newselector = new Selector(str_replace($matches[1], $newpseudo, $selector->getSelector()));
                        $selectors[$key] = $newselector;
                        $newnode = clone $node;
                        $newnode->setSelectors($selectors);
                        $parent->insert($newnode, $node);
                    }

                    // We're only expecting one affected pseudo class per block.
                    break;
                }
            }
        }
    }
}
