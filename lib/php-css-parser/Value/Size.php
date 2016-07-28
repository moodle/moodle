<?php

namespace Sabberworm\CSS\Value;

class Size extends PrimitiveValue {

	const ABSOLUTE_SIZE_UNITS = 'px/cm/mm/mozmm/in/pt/pc/vh/vw/vm/vmin/vmax/rem'; //vh/vw/vm(ax)/vmin/rem are absolute insofar as they don’t scale to the immediate parent (only the viewport)
	const RELATIVE_SIZE_UNITS = '%/em/ex/ch/fr';
	const NON_SIZE_UNITS = 'deg/grad/rad/s/ms/turns/Hz/kHz';

	private $fSize;
	private $sUnit;
	private $bIsColorComponent;

	public function __construct($fSize, $sUnit = null, $bIsColorComponent = false, $iLineNo = 0) {
		parent::__construct($iLineNo);
		$this->fSize = floatval($fSize);
		$this->sUnit = $sUnit;
		$this->bIsColorComponent = $bIsColorComponent;
	}

	public function setUnit($sUnit) {
		$this->sUnit = $sUnit;
	}

	public function getUnit() {
		return $this->sUnit;
	}

	public function setSize($fSize) {
		$this->fSize = floatval($fSize);
	}

	public function getSize() {
		return $this->fSize;
	}

	public function isColorComponent() {
		return $this->bIsColorComponent;
	}

	/**
	 * Returns whether the number stored in this Size really represents a size (as in a length of something on screen).
	 * @return false if the unit an angle, a duration, a frequency or the number is a component in a Color object.
	 */
	public function isSize() {
		if (in_array($this->sUnit, explode('/', self::NON_SIZE_UNITS))) {
			return false;
		}
		return !$this->isColorComponent();
	}

	public function isRelative() {
		if (in_array($this->sUnit, explode('/', self::RELATIVE_SIZE_UNITS))) {
			return true;
		}
		if ($this->sUnit === null && $this->fSize != 0) {
			return true;
		}
		return false;
	}

	public function __toString() {
		return $this->render(new \Sabberworm\CSS\OutputFormat());
	}

	public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat) {
		$l = localeconv();
		$sPoint = preg_quote($l['decimal_point'], '/');
		return preg_replace(array("/$sPoint/", "/^(-?)0\./"), array('.', '$1.'), $this->fSize) . ($this->sUnit === null ? '' : $this->sUnit);
	}

}
