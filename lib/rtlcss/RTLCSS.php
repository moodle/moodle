<?php
/**
 * RTLCSS.
 *
 * @package   MoodleHQ\RTLCSS
 * @copyright 2016 Frédéric Massart - FMCorz.net
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace MoodleHQ\RTLCSS;

use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\RuleSet;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\PrimitiveValue;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Size;
use Sabberworm\CSS\Value\ValueList;

/**
 * RTLCSS Class.
 *
 * @package   MoodleHQ\RTLCSS
 * @copyright 2016 Frédéric Massart - FMCorz.net
 * @license   https://opensource.org/licenses/MIT MIT
 */
class RTLCSS {

    protected $tree;
    protected $shouldAddCss = [];
    protected $shouldIgnore = false;
    protected $shouldRemove = false;

    public function __construct(Document $tree) {
        $this->tree = $tree;
    }

    protected function compare($what, $to, $ignoreCase) {
        if ($ignoreCase) {
            return strtolower($what) === strtolower($to);
        }
        return $what === $to;
    }

    protected function complement($value) {
        if ($value instanceof Size) {
            $value->setSize(100 - $value->getSize());

        } else if ($value instanceof CSSFunction) {
            $arguments = implode($value->getListSeparator(), $value->getArguments());
            $arguments = "100% - ($arguments)";
            $value->setListComponents([$arguments]);
        }
    }

    public function flip() {
        $this->processBlock($this->tree);
        return $this->tree;
    }

    protected function negate($value) {
        if ($value instanceof ValueList) {
            foreach ($value->getListComponents() as $part) {
                $this->negate($part);
            }
        } else if ($value instanceof Size) {
            if ($value->getSize() != 0) {
                $value->setSize(-$value->getSize());
            }
        }
    }

    protected function parseComments(array $comments) {
        $startRule = '^(\s|\*)*!?rtl:';
        foreach ($comments as $comment) {
            $content = $comment->getComment();
            if (preg_match('/' . $startRule . 'ignore/', $content)) {
                $this->shouldIgnore = 1;
            } else if (preg_match('/' . $startRule . 'begin:ignore/', $content)) {
                $this->shouldIgnore = true;
            } else if (preg_match('/' . $startRule . 'end:ignore/', $content)) {
                $this->shouldIgnore = false;
            } else if (preg_match('/' . $startRule . 'remove/', $content)) {
                $this->shouldRemove = 1;
            } else if (preg_match('/' . $startRule . 'begin:remove/', $content)) {
                $this->shouldRemove = true;
            } else if (preg_match('/' . $startRule . 'end:remove/', $content)) {
                $this->shouldRemove = false;
            } else if (preg_match('/' . $startRule . 'raw:/', $content)) {
                $this->shouldAddCss[] = preg_replace('/' . $startRule . 'raw:/', '', $content);
            }
        }
    }

    protected function processBackground(Rule $rule) {
        $value = $rule->getValue();

        // TODO Fix upstream library as it does not parse this well, commas don't take precedence.
        // There can be multiple sets of properties per rule.
        $hasItems = false;
        $items = [$value];
        if ($value instanceof RuleValueList && $value->getListSeparator() == ',') {
            $hasItems = true;
            $items = $value->getListComponents();
        }

        // Foreach set.
        foreach ($items as $itemKey => $item) {

            // There can be multiple values in the same set.
            $hasValues = false;
            $parts = [$item];
            if ($item instanceof RuleValueList) {
                $hasValues = true;
                $parts = $value->getListComponents();
            }

            $requiresPositionalArgument = false;
            $hasPositionalArgument = false;
            foreach ($parts as $key => $part) {
                $part = $parts[$key];

                if (!is_object($part)) {
                    $flipped = $this->swapLeftRight($part);

                    // Positional arguments can have a size following.
                    $hasPositionalArgument = $parts[$key] != $flipped;
                    $requiresPositionalArgument = true;

                    $parts[$key] = $flipped;
                    continue;

                } else if ($part instanceof CSSFunction && strpos($part->getName(), 'gradient') !== false) {
                    // TODO Fix this.

                } else if ($part instanceof Size && ($part->getUnit() === '%' || !$part->getUnit())) {

                    // Is this a value we're interested in?
                    if (!$requiresPositionalArgument || $hasPositionalArgument) {
                        $this->complement($part);
                        $part->setUnit('%');
                        // We only need to change one value.
                        break;
                    }

                }

                $hasPositionalArgument = false;
            }

            if ($hasValues) {
                $item->setListComponents($parts);
            } else {
                $items[$itemKey] = $parts[$key];
            }
        }

        if ($hasItems) {
            $value->setListComponents($items);
        } else {
            $rule->setValue($items[0]);
        }
    }

    protected function processBlock($block) {
        $contents = [];

        foreach ($block->getContents() as $node) {
            $this->parseComments($node->getComments());

            if ($toAdd = $this->shouldAddCss()) {
                foreach ($toAdd as $add) {
                    $parser = new Parser($add);
                    $contents[] = $parser->parse();
                }
            }

            if ($this->shouldRemoveNext()) {
                continue;

            } else if (!$this->shouldIgnoreNext()) {
                if ($node instanceof CSSList) {
                    $this->processBlock($node);
                }
                if ($node instanceof RuleSet) {
                    $this->processDeclaration($node);
                }
            }

            $contents[] = $node;
        }

        $block->setContents($contents);
    }

    protected function processDeclaration($node) {
        $rules = [];

        foreach ($node->getRules() as $key => $rule) {
            $this->parseComments($rule->getComments());

            if ($toAdd = $this->shouldAddCss()) {
                foreach ($toAdd as $add) {
                    $parser = new Parser('.wrapper{' . $add . '}');
                    $tree = $parser->parse();
                    $contents = $tree->getContents();
                    foreach ($contents[0]->getRules() as $newRule) {
                        $rules[] = $newRule;
                    }
                }
            }

            if ($this->shouldRemoveNext()) {
                continue;

            } else if (!$this->shouldIgnoreNext()) {
                $this->processRule($rule);
            }

            $rules[] = $rule;
        }

        $node->setRules($rules);
    }

    protected function processRule($rule) {
        $property = $rule->getRule();
        $value = $rule->getValue();

        if (preg_match('/direction$/im', $property)) {
            $rule->setValue($this->swapLtrRtl($value));

        } else if (preg_match('/left/im', $property)) {
            $rule->setRule(str_replace('left', 'right', $property));

        } else if (preg_match('/right/im', $property)) {
            $rule->setRule(str_replace('right', 'left', $property));

        } else if (preg_match('/transition(-property)?$/i', $property)) {
            $rule->setValue($this->swapLeftRight($value));

        } else if (preg_match('/float|clear|text-align/i', $property)) {
            $rule->setValue($this->swapLeftRight($value));

        } else if (preg_match('/^(margin|padding|border-(color|style|width))$/i', $property)) {

            if ($value instanceof RuleValueList) {
                $values = $value->getListComponents();
                $count = count($values);
                if ($count == 4) {
                    $right = $values[3];
                    $values[3] = $values[1];
                    $values[1] = $right;
                }
                $value->setListComponents($values);
            }

        } else if (preg_match('/border-radius/i', $property)) {
            if ($value instanceof RuleValueList) {

                // Border radius can contain two lists separated by a slash.
                $groups = $value->getListComponents();
                if ($value->getListSeparator() !== '/') {
                    $groups = [$value];
                }
                foreach ($groups as $group) {
                    $values = $group->getListComponents();
                    switch (count($values)) {
                        case 2:
                            $group->setListComponents(array_reverse($values));
                            break;
                        case 3:
                            $group->setListComponents([$values[1], $values[0], $values[1], $values[2]]);
                            break;
                        case 4:
                            $group->setListComponents([$values[1], $values[0], $values[3], $values[2]]);
                            break;
                    }
                }
            }

        } else if (preg_match('/shadow/i', $property)) {
            // TODO Fix upstream, each shadow should be in a RuleValueList.
            if ($value instanceof RuleValueList) {
                // negate($value->getListComponents()[0]);
            }

        } else if (preg_match('/transform-origin/i', $property)) {
            $this->processTransformOrigin($rule);

        } else if (preg_match('/^(?!text\-).*?transform$/i', $property)) {
            // TODO Parse function parameters first.

        } else if (preg_match('/background(-position(-x)?|-image)?$/i', $property)) {
            $this->processBackground($rule);

        } else if (preg_match('/cursor/i', $property)) {
            $hasList = false;

            $parts = [$value];
            if ($value instanceof RuleValueList) {
                $hastList = true;
                $parts = $value->getListComponents();
            }

            foreach ($parts as $key => $part) {
                if (!is_object($part)) {
                    $parts[$key] = preg_replace_callback('/\b(ne|nw|se|sw|nesw|nwse)-resize/', function($matches) {
                        return str_replace($matches[1], str_replace(['e', 'w', '*'], ['*', 'e', 'w'], $matches[1]), $matches[0]);
                    }, $part);
                }
            }

            if ($hasList) {
                $value->setListComponents($parts);
            } else {
                $rule->setValue($parts[0]);
            }

        }

    }

    protected function processTransformOrigin(Rule $rule) {
        $value = $rule->getValue();
        $foundLeftOrRight = false;

        // Search for left or right.
        $parts = [$value];
        if ($value instanceof RuleValueList) {
            $parts = $value->getListComponents();
            $isInList = true;
        }
        foreach ($parts as $key => $part) {
            if (!is_object($part) && preg_match('/left|right/i', $part)) {
                $foundLeftOrRight = true;
                $parts[$key] = $this->swapLeftRight($part);
            }
        }

        if ($foundLeftOrRight) {
            // We need to reconstruct the value because left/right are not represented by an object.
            $list = new RuleValueList(' ');
            $list->setListComponents($parts);
            $rule->setValue($list);

        } else {

            $value = $parts[0];
            // The first value may be referencing top or bottom (y instead of x).
            if (!is_object($value) && preg_match('/top|bottom/i', $value)) {
                $value = $parts[1];
            }

            // Flip the value.
            if ($value instanceof Size) {

                if ($value->getSize() == 0) {
                    $value->setSize(100);
                    $value->setUnit('%');

                } else if ($value->getUnit() === '%') {
                    $this->complement($value);
                }

            } else if ($value instanceof CSSFunction && strpos($value->getName(), 'calc') !== false) {
                // TODO Fix upstream calc parsing.
                $this->complement($value);
            }
        }
    }

    protected function shouldAddCss() {
        if (!empty($this->shouldAddCss)) {
            $css = $this->shouldAddCss;
            $this->shouldAddCss = [];
            return $css;
        }
        return [];
    }

    protected function shouldIgnoreNext() {
        if ($this->shouldIgnore) {
            if (is_int($this->shouldIgnore)) {
                $this->shouldIgnore--;
            }
            return true;
        }
        return false;
    }

    protected function shouldRemoveNext() {
        if ($this->shouldRemove) {
            if (is_int($this->shouldRemove)) {
                $this->shouldRemove--;
            }
            return true;
        }
        return false;
    }

    protected function swap($value, $a, $b, $options = ['scope' => '*', 'ignoreCase' => true]) {
        $expr = preg_quote($a) . '|' . preg_quote($b);
        if (!empty($options['greedy'])) {
            $expr = '\\b(' . $expr . ')\\b';
        }
        $flags = !empty($options['ignoreCase']) ? 'im' : 'm';
        $expr = "/$expr/$flags";
        return preg_replace_callback($expr, function($matches) use ($a, $b, $options) {
            return $this->compare($matches[0], $a, !empty($options['ignoreCase'])) ? $b : $a;
        }, $value);
    }

    protected function swapLeftRight($value) {
        return $this->swap($value, 'left', 'right');
    }

    protected function swapLtrRtl($value) {
        return $this->swap($value, 'ltr', 'rtl');
    }

}
