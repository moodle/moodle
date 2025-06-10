<?php
/*
pDraw - class to manipulate data arrays

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* pData class definition */
class pData
{
	protected $Data;
	protected $Palette;

	function __construct()
	{
		$this->Palette = [
			new pColor(188,224,46,100),
			new pColor(224,100,46,100),
			new pColor(224,214,46,100),
			new pColor(46,151,224,100),
			new pColor(176,46,224,100),
			new pColor(224,46,117,100),
			new pColor(92,224,46,100),
			new pColor(224,176,46,100)
		];

		$this->Data = [
			"Series" => [],
			"ScatterSeries" => [],
			"Abscissa" => NULL,
			"AbscissaProperties" => [
				"Name" => NULL,
				"Position" => 0,
				"Display" => AXIS_FORMAT_DEFAULT,
				"Format" => NULL,
				"Unit" => NULL
			],
			"Axis" => [0 => [
					"Identity" => AXIS_Y,
					"Position" => AXIS_POSITION_LEFT,
					"Display" => AXIS_FORMAT_DEFAULT,
					"Format" => NULL,
					"Unit" => NULL
				]
			]
		];
	}

	/* Initialize a given serie */
	public function initialise(string $Serie)
	{
		$Id = count($this->Data["Series"]);

		$this->Data["Series"][$Serie] = [
			"Data" => [],
			"Description" => $Serie,
			"isDrawable" => TRUE,
			"Picture" => NULL,
			"Max" => 0,
			"Min" => 0,
			"Axis" => 0,
			"Ticks" => NULL,
			"Weight" => NULL,
			"XOffset" => 0,
			"Shape" => SERIE_SHAPE_FILLEDCIRCLE,
			"Color" => (isset($this->Palette[$Id])) ? $this->Palette[$Id] : new pColor()
		];
	}

	/* Add a single point or an array to the given serie */
	public function addPoints(array $Values, string $SerieName)
	{
		if (!isset($this->Data["Series"][$SerieName])){
			$this->initialise($SerieName);
		}

		foreach($Values as $Value) {
			$this->Data["Series"][$SerieName]["Data"][] = $Value;
		}

		$StrippedData = array_diff($this->Data["Series"][$SerieName]["Data"], [VOID]);

		if (empty($StrippedData)) {
			$this->Data["Series"][$SerieName]["Max"] = 0;
			$this->Data["Series"][$SerieName]["Min"] = 0;
		} else {
			$this->Data["Series"][$SerieName]["Max"] = max($StrippedData);
			$this->Data["Series"][$SerieName]["Min"] = min($StrippedData);
		}
	}

	/* In case you add points to the a serie with the same name - pSplit */
	public function clearPoints(string $SerieName)
	{
		$this->Data["Series"][$SerieName]["Data"] = [];
		$this->Data["Series"][$SerieName]["Max"] = 0;
		$this->Data["Series"][$SerieName]["Min"] = 0;
	}

	/* Remove a serie from the pData object */
	public function removeSerie(string $Serie)  # UNUSED
	{
		if (isset($this->Data["Series"][$Serie])) {
			unset($this->Data["Series"][$Serie]);
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	public function resetSeriesColors() # UNUSED
	{
		$Id = 0;
		foreach($this->Data["Series"] as $SerieName => $SeriesParameters) {
			if ($SeriesParameters["isDrawable"]) {
				$this->Data["Series"][$SerieName]["Color"] = $this->Palette[$Id];
				$Id++;
			}
		}
	}

	/* Combination of 
		setSerieShape
		setSerieDescription
		setSerieDrawable
		setSeriePicture
		setSerieWeight
		setSerieTicks
	*/
	public function setSerieProperties(string $Serie, array $Props)
	{
		if (!isset($this->Data["Series"][$Serie])) {
			throw pException::InvalidInput("Invalid serie name");
		}

		(isset($Props["Picture"]))    	AND $this->Data["Series"][$Serie]["Picture"]     = strval($Props["Picture"]);
		(isset($Props["Description"])) 	AND $this->Data["Series"][$Serie]["Description"] = strval($Props["Description"]);
		(isset($Props["Shape"]))  	AND $this->Data["Series"][$Serie]["Shape"]  	 = intval($Props["Shape"]);
		(isset($Props["isDrawable"]))   AND $this->Data["Series"][$Serie]["isDrawable"]  = boolval($Props["isDrawable"]);
		(isset($Props["Ticks"]))   	AND $this->Data["Series"][$Serie]["Ticks"]  	 = intval($Props["Ticks"]);
		(isset($Props["Weight"]))   	AND $this->Data["Series"][$Serie]["Weight"]  	 = intval($Props["Weight"]);
	}

	/* Set the description of a given serie */
	public function setSerieDescription(string $Serie, string $Description)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Description"] = $Description;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set the serie that will be used as abscissa */
	public function setAbscissa(string $Serie, array $Props = [])
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Abscissa"] = $Serie;

			if (!empty($Props)){
				(isset($Props["Name"]))    AND $this->Data["AbscissaProperties"]["Name"]    = strval($Props["Name"]);
				(isset($Props["Display"])) AND $this->Data["AbscissaProperties"]["Display"] = intval($Props["Display"]);
				(isset($Props["Format"]))  AND $this->Data["AbscissaProperties"]["Format"]  = $Props["Format"];
				(isset($Props["Unit"]))    AND $this->Data["AbscissaProperties"]["Unit"]    = strval($Props["Unit"]);
				(isset($Props["Name"]))    AND $this->Data["AbscissaProperties"]["Name"]    = strval($Props["Name"]);
				(isset($Props["Position"])) AND $this->Data["AbscissaProperties"]["Position"] = intval($Props["Position"]);
			}
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set the name of the abscissa axis */
	public function setAbscissaName(string $Name)
	{
		$this->Data["AbscissaProperties"]["Name"] = $Name;
	}

	/* Return the abscissa margin */
	public function getAbscissaMargin()
	{
		foreach($this->Data["Axis"] as $Values) {
			if ($Values["Identity"] == AXIS_X) {
				return $Values["Margin"];
			}
		}

		throw pException::InvalidInput("No margin set!");
	}

	/* Create a scatter group specifying X and Y data series */
	public function setScatterSerie(string $SerieX, string $SerieY, int $Id = 0)
	{
		if (isset($this->Data["Series"][$SerieX]) && isset($this->Data["Series"][$SerieY])) {
			$this->initScatterSerie($Id);
			$this->Data["ScatterSeries"][$Id]["X"] = $SerieX;
			$this->Data["ScatterSeries"][$Id]["Y"] = $SerieY;
		} else {
			throw pException::InvalidInput("Invalid scatter serie names");
		}
	}

	/* Combination of 
		setScatterSerieShape
		setScatterSerieDescription
		setScatterSeriePicture
		setScatterSerieDrawable
		setScatterSerieTicks
		setScatterSerieWeight
		setScatterSerieColor
	*/
	public function setScatterSerieProperties(int $Id, array $Props)
	{
		if (isset($this->Data["ScatterSeries"][$Id])) {

			(isset($Props["Shape"]))	AND $this->Data["ScatterSeries"][$Id]["Shape"]	     = intval($Props["Shape"]);
			(isset($Props["Description"]))	AND $this->Data["ScatterSeries"][$Id]["Description"] = strval($Props["Description"]);
			(isset($Props["Picture"]))	AND $this->Data["ScatterSeries"][$Id]["Picture"]     = strval($Props["Picture"]);
			(isset($Props["isDrawable"]))	AND $this->Data["ScatterSeries"][$Id]["isDrawable"]  = boolval($Props["isDrawable"]);
			(isset($Props["Ticks"]))	AND $this->Data["ScatterSeries"][$Id]["Ticks"]	     = intval($Props["Ticks"]);
			(isset($Props["Weight"]))	AND $this->Data["ScatterSeries"][$Id]["Weight"]	     = intval($Props["Weight"]);
			if (isset($Props["Color"])) {
				if ($Props["Color"] instanceof pColor){
					$this->Data["ScatterSeries"][$Id]["Color"] = $Props["Color"];
				} else {
					throw pException::InvalidInput("Invalid Color format");
				}
			}
		} else {
			throw pException::InvalidInput("Invalid serie Id");
		}
	}

	/* Initialize a given scatter serie */
	public function initScatterSerie(int $Id)
	{
		if (isset($this->Data["ScatterSeries"][$Id])) {
			throw pException::InvalidInput("Invalid scatter serie Id");
		}

		$this->Data["ScatterSeries"][$Id] = [
			"Description" => "Scatter " . $Id,
			"isDrawable" => TRUE,
			"Picture" => NULL,
			"Ticks" => NULL,
			"Weight" => NULL,
			"Color" => (isset($this->Palette[$Id])) ? $this->Palette[$Id] : new pColor()
		];
	}

	/* Compute the series limits for an individual and global point of view */
	public function limits()
	{
		$GlobalMin = PHP_INT_MIN;
		$GlobalMax = PHP_INT_MAX;
		foreach($this->Data["Series"] as $Key => $Value) {
			if ($this->Data["Abscissa"] != $Key && $this->Data["Series"][$Key]["isDrawable"]) {
				if ($GlobalMin > $this->Data["Series"][$Key]["Min"]) {
					$GlobalMin = $this->Data["Series"][$Key]["Min"];
				}

				if ($GlobalMax < $this->Data["Series"][$Key]["Max"]) {
					$GlobalMax = $this->Data["Series"][$Key]["Max"];
				}
			}
		}

		$this->Data["Min"] = $GlobalMin;
		$this->Data["Max"] = $GlobalMax;
		return [$GlobalMin,$GlobalMax];
	}

	/* Mark all series as drawable */
	public function setAllDrawable()
	{
		foreach($this->Data["Series"] as $Key => $Value) {
			if ($this->Data["Abscissa"] != $Key) {
				$this->Data["Series"][$Key]["isDrawable"] = TRUE;
			}
		}
	}

	/* Returns the number of drawable series */
	public function countDrawableSeries()
	{
		$Results = 0;
		foreach($this->Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $this->Data["Abscissa"]) {
				$Results++;
			}
		}

		return $Results;
	}

	/* Combination of:
		setAxisDisplay
		setAxisPosition
		setAxisUnit
		setAxisName
		setAxisColor
		setAxisXY 
	*/
	public function setAxisProperties(int $AxisID, array $Props)
	{
		if (isset($this->Data["Axis"][$AxisID])) {

			(isset($Props["Unit"]))     AND $this->Data["Axis"][$AxisID]["Unit"] 	 = strval($Props["Unit"]);
			(isset($Props["Name"]))     AND $this->Data["Axis"][$AxisID]["Name"] 	 = strval($Props["Name"]);
			(isset($Props["Display"]))  AND $this->Data["Axis"][$AxisID]["Display"]  = intval($Props["Display"]);
			(isset($Props["Format"]))   AND $this->Data["Axis"][$AxisID]["Format"] 	 = $Props["Format"];
			(isset($Props["Position"])) AND $this->Data["Axis"][$AxisID]["Position"] = intval($Props["Position"]);
			(isset($Props["Identity"])) AND $this->Data["Axis"][$AxisID]["Identity"] = intval($Props["Identity"]);
			if (isset($Props["Color"])) {
				if ($Props["Color"] instanceof pColor){
					$this->Data["Axis"][$AxisID]["Color"] = $Props["Color"];
				} else {
					throw pException::InvalidInput("Invalid Color format");
				}
			}
		} else {
			throw pException::InvalidInput("Invalid serie Id");
		}
	}

	/* Associate a name to an axis */
	public function setAxisName(int $AxisID, string $Name)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Name"] = $Name;
		} else {
			throw pException::InvalidInput("Invalid serie Id");
		}
	}

	/* Associate one data serie with one axis */
	public function setSerieOnAxis(string $Serie, int $AxisID)
	{
		$PreviousAxis = $this->Data["Series"][$Serie]["Axis"];
		/* Create missing axis */
		if (!isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT;
			$this->Data["Axis"][$AxisID]["Identity"] = AXIS_Y;
			$this->Data["Axis"][$AxisID]["Unit"] = NULL;
			$this->Data["Axis"][$AxisID]["Format"] = NULL;
			$this->Data["Axis"][$AxisID]["Display"] = NULL;
		}

		$this->Data["Series"][$Serie]["Axis"] = $AxisID;
		/* Cleanup unused axis */
		$Found = FALSE;
		foreach($this->Data["Series"] as $SerieName => $Values) {
			if ($Values["Axis"] == $PreviousAxis) {
				$Found = TRUE;
			}
		}

		if (!$Found) {
			unset($this->Data["Axis"][$PreviousAxis]);
		}
	}

	/* Set the color of one serie */
	/* Momchil: tried to refactor. did not work */
	public function setPalette(string $Serie, pColor $Color)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$Old = $this->Data["Series"][$Serie]["Color"];
			$this->Data["Series"][$Serie]["Color"] = $Color;
			/* Do reverse processing on the internal palette array */
			foreach($this->Palette as $Key => $Value) {
				if ($Value == $Old) {
					$this->Palette[$Key] = $Color;
				}
			}
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Load a palette file */
	public function loadPalette(array $MyPalette, bool $Reset = FALSE)
	{
		if ($Reset) {
			$this->Palette = [];
		}

		foreach($MyPalette as $Id => $color){
			if (is_array($color)) {
				$this->Palette[$Id] = new pColor($color[0], $color[1], $color[2], $color[3]);
			} else {
				throw pException::InvalidInput("Invalid palette");
			}
		}

		/* Apply changes to current series */
		if (isset($this->Data["Series"])) {
			/* Momchil: no unit test gets here */
			foreach(array_keys($this->Data["Series"]) as $Id => $Key) {
				$this->Data["Series"][$Key]["Color"] = (!isset($this->Palette[$Id])) ? new pColor(0,0,0,0) : $this->Palette[$Id];
			}
		}
	}

	/* used in pPie */
	public function getPieParams($forLegend = FALSE)
	{
		/* Do we have an abscissa serie defined? */
		if (!in_array($this->Data["Abscissa"], array_keys($this->Data["Series"]))) {
			throw pException::PieNoAbscissaException();
		} else {
			$AbscissaData = $this->Data["Series"][$this->Data["Abscissa"]]["Data"];
		}

		$Palette = $this->Palette;

		if(!$forLegend){
			$SeriesData = $this->Data["Series"];
			$left = array_diff(array_keys($SeriesData), [$this->Data["Abscissa"]]);

			if (count($left) != 1){
				throw pException::PieNoDataSerieException();
			}

			/* Remove unused data clean0Values */
			$Values = array_shift($SeriesData)["Data"];

			foreach($Values as $key => $v) {
				 if ($v == NULL || $v == 0) {
					unset($Values[$key]);
					unset($AbscissaData[$key]);
					unset($Palette[$key]);
				 }
			}

			$Values = array_values($Values);
			$Palette = array_values($Palette);
			$AbscissaData = array_values($AbscissaData);

			/* Gen Palette */
			foreach($Values as $Id => $Value) {
				if(!isset($Palette[$Id])){
					$Palette[$Id] = new pColor();
				}
			}
			
			/* Save the new palette in case we need to draw a legend as well */
			$this->Palette = $Palette;
		} else {
			$Values = [];
		}

		return [$AbscissaData, $Values, $Palette];
	}

	/* Save a palette */
	public function savePalette(array $newPalette)
	{
		foreach($newPalette as $Id => $Color) {
			$this->Palette[$Id] = $Color;
		}
	}

	/* Return the palette of the series */
	public function getPalette()
	{
		return $this->Palette;
	}

	public function normalize(int $NormalizationFactor = 100, string $UnitChange = "", int $Round = 1)
	{
		$SelectedSeries = [];
		$MaxVal = 0;
		foreach($this->Data["Axis"] as $AxisID => $Axis) {

			($UnitChange != "") AND $this->Data["Axis"][$AxisID]["Unit"] = $UnitChange;

			foreach($this->Data["Series"] as $SerieName => $Serie) {
				if ($Serie["Axis"] == $AxisID && $Serie["isDrawable"] == TRUE && $SerieName != $this->Data["Abscissa"]) {
					$SelectedSeries[$SerieName] = $SerieName;
					if (count($Serie["Data"]) > $MaxVal) {
						$MaxVal = count($Serie["Data"]);
					}
				}
			}
		}

		for ($i = 0; $i < $MaxVal; $i++) {
			$Factor = 0;
			foreach($SelectedSeries as $SerieName) {
				$Value = $this->Data["Series"][$SerieName]["Data"][$i];
				($Value != VOID) AND $Factor += abs($Value);
			}

			if ($Factor != 0) {
				$Factor = $NormalizationFactor / $Factor;
				foreach($SelectedSeries as $SerieName) {
					$Value = $this->Data["Series"][$SerieName]["Data"][$i];
					if ($Value != VOID && $Factor != $NormalizationFactor) {
						$this->Data["Series"][$SerieName]["Data"][$i] = round(abs($Value) * $Factor, $Round);
					} elseif ($Value == VOID || $Value == 0) {
						$this->Data["Series"][$SerieName]["Data"][$i] = VOID;
					} elseif ($Factor == $NormalizationFactor) {
						$this->Data["Series"][$SerieName]["Data"][$i] = $NormalizationFactor;
					}
				}
			}
		}

		foreach($SelectedSeries as $SerieName) {
			$data = array_diff($this->Data["Series"][$SerieName]["Data"],[VOID]);
			$this->Data["Series"][$SerieName]["Max"] = max($data);
			$this->Data["Series"][$SerieName]["Min"] = min($data);
		}
	}

	/* Create a dataset based on a formula */
	public function createFunctionSerie(string $SerieName, \Closure $Function, string $Formula, array $Options = [])
	{
		$MinX = isset($Options["MinX"]) ? $Options["MinX"] : -10;
		$MaxX = isset($Options["MaxX"]) ? $Options["MaxX"] : 10;
		$XStep = isset($Options["XStep"]) ? $Options["XStep"] : 1;
		$AutoDescription = isset($Options["AutoDescription"]) ? $Options["AutoDescription"] : FALSE;
		$RecordAbscissa = isset($Options["RecordAbscissa"]) ? $Options["RecordAbscissa"] : FALSE;
		$AbscissaSerie = isset($Options["AbscissaSerie"]) ? $Options["AbscissaSerie"] : "Abscissa";

		$Result = [];
		$Abscissa = [];

		for ($i = $MinX; $i <= $MaxX; $i = $i + $XStep) {
			$Abscissa[] = $i;
			$ret = $Function($i);
			$Result[] = (in_array("$ret", ["NAN", "INF", "-INF"])) ? VOID : $ret;
		}

		$this->addPoints($Result, $SerieName);

		($AutoDescription) AND $this->setSerieDescription($SerieName, $Formula);
		($RecordAbscissa) AND $this->addPoints($Abscissa, $AbscissaSerie);
	}

	public function scaleGetXSettings()
	{
		foreach($this->Data["Axis"] as $Settings) {
			if ($Settings["Identity"] == AXIS_X) {
				return [$Settings["Margin"],$Settings["Rows"]];
			}
		}
	}

	/* Return the data & configuration of the series */
	public function getData()
	{
		return $this->Data;
	}

	public function saveData(array $Data)
	{
		foreach($Data as $key => $value) {
			if (isset($this->Data[$key])) {
				if (gettype($this->Data[$key]) != gettype($value)) {
					throw pException::InvalidInput("Wrong data type for $key");
				}
			}
			$this->Data[$key] = $value;
		}
	}

}
