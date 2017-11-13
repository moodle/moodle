<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2017 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.github.io/scssphp
 */

namespace Leafo\ScssPhp;

/**
 * Block
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class Block
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var \Leafo\ScssPhp\Block
     */
    public $parent;

    /**
     * @var integer
     */
    public $sourceIndex;

    /**
     * @var integer
     */
    public $sourceLine;

    /**
     * @var integer
     */
    public $sourceColumn;

    /**
     * @var array
     */
    public $selectors;

    /**
     * @var array
     */
    public $comments;

    /**
     * @var array
     */
    public $children;
}
