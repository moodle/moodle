<?php

declare(strict_types=1);

namespace Phpml\Classification\DecisionTree;

class DecisionTreeLeaf
{
    /**
     * @var string
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
     * @var DecisionTreeLeaf
     */
    public $leftLeaf = null;

    /**
     * @var DecisionTreeLeaf
     */
    public $rightLeaf= null;

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
     * @param array $record
     * @return bool
     */
    public function evaluate($record)
    {
        $recordField = $record[$this->columnIndex];

        if ($this->isContinuous) {
            $op = $this->operator;
            $value= $this->numericValue;
            $recordField = strval($recordField);
            eval("\$result = $recordField $op $value;");
            return $result;
        }
        
        return $recordField == $this->value;
    }

    /**
     * Returns Mean Decrease Impurity (MDI) in the node.
     * For terminal nodes, this value is equal to 0
     *
     * @param int $parentRecordCount
     *
     * @return float
     */
    public function getNodeImpurityDecrease(int $parentRecordCount)
    {
        if ($this->isTerminal) {
            return 0.0;
        }

        $nodeSampleCount = (float)count($this->records);
        $iT = $this->giniIndex;

        if ($this->leftLeaf) {
            $pL = count($this->leftLeaf->records)/$nodeSampleCount;
            $iT -= $pL * $this->leftLeaf->giniIndex;
        }

        if ($this->rightLeaf) {
            $pR = count($this->rightLeaf->records)/$nodeSampleCount;
            $iT -= $pR * $this->rightLeaf->giniIndex;
        }

        return $iT * $nodeSampleCount / $parentRecordCount;
    }

    /**
     * Returns HTML representation of the node including children nodes
     *
     * @param $columnNames
     * @return string
     */
    public function getHTML($columnNames = null)
    {
        if ($this->isTerminal) {
            $value = "<b>$this->classValue</b>";
        } else {
            $value = $this->value;
            if ($columnNames !== null) {
                $col = $columnNames[$this->columnIndex];
            } else {
                $col = "col_$this->columnIndex";
            }
            if (!preg_match("/^[<>=]{1,2}/", $value)) {
                $value = "=$value";
            }
            $value = "<b>$col $value</b><br>Gini: ". number_format($this->giniIndex, 2);
        }
        $str = "<table ><tr><td colspan=3 align=center style='border:1px solid;'>
				$value</td></tr>";
        if ($this->leftLeaf || $this->rightLeaf) {
            $str .='<tr>';
            if ($this->leftLeaf) {
                $str .="<td valign=top><b>| Yes</b><br>" . $this->leftLeaf->getHTML($columnNames) . "</td>";
            } else {
                $str .='<td></td>';
            }
            $str .='<td>&nbsp;</td>';
            if ($this->rightLeaf) {
                $str .="<td valign=top align=right><b>No |</b><br>" . $this->rightLeaf->getHTML($columnNames) . "</td>";
            } else {
                $str .='<td></td>';
            }
            $str .= '</tr>';
        }
        $str .= '</table>';
        return $str;
    }

    /**
     * HTML representation of the tree without column names
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getHTML();
    }
}
