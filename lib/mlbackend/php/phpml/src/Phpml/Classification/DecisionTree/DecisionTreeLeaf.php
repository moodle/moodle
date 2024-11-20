<?php

declare(strict_types=1);

namespace Phpml\Classification\DecisionTree;

use Phpml\Math\Comparison;

class DecisionTreeLeaf
{
    /**
     * @var string|int
     */
    public $value;

    /**
     * @var float
     */
    public $numericValue;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var int
     */
    public $columnIndex;

    /**
     * @var DecisionTreeLeaf|null
     */
    public $leftLeaf;

    /**
     * @var DecisionTreeLeaf|null
     */
    public $rightLeaf;

    /**
     * @var array
     */
    public $records = [];

    /**
     * Class value represented by the leaf, this value is non-empty
     * only for terminal leaves
     *
     * @var string
     */
    public $classValue = '';

    /**
     * @var bool
     */
    public $isTerminal = false;

    /**
     * @var bool
     */
    public $isContinuous = false;

    /**
     * @var float
     */
    public $giniIndex = 0;

    /**
     * @var int
     */
    public $level = 0;

    /**
     * HTML representation of the tree without column names
     */
    public function __toString(): string
    {
        return $this->getHTML();
    }

    public function evaluate(array $record): bool
    {
        $recordField = $record[$this->columnIndex];

        if ($this->isContinuous) {
            return Comparison::compare((string) $recordField, $this->numericValue, $this->operator);
        }

        return $recordField == $this->value;
    }

    /**
     * Returns Mean Decrease Impurity (MDI) in the node.
     * For terminal nodes, this value is equal to 0
     */
    public function getNodeImpurityDecrease(int $parentRecordCount): float
    {
        if ($this->isTerminal) {
            return 0.0;
        }

        $nodeSampleCount = (float) count($this->records);
        $iT = $this->giniIndex;

        if ($this->leftLeaf !== null) {
            $pL = count($this->leftLeaf->records) / $nodeSampleCount;
            $iT -= $pL * $this->leftLeaf->giniIndex;
        }

        if ($this->rightLeaf !== null) {
            $pR = count($this->rightLeaf->records) / $nodeSampleCount;
            $iT -= $pR * $this->rightLeaf->giniIndex;
        }

        return $iT * $nodeSampleCount / $parentRecordCount;
    }

    /**
     * Returns HTML representation of the node including children nodes
     */
    public function getHTML(?array $columnNames = null): string
    {
        if ($this->isTerminal) {
            $value = "<b>{$this}->classValue</b>";
        } else {
            $value = $this->value;
            if ($columnNames !== null) {
                $col = $columnNames[$this->columnIndex];
            } else {
                $col = "col_$this->columnIndex";
            }

            if ((bool) preg_match('/^[<>=]{1,2}/', (string) $value) === false) {
                $value = "={$value}";
            }

            $value = "<b>{$col} {$value}</b><br>Gini: ".number_format($this->giniIndex, 2);
        }

        $str = "<table ><tr><td colspan=3 align=center style='border:1px solid;'>{$value}</td></tr>";

        if ($this->leftLeaf !== null || $this->rightLeaf !== null) {
            $str .= '<tr>';
            if ($this->leftLeaf !== null) {
                $str .= '<td valign=top><b>| Yes</b><br>'.$this->leftLeaf->getHTML($columnNames).'</td>';
            } else {
                $str .= '<td></td>';
            }

            $str .= '<td>&nbsp;</td>';
            if ($this->rightLeaf !== null) {
                $str .= '<td valign=top align=right><b>No |</b><br>'.$this->rightLeaf->getHTML($columnNames).'</td>';
            } else {
                $str .= '<td></td>';
            }

            $str .= '</tr>';
        }

        $str .= '</table>';

        return $str;
    }
}
