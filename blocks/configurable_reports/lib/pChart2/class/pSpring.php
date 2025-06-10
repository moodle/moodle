<?php
/*
pSpring - class to draw spring graphs

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("NODE_TYPE_FREE", 690001);
define("NODE_TYPE_CENTRAL", 690002);
define("NODE_SHAPE_CIRCLE", 690011);
define("NODE_SHAPE_TRIANGLE", 690012);
define("NODE_SHAPE_SQUARE", 690013);
define("ALGORITHM_RANDOM", 690021);
define("ALGORITHM_WEIGHTED", 690022);
define("ALGORITHM_CIRCULAR", 690023);
define("ALGORITHM_CENTRAL", 690024);
define("LABEL_CLASSIC", 690031);
define("LABEL_LIGHT", 690032);

/* pSpring class definition */
class pSpring
{
	private $myPicture;
	private $History;
	private $Data;
	private $Links;
	private $AutoComputeFreeZone;
	private $Labels;
	private $MagneticForceA;
	private $MagneticForceR;
	private $RingSize;
	private $X1;
	private $Y1;
	private $X2;
	private $Y2;
	private $Default;

	function __construct(\pChart\pDraw $pChartObject)
	{
		/* Initialize data arrays */
		$this->Data = [];
		$this->Links = [];
		/* Set nodes defaults */
		$this->Default = [
			"Color" => new pColor(255),
			"BorderColor" => new pColor(0),
			"Surrounding" => NULL,
			"BackgroundColor" => new pColor(255),
			"Force" => 1,
			"NodeType" => NODE_TYPE_FREE,
			"Size" => 5,
			"Shape" => NODE_SHAPE_CIRCLE,
			"FreeZone" => 40,
			"LinkColor" => new pColor(0)
		];
		$this->Labels = ["Type" => LABEL_CLASSIC, "Color" => new pColor(0)];
		$this->AutoComputeFreeZone = FALSE; # Always FALSE

		$this->myPicture = $pChartObject;
	}

	/* Set default links options */
	public function setLinkDefaults(array $Settings = [])
	{
		#$vars = ["R", "G", "B", "Alpha"];
		foreach ($Settings as $key => $value){
			$this->Default["Link".$key] = $value;
		}
	}

	/* Set default links options */
	public function setLabelsSettings(array $Settings = [])
	{
		#$vars = ["Type", "R", "G", "B", "Alpha"];
		foreach ($Settings as $key => $value){
			$this->Labels[$key] = $value;
		}
	}

	/* Auto compute the FreeZone size based on the number of connections */
	public function autoFreeZone()
	{
		/* Check connections reciprocity */
		foreach($this->Data as $Key => $Settings) {
			$this->Data[$Key]["FreeZone"] = (isset($Settings["Connections"])) ? count($Settings["Connections"]) * 10 + 20 : 20;
		}
	}

	/* Set link properties */
	public function linkProperties(int $FromNode, int $ToNode, array $Settings)
	{
		if (!isset($this->Data[$FromNode])) {
			throw pException::SpringInvalidInputException("No data FromNode!");
		}

		if (!isset($this->Data[$ToNode])) {
			throw pException::SpringInvalidInputException("No data ToNode!");
		}

		$Color = new pColor(0);
		$Name = NULL;
		$Ticks = NULL;

		extract($Settings);

		$this->Links[$FromNode][$ToNode] = ["Color" => $Color, "Name" => $Name, "Ticks" => $Ticks];
		$this->Links[$ToNode][$FromNode] = $this->Links[$FromNode][$ToNode];

	}

	public function setNodeDefaults(array $Settings = [])
	{
		#$vars = ["R", "G", "B", "Alpha", "BorderR", "BorderG", "BorderB", "BorderAlpha", "Surrounding", "BackgroundR", "BackgroundG", "BackgroundB", "BackgroundAlpha", "NodeType", "Size", "Shape", "FreeZone"];
		foreach ($Settings as $key => $value){
			$this->Default[$key] = $value;
		}
	}

	/* Add a node */
	public function addNode(int $NodeID, array $Settings = [])
	{
		/* if the node already exists, ignore */
		if (isset($this->Data[$NodeID])) {
			throw pException::SpringInvalidInputException("Node ".$NodeID." is invalid!");
		}

		$Name = "Node " . strval($NodeID);
		$Connections = [];
		$Color = $this->Default["Color"];
		$BorderColor = $this->Default["BorderColor"];
		$BackgroundColor = $this->Default["BackgroundColor"];
		$Surrounding = $this->Default["Surrounding"];
		$Force = $this->Default["Force"];
		$NodeType = $this->Default["NodeType"];
		$Size = $this->Default["Size"];
		$Shape = $this->Default["Shape"];
		$FreeZone = $this->Default["FreeZone"];

		/* Override defaults */
		extract($Settings);

		if (!is_null($Surrounding)) {
			$BorderColor = $Color->newOne()->RGBChange($Surrounding);
		}

		$this->Data[$NodeID] = [
			"Color" => $Color,
			"BorderColor" => $BorderColor,
			"BackgroundColor" => $BackgroundColor,
			"Name" => $Name,
			"Force" => $Force,
			"Type" => $NodeType,
			"Size" => $Size,
			"Shape" => $Shape,
			"FreeZone" => $FreeZone
		];

		if (!is_array($Connections)){
			throw pException::SpringIvalidConnectionsException();
		}

		foreach($Connections as $Value){
			$this->Data[$NodeID]["Connections"][] = $Value;
		}
	}

	/* Set color attribute for a list of nodes */
	public function setNodesColor(array $Nodes, array $Settings = [])
	{
		foreach($Nodes as $NodeID) {
			if (isset($this->Data[$NodeID])) {

				(isset($Settings["Color"]))		  AND $this->Data[$NodeID]["Color"] = $Settings["Color"];
				(isset($Settings["BorderColor"])) AND $this->Data[$NodeID]["BorderColor"] = $Settings["BorderColor"];
				(isset($Settings["Surrounding"])) AND $this->Data[$NodeID]["BorderColor"] = $this->Data[$NodeID]["Color"]->newOne()->RGBChange($Settings["Surrounding"]);

			} else {
				throw pException::SpringInvalidInputException($NodeID." is invalid node");
			}
		}

	}

	/* Get the median linked nodes position */
	public function getMedianOffset($Key, $X, $Y)
	{
		$Cpt = 1;
		if (isset($this->Data[$Key]["Connections"])) {
			foreach($this->Data[$Key]["Connections"] as $NodeID) {
				if (isset($this->Data[$NodeID]["X"]) && isset($this->Data[$NodeID]["Y"])) {
					$X += $this->Data[$NodeID]["X"];
					$Y += $this->Data[$NodeID]["Y"];
					$Cpt++;
				}
			}
		}

		return ["X" => $X / $Cpt,"Y" => $Y / $Cpt];
	}

	/* Return the ID of the attached partner with the biggest weight */
	public function getBiggestPartner(int $Key)
	{
		if (!isset($this->Data[$Key]["Connections"])) {
			throw pException::SpringInvalidInputException("Connection ID is invalid");
		}

		$MaxWeight = 0;
		$Result = "";
		foreach($this->Data[$Key]["Connections"] as $PeerID) {
			if ($this->Data[$PeerID]["Weight"] > $MaxWeight) {
				$MaxWeight = $this->Data[$PeerID]["Weight"];
				$Result = $PeerID;
			}
		}

		return $Result;
	}

	/* Do the initial node positions computing pass */
	private function firstPass($Algorithm)
	{
		$CenterX = ($this->X2 - $this->X1) / 2 + $this->X1;
		$CenterY = ($this->Y2 - $this->Y1) / 2 + $this->Y1;
		/* Check connections reciprocity */
		foreach($this->Data as $Key => $Settings) {
			if (isset($Settings["Connections"])) {
				foreach($Settings["Connections"] as $ConnectionID) {

					/* Check connections reciprocity */
					if (isset($this->Data[$ConnectionID]["Connections"])) {
						if(!in_array($Key, $this->Data[$ConnectionID]["Connections"])) {
							$this->Data[$ConnectionID]["Connections"][] = $Key;
						}
					} else {
						$this->Data[$ConnectionID]["Connections"] = [$Key];
					}
				}
			}
		}

		if ($this->AutoComputeFreeZone) {
			$this->autoFreeZone();
		}

		/* Get the max number of connections */
		$MaxConnections = 0;
		foreach($this->Data as $Settings) {
			if (isset($Settings["Connections"])) {
				if ($MaxConnections < count($Settings["Connections"])) {
					$MaxConnections = count($Settings["Connections"]);
				}
			}
		}

		switch ($Algorithm) {
			case ALGORITHM_WEIGHTED:
				foreach($this->Data as $Key => $Settings) {
					if ($Settings["Type"] == NODE_TYPE_CENTRAL) {
						$this->Data[$Key]["X"] = $CenterX;
						$this->Data[$Key]["Y"] = $CenterY;
					}

					if ($Settings["Type"] == NODE_TYPE_FREE) {
						$Connections = (isset($Settings["Connections"])) ? count($Settings["Connections"]) : 0;
						$Ring = $MaxConnections - $Connections;
						$Angle = rand(0, 360);
						$this->Data[$Key]["X"] = cos(deg2rad($Angle)) * ($Ring * $this->RingSize) + $CenterX;
						$this->Data[$Key]["Y"] = sin(deg2rad($Angle)) * ($Ring * $this->RingSize) + $CenterY;
					}
				}
				break;
			case ALGORITHM_CENTRAL:
				/* Put a weight on each nodes */
				foreach($this->Data as $Key => $Settings) {
					$this->Data[$Key]["Weight"] = (isset($Settings["Connections"])) ? count($Settings["Connections"]) : 0;
				}

				$MaxConnections++;
				for ($i = $MaxConnections; $i >= 0; $i--) {
					foreach($this->Data as $Key => $Settings) {
						if ($Settings["Type"] == NODE_TYPE_CENTRAL) {
							$this->Data[$Key]["X"] = $CenterX;
							$this->Data[$Key]["Y"] = $CenterY;
						} elseif ($Settings["Type"] == NODE_TYPE_FREE) {
							$Connections = (isset($Settings["Connections"])) ? count($Settings["Connections"]) : 0;
							if ($Connections == $i) {
								$BiggestPartner = $this->getBiggestPartner($Key);
								if ($BiggestPartner != "") {
									$Ring = $this->Data[$BiggestPartner]["FreeZone"];
									$Weight = $this->Data[$BiggestPartner]["Weight"];
									$AngleDivision = 360 / $this->Data[$BiggestPartner]["Weight"];
									$Done = FALSE;
									$Tries = 0;
									while (!$Done && $Tries <= $Weight * 2) {
										$Tries++;
										$Angle = floor(rand(0, $Weight) * $AngleDivision);
										if (!isset($this->Data[$BiggestPartner]["Angular"][$Angle]) || !isset($this->Data[$BiggestPartner]["Angular"])) {
											$this->Data[$BiggestPartner]["Angular"][$Angle] = $Angle;
											$Done = TRUE;
										}
									}

									if (!$Done) {
										$Angle = rand(0, 360);
										$this->Data[$BiggestPartner]["Angular"][$Angle] = $Angle;
									}

									$X = cos(deg2rad($Angle)) * $Ring + $this->Data[$BiggestPartner]["X"];
									$Y = sin(deg2rad($Angle)) * $Ring + $this->Data[$BiggestPartner]["Y"];
									$this->Data[$Key]["X"] = $X;
									$this->Data[$Key]["Y"] = $Y;
								}
							}
						}
					}
				}
				break;
			case ALGORITHM_CIRCULAR:
				$MaxConnections++;
				for ($i = $MaxConnections; $i >= 0; $i--) {
					foreach($this->Data as $Key => $Settings) {
						if ($Settings["Type"] == NODE_TYPE_CENTRAL) {
							$this->Data[$Key]["X"] = $CenterX;
							$this->Data[$Key]["Y"] = $CenterY;
						} elseif ($Settings["Type"] == NODE_TYPE_FREE) {
							$Connections = (isset($Settings["Connections"])) ? count($Settings["Connections"]) : 0;
							if ($Connections == $i) {
								$Ring = $MaxConnections - $Connections;
								$Angle = rand(0, 360);
								$X = cos(deg2rad($Angle)) * ($Ring * $this->RingSize) + $CenterX;
								$Y = sin(deg2rad($Angle)) * ($Ring * $this->RingSize) + $CenterY;
								$MedianOffset = $this->getMedianOffset($Key, $X, $Y);
								$this->Data[$Key]["X"] = $MedianOffset["X"];
								$this->Data[$Key]["Y"] = $MedianOffset["Y"];
							}
						}
					}
				}
				break;
			case ALGORITHM_RANDOM:
				foreach($this->Data as $Key => $Settings) {
					if ($Settings["Type"] == NODE_TYPE_FREE) {
						$this->Data[$Key]["X"] = $CenterX + rand(-20, 20);
						$this->Data[$Key]["Y"] = $CenterY + rand(-20, 20);
					} elseif ($Settings["Type"] == NODE_TYPE_CENTRAL) {
						$this->Data[$Key]["X"] = $CenterX;
						$this->Data[$Key]["Y"] = $CenterY;
					}
				}
				break;
		}
	}

	/* Compute one pass */
	private function doPass()
	{
		/* Compute vectors */
		foreach($this->Data as $Key => $Settings) {
			if ($Settings["Type"] != NODE_TYPE_CENTRAL) {
				unset($this->Data[$Key]["Vectors"]);
				$X1 = $Settings["X"];
				$Y1 = $Settings["Y"];
				/* Repulsion vectors */
				foreach($this->Data as $Key2 => $Settings2) {
					if ($Key != $Key2) {
						$X2 = $this->Data[$Key2]["X"];
						$Y2 = $this->Data[$Key2]["Y"];
						$FreeZone = $this->Data[$Key2]["FreeZone"];
						$Distance = hypot(($X2 - $X1),($Y2 - $Y1)); # GetDistance
						$Angle = $this->getAngle($X1, $Y1, $X2, $Y2) + 180;
						/* Nodes too close, repulsion occurs */
						if ($Distance < $FreeZone) {
							$Force = log(pow(2, $FreeZone - $Distance));
							if ($Force > 1) {
								$this->Data[$Key]["Vectors"][] = ["Type" => "R","Angle" => intval($Angle) % 360,"Force" => $Force];
							}
						}
						$lastKey = $Key2;
					}
				}

				/* Attraction vectors */
				if (isset($Settings["Connections"])) {
					foreach($Settings["Connections"] as $NodeID) {
						if (isset($this->Data[$NodeID])) {
							$X2 = $this->Data[$NodeID]["X"];
							$Y2 = $this->Data[$NodeID]["Y"];
							$FreeZone = $this->Data[$lastKey]["FreeZone"];
							$Distance = hypot(($X2 - $X1),($Y2 - $Y1)); # GetDistance
							$Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
							if ($Distance > $FreeZone) {
								$Force = log(($Distance - $FreeZone) + 1);
							} else {
								$Force = log(($FreeZone - $Distance) + 1);
								$Angle += 180;
							}

							if ($Force > 1) {
								$this->Data[$Key]["Vectors"][] = ["Type" => "A","Angle" => intval($Angle) % 360,"Force" => $Force];
							}
						}
					}
				}
			}
		}

		/* Move the nodes according to the vectors */
		foreach($this->Data as $Key => $Settings) {
			$X = $Settings["X"];
			$Y = $Settings["Y"];
			if (isset($Settings["Vectors"]) && $Settings["Type"] != NODE_TYPE_CENTRAL) {
				foreach($Settings["Vectors"] as $Vector) {
					$Factor = ($Vector["Type"] == "A") ? $this->MagneticForceA : $this->MagneticForceR;
					$X = cos(deg2rad($Vector["Angle"])) * $Vector["Force"] * $Factor + $X;
					$Y = sin(deg2rad($Vector["Angle"])) * $Vector["Force"] * $Factor + $Y;
				}
			}

			$this->Data[$Key]["X"] = $X;
			$this->Data[$Key]["Y"] = $Y;
		}
	}

	private function lastPass()
	{
		/* Put everything inside the graph area */
		foreach($this->Data as $Key => $Settings) {
			$X = $Settings["X"];
			$Y = $Settings["Y"];
			($X < $this->X1) AND $X = $this->X1;
			($X > $this->X2) AND $X = $this->X2;
			($Y < $this->Y1) AND $Y = $this->Y1;
			($Y > $this->Y2) AND $Y = $this->Y2;
			$this->Data[$Key]["X"] = $X;
			$this->Data[$Key]["Y"] = $Y;
		}

		/* Dump all links */
		$Links = [];
		foreach($this->Data as $Settings) {
			if (isset($Settings["Connections"])) {
				foreach($Settings["Connections"] as $NodeID) {
					if (isset($this->Data[$NodeID])) {
						$Links[] = [
							"X1" => $Settings["X"],
							"Y1" => $Settings["Y"],
							"X2" => $this->Data[$NodeID]["X"],
							"Y2" => $this->Data[$NodeID]["Y"],
							"Source" => $Settings["Name"],
							"Destination" => $this->Data[$NodeID]["Name"]
						];
					}
				}
			}
		}

		/* Check collisions */
		$Conflicts = 0;
		foreach($this->Data as $Key => $Settings) {
			$X1 = $Settings["X"];
			$Y1 = $Settings["Y"];
			if (isset($Settings["Connections"])) {
				foreach($Settings["Connections"] as $NodeID) {
					if (isset($this->Data[$NodeID])) {
						$X2 = $this->Data[$NodeID]["X"];
						$Y2 = $this->Data[$NodeID]["Y"];
						foreach($Links as $IDLinks => $Link) {
							$X3 = $Link["X1"];
							$Y3 = $Link["Y1"];
							$X4 = $Link["X2"];
							$Y4 = $Link["Y2"];
							if (!($X1 == $X3 && $X2 == $X4 && $Y1 == $Y3 && $Y2 == $Y4)) {
								if ($this->intersect($X1, $Y1, $X2, $Y2, $X3, $Y3, $X4, $Y4)) {
									if ($Link["Source"] != $Settings["Name"] && $Link["Source"] != $this->Data[$NodeID]["Name"] && $Link["Destination"] != $Settings["Name"] && $Link["Destination"] != $this->Data[$NodeID]["Name"]) {
										$Conflicts++;
									}
								}
							}
						}
					}
				}
			}
		}

		return ($Conflicts / 2);
	}

	/* Center the graph */
	private function center()
	{
		/* Determine the real center */
		$TargetCenterX = ($this->X2 - $this->X1) / 2 + $this->X1;
		$TargetCenterY = ($this->Y2 - $this->Y1) / 2 + $this->Y1;
		/* Get current boundaries */
		$XMin = $this->X2;
		$XMax = $this->X1;
		$YMin = $this->Y2;
		$YMax = $this->Y1;
		foreach($this->Data as $Settings) {
			$X = $Settings["X"];
			$Y = $Settings["Y"];
			($X < $XMin) AND $XMin = $X;
			($X > $XMax) AND $XMax = $X;
			($Y < $YMin) AND $YMin = $Y;
			($Y > $YMax) AND $YMax = $Y;
		}

		$CurrentCenterX = ($XMax - $XMin) / 2 + $XMin;
		$CurrentCenterY = ($YMax - $YMin) / 2 + $YMin;
		/* Compute the offset to apply */
		$XOffset = $TargetCenterX - $CurrentCenterX;
		$YOffset = $TargetCenterY - $CurrentCenterY;
		/* Correct the points position */
		foreach($this->Data as $Key => $Settings) {
			$this->Data[$Key]["X"] = $Settings["X"] + $XOffset;
			$this->Data[$Key]["Y"] = $Settings["Y"] + $YOffset;
		}
	}

	/* Create the encoded string */
	public function drawSpring(array $Settings = [])
	{
		$Pass = isset($Settings["Pass"]) ? $Settings["Pass"] : 50;
		$Retries = isset($Settings["Retry"]) ? $Settings["Retry"] : 10;
		$this->MagneticForceA = isset($Settings["MagneticForceA"]) ? $Settings["MagneticForceA"] : 1.5;
		$this->MagneticForceR = isset($Settings["MagneticForceR"]) ? $Settings["MagneticForceR"] : 2;
		$this->RingSize = isset($Settings["RingSize"]) ? $Settings["RingSize"] : 40;
		$DrawVectors = isset($Settings["DrawVectors"]) ? $Settings["DrawVectors"] : FALSE;
		$DrawQuietZone = isset($Settings["DrawQuietZone"]) ? $Settings["DrawQuietZone"] : FALSE;
		$CenterGraph = isset($Settings["CenterGraph"]) ? $Settings["CenterGraph"] : TRUE;
		$TextPadding = isset($Settings["TextPadding"]) ? $Settings["TextPadding"] : 4;
		$Algorithm = isset($Settings["Algorithm"]) ? $Settings["Algorithm"] : ALGORITHM_WEIGHTED;

		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();
		$this->X1 = $GraphAreaCoordinates["L"];
		$this->Y1 = $GraphAreaCoordinates["T"];
		$this->X2 = $GraphAreaCoordinates["R"];
		$this->Y2 = $GraphAreaCoordinates["B"];

		$Conflicts = 1;
		$Jobs = 0;
		$this->History["MinimumConflicts"] = - 1;
		while ($Conflicts != 0 && $Jobs < $Retries) {
			$Jobs++;
			/* Compute the initial settings */
			$this->firstPass($Algorithm);
			/* Apply the vectors */
			if ($Pass > 0) {
				for ($i = 0; $i <= $Pass; $i++) {
					$this->doPass();
				}
			}

			$Conflicts = $this->lastPass();
			if ($this->History["MinimumConflicts"] == - 1 || $Conflicts < $this->History["MinimumConflicts"]) {
				$this->History["MinimumConflicts"] = $Conflicts;
				$this->History["Result"] = $this->Data;
			}
		}

		$Conflicts = $this->History["MinimumConflicts"];
		$this->Data = $this->History["Result"];
		if ($CenterGraph) {
			$this->center();
		}

		/* Draw the connections */
		$Drawn = [];
		$defaultColor = ["Color" => $this->Default["LinkColor"]->newOne()];
		foreach($this->Data as $Key => $Settings) {
			$X = $Settings["X"];
			$Y = $Settings["Y"];
			if (isset($Settings["Connections"])) {
				foreach($Settings["Connections"] as $NodeID) {
					if (!isset($Drawn[$Key])) {
						$Drawn[$Key] = [];
					}

					if (!isset($Drawn[$NodeID])) {
						$Drawn[$NodeID] = [];
					}

					if (isset($this->Data[$NodeID]) && !isset($Drawn[$Key][$NodeID]) && !isset($Drawn[$NodeID][$Key])) {
						$Color = $defaultColor;
						if (!empty($this->Links)) {
							if (isset($this->Links[$Key][$NodeID])) {
								$Color = $this->Links[$Key][$NodeID];
								unset($Color['name']); # ticks is already there
							}
						}

						$X2 = $this->Data[$NodeID]["X"];
						$Y2 = $this->Data[$NodeID]["Y"];
						if (($X2 != $X) && ($Y2 != $Y)){
							$this->myPicture->drawLine($X, $Y, $X2, $Y2, $Color);
						}
						$Drawn[$Key][$NodeID] = TRUE;

						if (!empty($this->Links)) {
							if (isset($this->Links[$Key][$NodeID]["Name"]) || isset($this->Links[$NodeID][$Key]["Name"])) {
								$Name = isset($this->Links[$Key][$NodeID]["Name"]) ? $this->Links[$Key][$NodeID]["Name"] : $this->Links[$NodeID][$Key]["Name"];
								$TxtX = ($X2 - $X) / 2 + $X;
								$TxtY = ($Y2 - $Y) / 2 + $Y;
								if ($X <= $X2) {
									$Angle = $this->myPicture->getAngle($X, $Y, $X2, $Y2);
								} else {
									$Angle = $this->myPicture->getAngle($X2, $Y2, $X, $Y);
								}

								$Settings = $Color;
								$Settings["Angle"] = floor(360 - $Angle);
								$Settings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
								$this->myPicture->drawText($TxtX, $TxtY, $Name, $Settings);
							}
						}
					}
				}
			}
		}

		/* Draw the quiet zones */
		if ($DrawQuietZone) {
			foreach($this->Data as $Settings) {
				$this->myPicture->drawFilledCircle($Settings["X"], $Settings["Y"],$Settings["FreeZone"], ["Color" => new pColor(0,0,0,2)]);
			}
		}

		/* Draw the nodes */
		foreach($this->Data as $Settings) {
			$X = $Settings["X"];
			$Y = $Settings["Y"];
			$Name = $Settings["Name"];
			$Size = $Settings["Size"];
			$ShapeSettings = ["Color" => $Settings["Color"],"BorderColor" => $Settings["BorderColor"]];

			switch ($Settings["Shape"]){
				case NODE_SHAPE_CIRCLE:
					$this->myPicture->drawFilledCircle($X, $Y, $Size, $ShapeSettings);
					break;
				case NODE_SHAPE_TRIANGLE:
					# do not hardcode it as the result depends on the PHP precision config
					$cos45 = cos(deg2rad(45)) * $Size;
					$Points = [
						cos(deg2rad(270)) * $Size + $X,	-$Size + $Y,
						$cos45 + $X, $cos45 + $Y,
						($cos45 * -1) + $X, $cos45 + $Y
					];
					$this->myPicture->drawPolygon($Points, $ShapeSettings);
					break;
				case NODE_SHAPE_SQUARE:
					$Size = $Size / 2;
					$this->myPicture->drawFilledRectangle($X - $Size, $Y - $Size, $X + $Size, $Y + $Size, $ShapeSettings);
					break;
			}

			if ($Name != "") {
				$LabelOptions = $this->Labels; # Momchil: Labels contains Type but it is not accepted by drawText
				if ($this->Labels["Type"] == LABEL_LIGHT) {
					$LabelOptions["Align"] = TEXT_ALIGN_BOTTOMLEFT;
					$this->myPicture->drawText($X, $Y, $Name, $LabelOptions);
				} elseif ($this->Labels["Type"] == LABEL_CLASSIC) {
					$LabelOptions["Align"] = TEXT_ALIGN_TOPMIDDLE;
					$LabelOptions["DrawBox"] = TRUE;
					$LabelOptions["BoxAlpha"] = 50;
					$LabelOptions["BorderOffset"] = 4;
					$LabelOptions["RoundedRadius"] = 3;
					$LabelOptions["BoxRounded"] = TRUE;
					$LabelOptions["NoShadow"] = TRUE;
					$this->myPicture->drawText($X, $Y + $Size + $TextPadding, $Name, $LabelOptions);
				}
			}
		}

		/* Draw the vectors */
		if ($DrawVectors) {
			foreach($this->Data as $Settings) {
				if (isset($Settings["Vectors"]) && $Settings["Type"] != NODE_TYPE_CENTRAL) {
					foreach($Settings["Vectors"] as $Vector) {
						$Factor = ($Vector["Type"] == "A") ? $this->MagneticForceA : $this->MagneticForceR;
						$FillColor = ($Vector["Type"] == "A") ? ["FillColor" => new pColor(255,0,0)] : ["FillColor" => new pColor(0,255,0)];
						$X2 = cos(deg2rad($Vector["Angle"])) * $Vector["Force"] * $Factor + $Settings["X"];
						$Y2 = sin(deg2rad($Vector["Angle"])) * $Vector["Force"] * $Factor + $Settings["Y"];
						$this->myPicture->drawArrow( $Settings["X"], $Settings["Y"], $X2, $Y2, $FillColor);
					}
				}
			}
		}

		return ["Pass" => $Jobs,"Conflicts" => $Conflicts];
	}

	/* Return the angle made by a line and the X axis */
	public function getAngle($X1, $Y1, $X2, $Y2)
	{
		#$Opposite = $Y2 - $Y1;
		#$Adjacent = $X2 - $X1;
		$Angle = rad2deg(atan2(($Y2 - $Y1), ($X2 - $X1)));

		return ($Angle > 0) ? $Angle : (360 + $Angle);

	}

	private function intersect($X1, $Y1, $X2, $Y2, $X3, $Y3, $X4, $Y4)
	{
		$A = (($X3 * $Y4 - $X4 * $Y3) * ($X1 - $X2) - ($X1 * $Y2 - $X2 * $Y1) * ($X3 - $X4));
		$B = (($Y1 - $Y2) * ($X3 - $X4) - ($Y3 - $Y4) * ($X1 - $X2));
		if ($B == 0) {
			return FALSE;
		}

		$Xi = $A / $B;
		$C = ($X1 - $X2);
		if ($C == 0) {
			return FALSE;
		}

		$Yi = $Xi * (($Y1 - $Y2) / $C) + (($X1 * $Y2 - $X2 * $Y1) / $C);
		if ($Xi >= min($X1, $X2) && $Xi >= min($X3, $X4) && $Xi <= max($X1, $X2) && $Xi <= max($X3, $X4)) {
			if ($Yi >= min($Y1, $Y2) && $Yi >= min($Y3, $Y4) && $Yi <= max($Y1, $Y2) && $Yi <= max($Y3, $Y4)) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
