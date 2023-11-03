<?php
namespace \block_learnerscript\Spout;

use \block_learnerscript\Spout\Common\Type;
use \block_learnerscript\Spout\Writer\WriterFactory;

class spout {
	/**
	 * @var $spouttype
	 */
	protected $spouttype = '';
	/**
	 * @var $mimetype
	 */
    protected $mimetype = "text/plain";

    /**
     * @var $extension
     */
    protected $extension = ".txt";

    /**
     * @var $filename
     */
    protected $filename = '';

    public function __construct(){

    }

}