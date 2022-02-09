<?php namespace RedeyeVentures\GeoPattern;

use RedeyeVentures\GeoPattern\SVGElements\Polyline;
use RedeyeVentures\GeoPattern\SVGElements\Rectangle;
use RedeyeVentures\GeoPattern\SVGElements\Group;

class GeoPattern {

    protected $string;
    protected $baseColor;
    protected $color;
    protected $generator;

    protected $hash;
    protected $svg;

    protected $patterns = [
        'octogons',
        'overlapping_circles',
        'plus_signs',
        'xes',
        'sine_waves',
        'hexagons',
        'overlapping_rings',
        'plaid',
        'triangles',
        'squares',
        'concentric_circles',
        'diamonds',
        'tessellation',
        'nested_squares',
        'mosaic_squares',
        'triangles_rotated',
        'chevrons',
    ];
    const FILL_COLOR_DARK = '#222';
    const FILL_COLOR_LIGHT = '#ddd';
    const STROKE_COLOR = '#000';
    const STROKE_OPACITY = '0.02';
    const OPACITY_MIN = '0.02';
    const OPACITY_MAX = '0.15';

    function __construct($options=array())
    {
        // Set string if provided. If not, set default.
        if (isset($options['string'])) {
            $this->setString($options['string']);
        } else {
            $this->setString(time());
        }

        // Set base color if provided. If not, set default.
        if (isset($options['baseColor'])) {
            $this->setBaseColor($options['baseColor']);
        } else {
            $this->setBaseColor('#933c3c');
        }

        // Set color if provided.
        if (isset($options['color'])) {
            $this->setColor($options['color']);
        }

        // Set generator if provided. If not, leave null.
        if (isset($options['generator']))
            $this->setGenerator($options['generator']);

        $this->svg = new SVG();
    }

    // Fluent Interfaces
    public function setString($string)
    {
        $this->string = $string;
        $this->hash = sha1($this->string);
        return $this;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    public function setBaseColor($baseColor)
    {
        if(preg_match('/^#[a-f0-9]{6}$/i', $baseColor)) //hex color is valid
        {
            $this->baseColor = $baseColor;
            return $this;
        }
        throw new \InvalidArgumentException("$baseColor is not a valid hex color.");
    }

    public function setColor($color)
    {
        if(preg_match('/^#[a-f0-9]{6}$/i', $color)) //hex color is valid
        {
            $this->color = $color;
            return $this;
        }
        throw new \InvalidArgumentException("$color is not a valid hex color.");
    }

    public function setGenerator($generator)
    {
        $generator = strtolower($generator);
        if (in_array($generator, $this->patterns) || is_null($generator)) {
            $this->generator = $generator;
            return $this;
        }
        throw new \InvalidArgumentException("$generator is not a valid generator type.");
    }

    public function toSVG()
    {
        $this->svg = new SVG();
        $this->generateBackground();
        $this->generatePattern();
        return (string) $this->svg;
    }

    public function toBase64()
    {
        return base64_encode($this->toSVG());
    }

    public function toDataURI()
    {
        return "data:image/svg+xml;base64,{$this->toBase64()}";
    }

    public function toDataURL()
    {
        return "url(\"{$this->toDataURI()}\")";
    }

    public function __toString() {
        return $this->toSVG();
    }

    // Generators
    protected function generateBackground()
    {
        $hueOffset = $this->map($this->hexVal(14, 3), 0, 4095, 0, 359);
        $satOffset = $this->hexVal(17, 1);
        $baseColor = $this->hexToHSL($this->baseColor);
        $color     = $this->color;

        $baseColor['h'] = $baseColor['h'] - $hueOffset;


        if ($satOffset % 2 == 0)
            $baseColor['s'] = $baseColor['s'] + $satOffset/100;
        else
            $baseColor['s'] = $baseColor['s'] - $satOffset/100;

        if (isset($color))
            $rgb = $this->hexToRGB($color);
        else
            $rgb = $this->hslToRGB($baseColor['h'], $baseColor['s'], $baseColor['l']);

        $this->svg->addRectangle(0, 0, "100%", "100%", ['fill' => "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})"]);
    }

    protected function generatePattern()
    {
        if (is_null($this->generator))
            $pattern = $this->patterns[$this->hexVal(20, 1)];
        else
            $pattern = $this->generator;

        $function = 'geo'.str_replace(' ', '', ucwords(str_replace('_', ' ', $pattern)));

        if (method_exists($this, $function))
            $this->$function();
    }

    // Pattern Makers
    protected function geoHexagons()
    {
        $scale = $this->hexVal(0, 1);
        $sideLength = $this->map($scale, 0, 15, 8, 60);
        $hexHeight = $sideLength * sqrt(3);
        $hexWidth = $sideLength * 2;
        $hex = $this->buildHexagonShape($sideLength);
        $this->svg->setWidth(($hexWidth * 3) + ($sideLength * 3))
            ->setHeight($hexHeight * 6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $dy = ($x % 2 == 0) ? ($y * $hexHeight) : ($y*$hexHeight + $hexHeight / 2);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);
                $styles = [
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                    'fill-opacity' => $opacity,
                    'fill' => $fill,
                ];

                $onePointFiveXSideLengthMinusHalfHexWidth = $x * $sideLength * 1.5 - $hexWidth / 2;
                $dyMinusHalfHexHeight = $dy - $hexHeight / 2;
                $this->svg->addPolyline($hex, array_merge($styles, ['transform' => "translate($onePointFiveXSideLengthMinusHalfHexWidth, $dyMinusHalfHexHeight)"]));

                // Add an extra one at top-right, for tiling.
                if ($x == 0) {
                    $onePointFiveSideLengthSixMinusHalfHexWidth = 6 * $sideLength * 1.5 - $hexWidth / 2;
                    $this->svg->addPolyline($hex, array_merge($styles, ['transform' => "translate($onePointFiveSideLengthSixMinusHalfHexWidth, $dyMinusHalfHexHeight)"]));
                }

                // Add an extra row at the end that matches the first row, for tiling.
                if ($y == 0) {
                    $dy2 = ($x % 2 == 0) ? (6 * $hexHeight) : (6 * $hexHeight + $hexHeight / 2);
                    $dy2MinusHalfHexHeight = $dy2 - $hexHeight / 2;
                    $this->svg->addPolyline($hex, array_merge($styles, ['transform' => "translate($onePointFiveXSideLengthMinusHalfHexWidth, $dy2MinusHalfHexHeight)"]));
                }

                // Add an extra one at bottom-right, for tiling.
                if ($x == 0 && $y == 0) {
                    $onePointFiveSideLengthSixMinusHalfHexWidth = 6 * $sideLength * 1.5 - $hexWidth / 2;
                    $fiveHexHeightPlusHalfHexHeight = 5 * $hexHeight + $hexHeight / 2;
                    $this->svg->addPolyline($hex, array_merge($styles, ['transform' => "translate($onePointFiveSideLengthSixMinusHalfHexWidth, $fiveHexHeightPlusHalfHexHeight)"]));
                }

                $i++;
            }
        }
    }

    protected function geoSineWaves()
    {
        $period = floor($this->map($this->hexVal(0, 1), 0, 15, 100, 400));
        $quarterPeriod = $period / 4;
        $xOffset = $period / 4 * 0.7;
        $amplitude = floor($this->map($this->hexVal(1, 1), 0, 15, 30, 100));
        $waveWidth = floor($this->map($this->hexVal(2, 1), 0, 15, 3, 30));
        $amplitudeString = number_format($amplitude);
        $halfPeriod = number_format($period / 2);
        $halfPeriodMinusXOffset = number_format($period / 2 - $xOffset);
        $periodMinusXOffset = number_format($period - $xOffset);
        $twoAmplitude = number_format(2 * $amplitude);
        $onePointFivePeriodMinusXOffset = number_format($period * 1.5 - $xOffset);
        $onePointFivePeriod = number_format($period * 1.5);
        $str = "M0 $amplitudeString C $xOffset 0, $halfPeriodMinusXOffset 0, $halfPeriod $amplitudeString S $periodMinusXOffset $twoAmplitude, $period $amplitudeString S $onePointFivePeriodMinusXOffset 0, $onePointFivePeriod, $amplitudeString";

        $this->svg->setWidth($period)
            ->setHeight($waveWidth*36);
        for ($i = 0; $i <= 35; $i++) {
            $val = $this->hexVal($i, 1);
            $opacity = $this->opacity($val);
            $fill = $this->fillColor($val);
            $styles = [
                'fill' => 'none',
                'stroke' => $fill,
                'style' => [
                    'opacity' => $opacity,
                    'stroke-width' => "{$waveWidth}px"
                ]
            ];

            $iWaveWidthMinusOnePointFiveAmplitude = $waveWidth * $i - $amplitude * 1.5;
            $iWaveWidthMinusOnePointFiveAmplitudePlusThirtySixWaveWidth = $waveWidth * $i - $amplitude * 1.5 + $waveWidth * 36;
            $this->svg->addPath($str, array_merge($styles, ['transform' => "translate(-$quarterPeriod, $iWaveWidthMinusOnePointFiveAmplitude)"]));
            $this->svg->addPath($str, array_merge($styles, ['transform' => "translate(-$quarterPeriod, $iWaveWidthMinusOnePointFiveAmplitudePlusThirtySixWaveWidth)"]));

        }
    }

    protected function geoChevrons()
    {
        $chevronWidth = $this->map($this->hexVal(0, 1), 0, 15, 30, 80);
        $chevronHeight = $this->map($this->hexVal(0, 1), 0, 15, 30, 80);
        $chevron = $this->buildChevronShape($chevronWidth, $chevronHeight);

        $this->svg->setWidth($chevronWidth*6)
            ->setHeight($chevronHeight*6*0.66);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);
                $styles = [
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                    'stroke-width' => '1',
                    'fill-opacity' => $opacity,
                    'fill' => $fill,
                ];

                $group = new Group();
                $group->addItem($chevron[0])
                    ->addItem($chevron[1]);

                $xChevronWidth = $x * $chevronWidth;
                $yPointSixSixChevronHeightMinusHalfChevronHeight = $y * $chevronHeight * 0.66 - $chevronHeight / 2;
                $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xChevronWidth,$yPointSixSixChevronHeightMinusHalfChevronHeight)"]));
                // Add an extra row at the end that matches the first row, for tiling.
                if ($y == 0) {
                    $sixPointSixSixChevronHeightMinusHalfChevronHeight = 6 * $chevronHeight * 0.66 - $chevronHeight / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xChevronWidth,$sixPointSixSixChevronHeightMinusHalfChevronHeight)"]));
                }

                $i++;
            }
        }

    }

    protected function geoPlusSigns()
    {
        $squareSize = $this->map($this->hexVal(0, 1), 0, 15, 10, 25);
        $plusSize = $squareSize * 3;
        $plusShape = $this->buildPlusShape($squareSize);

        $this->svg->setWidth($squareSize*12)
            ->setHeight($squareSize*12);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);
                $dx = ($y % 2 == 0) ? 0 : 1;

                $styles = [
                    'fill' => $fill,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                    'style' => [
                        'fill-opacity' => $opacity,
                    ],
                ];

                $group = new Group();
                $group->addItem($plusShape[0])
                    ->addItem($plusShape[1]);

                $t1 = $x * $plusSize - $x * $squareSize + $dx * $squareSize - $squareSize;
                $t2 = $y * $plusSize - $y * $squareSize - $plusSize / 2;

                $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($t1, $t2)"]));

                // Add an extra column on the right for tiling.
                if ($x == 0) {
                    $xT1 = 4 * $plusSize - $x * $squareSize + $dx * $squareSize - $squareSize;
                    $xT2 = $y * $plusSize - $y * $squareSize - $plusSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xT1, $xT2)"]));
                }

                // Add an extra row on the bottom that matches the first row, for tiling.
                if ($y == 0) {
                    $yT1 = $x * $plusSize - $x * $squareSize + $dx * $squareSize - $squareSize;
                    $yT2 = 4 * $plusSize - $y * $squareSize - $plusSize /2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($yT1, $yT2)"]));
                }

                // Add an extra one at top-right and bottom-right, for tiling.
                if ($x == 0 && $y == 0) {
                    $xyT1 = 4 * $plusSize - $x * $squareSize + $dx * $squareSize - $squareSize;
                    $xyT2 = 4 * $plusSize - $y * $squareSize - $plusSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xyT1, $xyT2)"]));
                }

                $i++;
            }
        }
    }

    protected function geoXes()
    {
        $squareSize = $this->map($this->hexVal(0, 1), 0, 15, 10, 25);
        $xSize = $squareSize * 3 * 0.943;
        $xShape = $this->buildPlusShape($squareSize);

        $this->svg->setWidth($xSize*3)
            ->setHeight($xSize*3);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);
                $dy = ($x % 2 == 0) ? ($y * $xSize - $xSize * 0.5) : ($y * $xSize - $xSize * 0.5 + $xSize / 4);

                $styles = [
                    'fill' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                    ],
                ];

                $group = new Group();
                $group->addItem($xShape[0])
                    ->addItem($xShape[1]);

                $t1 = $x * $xSize / 2 - $xSize / 2;
                $t2 = $dy - $y * $xSize / 2;
                $halfXSize = $xSize / 2;
                $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($t1, $t2) rotate(45, $halfXSize, $halfXSize)"]));

                // Add an extra column on the right for tiling.
                if ($x == 0) {
                    $xT1 = 6 * $xSize / 2 - $xSize / 2;
                    $xT2 = $dy - $y * $xSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xT1, $xT2) rotate(45, $halfXSize, $halfXSize)"]));
                }

                // Add an extra row on the bottom that matches the first row, for tiling.
                if ($y == 0) {
                    $dy = ($x % 2 == 0) ? (6 * $xSize - $xSize / 2) : (6 * $xSize - $xSize / 2 + $xSize / 4);
                    $yT1 = $x * $xSize / 2 - $xSize / 2;
                    $yT2 = $dy - 6 * $xSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($yT1, $yT2) rotate(45, $halfXSize, $halfXSize)"]));
                }

                // These can hang off the bottom, so put a row at the top for tiling.
                if ($y == 5) {
                    $y2T1 = $x * $xSize / 2 - $xSize / 2;
                    $y2T2 = $dy - 11 * $xSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($y2T1, $y2T2) rotate(45, $halfXSize, $halfXSize)"]));
                }

                // Add an extra one at top-right and bottom-right, for tiling.
                if ($x == 0 && $y == 0) {
                    $xyT1 = 6 * $xSize / 2 - $xSize / 2;
                    $xyT2 = $dy - 6 * $xSize / 2;
                    $this->svg->addGroup($group, array_merge($styles, ['transform' => "translate($xyT1, $xyT2) rotate(45, $halfXSize, $halfXSize)"]));
                }

                $i++;
            }
        }
    }

    protected function geoOverlappingCircles()
    {
        $scale = $this->hexVal(0, 1);
        $diameter = $this->map($scale, 0, 15, 25, 200);
        $radius = $diameter/2;

        $this->svg->setWidth($radius*6)
            ->setHeight($radius*6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);
                $styles = [
                    'fill' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                    ],
                ];

                $this->svg->addCircle($x*$radius, $y*$radius, $radius, $styles);

                // Add an extra one at top-right, for tiling.
                if ($x == 0)
                    $this->svg->addCircle(6*$radius, $y*$radius, $radius, $styles);

                // Add an extra row at the end that matches the first row, for tiling.
                if ($y == 0)
                    $this->svg->addCircle($x*$radius, 6*$radius, $radius, $styles);

                // Add an extra one at bottom-right, for tiling.
                if ($x == 0 && $y == 0)
                    $this->svg->addCircle(6*$radius, 6*$radius, $radius, $styles);

                $i++;
            }
        }
    }

    protected function geoOctogons()
    {
        $squareSize = $this->map($this->hexVal(0, 1), 0, 15, 10, 60);
        $tile = $this->buildOctogonShape($squareSize);

        $this->svg->setWidth($squareSize*6)
            ->setHeight($squareSize*6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $xSquareSize = $x * $squareSize;
                $ySquareSize = $y * $squareSize;

                $this->svg->addPolyline($tile, [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                    'transform' => "translate($xSquareSize, $ySquareSize)",
                ]);

                $i++;
            }
        }

    }

    protected function geoSquares()
    {
        $squareSize = $this->map($this->hexVal(0, 1), 0, 15, 10, 60);

        $this->svg->setWidth($squareSize*6)
            ->setHeight($squareSize*6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $this->svg->addRectangle($x*$squareSize, $y*$squareSize, $squareSize, $squareSize, [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                ]);

                $i++;
            }
        }

    }

    protected function geoConcentricCircles()
    {
        $scale = $this->hexVal(0, 1);
        $ringSize = $this->map($scale, 0, 15, 10, 60);
        $strokeWidth = $ringSize / 5;

        $this->svg->setWidth(($ringSize + $strokeWidth)*6)
            ->setHeight(($ringSize + $strokeWidth)*6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $cx = $x * $ringSize + $x * $strokeWidth + ($ringSize + $strokeWidth) / 2;
                $cy = $y * $ringSize + $y * $strokeWidth + ($ringSize + $strokeWidth) / 2;
                $halfRingSize = $ringSize / 2;

                $this->svg->addCircle($cx, $cy, $halfRingSize, [
                    'fill' => 'none',
                    'stroke' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                        'stroke-width' => "{$strokeWidth}px",
                    ],
                ]);

                $val = $this->hexVal(39-$i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $quarterRingSize = $ringSize / 4;

                $this->svg->addCircle($cx, $cy, $quarterRingSize, [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                ]);

                $i++;
            }
        }
    }

    protected function geoOverlappingRings()
    {
        $scale = $this->hexVal(0, 1);
        $ringSize = $this->map($scale, 0, 15, 10, 60);
        $strokeWidth = $ringSize / 4;

        $this->svg->setWidth($ringSize*6)
            ->setHeight($ringSize*6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => 'none',
                    'stroke' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                        'stroke-width' => "{$strokeWidth}px",
                    ],
                ];

                $ringSizeMinusHalfStrokeWidth = $ringSize - $strokeWidth / 2;

                $this->svg->addCircle($x*$ringSize, $y*$ringSize, $ringSizeMinusHalfStrokeWidth, $styles);

                // Add an extra one at top-right, for tiling.
                if ($x == 0)
                    $this->svg->addCircle(6*$ringSize, $y*$ringSize, $ringSizeMinusHalfStrokeWidth, $styles);

                // Add an extra row at the end that matches the first row, for tiling.
                if ($y == 0)
                    $this->svg->addCircle($x*$ringSize, 6*$ringSize, $ringSizeMinusHalfStrokeWidth, $styles);

                // Add an extra one at bottom-right, for tiling.
                if ($x == 0 && $y == 0)
                    $this->svg->addCircle(6*$ringSize, 6*$ringSize, $ringSizeMinusHalfStrokeWidth, $styles);

                $i++;
            }
        }
    }

    protected function geoTriangles()
    {
        $scale = $this->hexVal(0, 1);
        $sideLength = $this->map($scale, 0 ,15, 15, 80);
        $triangleHeight = $sideLength / 2 * sqrt(3);
        $triangle = $this->buildTriangleShape($sideLength, $triangleHeight);

        $this->svg->setWidth($sideLength * 3)
            ->setHeight($triangleHeight * 6);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                ];

                $rotation = '';
                if ($y % 2 == 0)
                    $rotation = ($x % 2 == 0) ? 180 : 0;
                else
                    $rotation = ($x % 2 != 0) ? 180 : 0;

                $halfSideLength = $sideLength / 2;
                $halfTriangleHeight = $triangleHeight / 2;
                $yTriangleHeight = $triangleHeight * $y;

                $t1 = $x * $sideLength * 0.5 - $sideLength / 2;
                $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($t1, $yTriangleHeight) rotate($rotation, $halfSideLength, $halfTriangleHeight)"]));

                // Add an extra one at top-right, for tiling.
                if ($x == 0)
                {
                    $xT1 = 6 * $sideLength * 0.5 - $sideLength / 2;
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xT1, $yTriangleHeight) rotate($rotation, $halfSideLength, $halfTriangleHeight)"]));
                }

                $i++;
            }
        }

    }

    protected function geoTrianglesRotated()
    {
        $scale = $this->hexVal(0, 1);
        $sideLength = $this->map($scale, 0 ,15, 15, 80);
        $triangleWidth = $sideLength / 2 * sqrt(3);
        $triangle = $this->buildRotatedTriangleShape($sideLength, $triangleWidth);

        $this->svg->setWidth($triangleWidth * 6)
            ->setHeight($sideLength * 3);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                ];

                $rotation = '';
                if ($y % 2 == 0)
                    $rotation = ($x % 2 == 0) ? 180 : 0;
                else
                    $rotation = ($x % 2 != 0) ? 180 : 0;

                $halfSideLength = $sideLength / 2;
                $halfTriangleWidth = $triangleWidth / 2;
                $xTriangleWidth = $x * $triangleWidth;

                $t1 = $y * $sideLength * 0.5 - $sideLength / 2;
                $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xTriangleWidth, $t1) rotate($rotation, $halfTriangleWidth, $halfSideLength)"]));

                // Add an extra one at top-right, for tiling.
                if ($y == 0)
                {
                    $yT1 = 6 * $sideLength * 0.5 - $sideLength / 2;
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xTriangleWidth, $yT1) rotate($rotation, $halfTriangleWidth, $halfSideLength)"]));
                }

                $i++;
            }
        }

    }

    protected function geoDiamonds()
    {
        $diamondWidth = $this->map($this->hexVal(0, 1), 0, 15, 10, 50);
        $diamondHeight = $this->map($this->hexVal(1, 1), 0, 15, 10, 50);
        $diamond = $this->buildDiamondShape($diamondWidth, $diamondHeight);

        $this->svg->setWidth($diamondWidth*6)
            ->setHeight($diamondHeight*3);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => $fill,
                    'fill-opacity' => $opacity,
                    'stroke' => self::STROKE_COLOR,
                    'stroke-opacity' => self::STROKE_OPACITY,
                ];

                $dx = ($y % 2 == 0) ? 0 : ($diamondWidth / 2);

                $t1 = $x * $diamondWidth - $diamondWidth / 2 + $dx;
                $t2 = $diamondHeight / 2 * $y - $diamondHeight / 2;
                $this->svg->addPolyline($diamond, array_merge($styles, ['transform' => "translate($t1, $t2)"]));

                // Add an extra one at top-right, for tiling.
                if ($x == 0)
                {
                    $xT1 = 6 * $diamondWidth - $diamondWidth / 2 + $dx;
                    $xT2 = $diamondHeight / 2 * $y - $diamondHeight / 2;
                    $this->svg->addPolyline($diamond, array_merge($styles, ['transform' => "translate($xT1, $xT2)"]));
                }

                // Add an extra row at the end that matches the first row, for tiling.
                if ($y == 0)
                {
                    $yT1 = $x * $diamondWidth - $diamondWidth / 2 + $dx;
                    $yT2 = $diamondHeight / 2 * 6 - $diamondHeight / 2;
                    $this->svg->addPolyline($diamond, array_merge($styles, ['transform' => "translate($yT1, $yT2)"]));
                }

                // Add an extra one at bottom-right, for tiling.
                if ($x == 0 && $y == 0)
                {
                    $xyT1 = 6 * $diamondWidth - $diamondWidth / 2 + $dx;
                    $xyT2 = $diamondHeight / 2 * 6 - $diamondHeight / 2;
                    $this->svg->addPolyline($diamond, array_merge($styles, ['transform' => "translate($xyT1, $xyT2)"]));
                }

                $i++;
            }
        }
    }

    protected function geoNestedSquares()
    {
        $blockSize = $this->map($this->hexVal(0, 1), 0, 15, 4, 12);
        $squareSize = $blockSize * 7;
        $dimension = ($squareSize + $blockSize) * 6 + $blockSize * 6;

        $this->svg->setWidth($dimension)
            ->setHeight($dimension);

        $i = 0;
        for ($y = 0; $y <= 5; $y++) {
            for ($x = 0; $x <= 5; $x++) {
                $val = $this->hexVal($i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => 'none',
                    'stroke' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                        'stroke-width' => "{$blockSize}px",
                    ],
                ];

                $rX = $x * $squareSize + $x * $blockSize * 2 + $blockSize / 2;
                $rY = $y * $squareSize + $y * $blockSize * 2 + $blockSize / 2;

                $this->svg->addRectangle($rX, $rY, $squareSize, $squareSize, $styles);

                $val = $this->hexVal(39-$i, 1);
                $opacity = $this->opacity($val);
                $fill = $this->fillColor($val);

                $styles = [
                    'fill' => 'none',
                    'stroke' => $fill,
                    'style' => [
                        'opacity' => $opacity,
                        'stroke-width' => "{$blockSize}px",
                    ],
                ];

                $rX2 = $x * $squareSize + $x * $blockSize * 2 + $blockSize / 2 + $blockSize * 2;
                $rY2 = $y * $squareSize + $y * $blockSize * 2 + $blockSize / 2 + $blockSize * 2;

                $this->svg->addRectangle($rX2, $rY2, $blockSize * 3, $blockSize * 3, $styles);

                $i++;
            }
        }
    }

    protected function geoMosaicSquares()
    {
        $triangleSize = $this->map($this->hexVal(0, 1), 0, 15, 15, 50);

        $this->svg->setWidth($triangleSize*8)
            ->setHeight($triangleSize*8);

        $i = 0;
        for ($y = 0; $y <= 3; $y++) {
            for ($x = 0; $x <= 3; $x++) {
                if ($x % 2 == 0)
                {
                    if ($y % 2 == 0)
                        $this->drawOuterMosaicTile($x*$triangleSize*2, $y*$triangleSize*2, $triangleSize, $this->hexVal($i, 1));
                    else
                        $this->drawInnerMosaicTile($x*$triangleSize*2, $y*$triangleSize*2, $triangleSize, [$this->hexVal($i, 1), $this->hexVal($i+1, 1)]);
                }
                else
                {
                    if ($y % 2 == 0)
                        $this->drawInnerMosaicTile($x*$triangleSize*2, $y*$triangleSize*2, $triangleSize, [$this->hexVal($i, 1), $this->hexVal($i+1, 1)]);
                    else
                        $this->drawOuterMosaicTile($x*$triangleSize*2, $y*$triangleSize*2, $triangleSize, $this->hexVal($i, 1));
                }
                $i++;
            }
        }

    }

    protected function geoPlaid()
    {
        $height = 0;
        $width = 0;

        // Horizontal Stripes
        $i = 0;
        $times = 0;
        while ($times++ <= 18)
        {
            $space = $this->hexVal($i, 1);
            $height += $space + 5;

            $val = $this->hexVal($i+1, 1);
            $opacity = $this->opacity($val);
            $fill = $this->fillColor($val);
            $stripeHeight = $val + 5;

            $this->svg->addRectangle(0, $height, "100%", $stripeHeight, [
                'opacity' => $opacity,
                'fill' => $fill,
            ]);
            $height += $stripeHeight;
            $i += 2;
        }

        // Vertical Stripes
        $i = 0;
        $times = 0;
        while ($times++ <= 18)
        {
            $space = $this->hexVal($i, 1);
            $width += $space + 5;

            $val = $this->hexVal($i+1, 1);
            $opacity = $this->opacity($val);
            $fill = $this->fillColor($val);
            $stripeWidth = $val + 5;

            $this->svg->addRectangle($width, 0, $stripeWidth, "100%", [
                'opacity' => $opacity,
                'fill' => $fill,
            ]);
            $width += $stripeWidth;
            $i += 2;
        }

        $this->svg->setWidth($width)
            ->setHeight($height);

    }

    protected function geoTessellation()
    {
        $sideLength = $this->map($this->hexVal(0, 1), 0, 15, 5, 40);
        $hexHeight = $sideLength * sqrt(3);
        $hexWidth = $sideLength * 2;
        $triangleHeight = $sideLength / 2 * sqrt(3);
        $triangle = $this->buildRotatedTriangleShape($sideLength, $triangleHeight);
        $tileWidth = $sideLength * 3 + $triangleHeight * 2;
        $tileHeight = ($hexHeight * 2) + ($sideLength * 2);

        $this->svg->setWidth($tileWidth)
            ->setHeight($tileHeight);

        // Doing these variables up here, so we only have to calculate them once.
        $halfSideLength = $sideLength / 2;
        $negativeHalfSideLength = -$sideLength / 2;
        $halfTriangleHeight = $triangleHeight / 2;
        $halfHexHeight = $hexHeight / 2;
        $tileHeightPlusHalfSideLength = $tileHeight + $sideLength / 2;
        $halfTileHeightMinusHalfSideLength = $tileHeight / 2 - $sideLength / 2;
        $halfTileWidthPlusHalfSideLength = $tileWidth / 2 + $sideLength / 2;
        $tileWidthMinusHalfTileWidthMinusHalfSideLength = $tileWidth - $tileWidth/2 - $sideLength/2;
        $tileWidthMinusHalfSideLength = $tileWidth - $sideLength / 2;
        $tileHeightMinusHalfHexHeight = $tileHeight - $hexHeight / 2;
        $negativeTileWidthPlusHalfSideLength = -$tileWidth + $sideLength / 2;
        $halfTileHeightMinusHalfSideLengthMinusSideLength = $tileHeight/2-$sideLength/2-$sideLength;
        $negativeTileHeightPlusHalfTileHeightMinusHalfSideLengthMinusSideLength = -$tileHeight+$tileHeight/2-$sideLength/2-$sideLength;
        $negativeTileHeightPlusHalfSideLength = -$tileHeight + $sideLength / 2;
        for ($i = 0; $i <= 19; $i++) {
            $val = $this->hexVal($i, 1);
            $opacity = $this->opacity($val);
            $fill = $this->fillColor($val);

            $styles = [
                'stroke' => self::STROKE_COLOR,
                'stroke-opacity' => self::STROKE_OPACITY,
                'fill' => $fill,
                'fill-opacity' => $opacity,
                'stroke-width' => 1,
            ];

            switch ($i) {
                case 0: # all 4 corners
                    $this->svg->addRectangle(-$sideLength/2, -$sideLength/2, $sideLength, $sideLength, $styles);
                    $this->svg->addRectangle($tileWidth-$sideLength/2, -$sideLength/2, $sideLength, $sideLength, $styles);
                    $this->svg->addRectangle(-$sideLength/2, $tileHeight-$sideLength/2, $sideLength, $sideLength, $styles);
                    $this->svg->addRectangle($tileWidth-$sideLength/2, $tileHeight-$sideLength/2, $sideLength, $sideLength, $styles);
                    break;
                case 1: # center / top square
                    $this->svg->addRectangle($hexWidth/2+$triangleHeight, $hexHeight/2, $sideLength, $sideLength, $styles);
                    break;
                case 2: # side squares
                    $this->svg->addRectangle(-$sideLength/2, $tileHeight/2-$sideLength/2, $sideLength, $sideLength, $styles);
                    $this->svg->addRectangle($tileWidth-$sideLength/2, $tileHeight/2-$sideLength/2, $sideLength, $sideLength, $styles);
                    break;
                case 3: # center / bottom square
                    $this->svg->addRectangle($hexWidth/2+$triangleHeight, $hexHeight*1.5+$sideLength, $sideLength, $sideLength, $styles);
                    break;
                case 4: # left top / bottom triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($halfSideLength, $negativeHalfSideLength) rotate(0, $halfSideLength, $halfTriangleHeight)"]));
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($halfSideLength, $tileHeightPlusHalfSideLength) rotate(0, $halfSideLength, $halfTriangleHeight) scale(1, -1)"]));
                    break;
                case 5: # right top / bottom triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($tileWidthMinusHalfSideLength, $negativeHalfSideLength) rotate(0, $halfSideLength, $halfTriangleHeight) scale(-1, 1)"]));
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($tileWidthMinusHalfSideLength, $tileHeightPlusHalfSideLength) rotate(0, $halfSideLength, $halfTriangleHeight) scale(-1, -1)"]));
                    break;
                case 6: # center / top / right triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($halfTileWidthPlusHalfSideLength, $halfHexHeight)"]));
                    break;
                case 7: # center / top / left triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($tileWidthMinusHalfTileWidthMinusHalfSideLength, $halfHexHeight) scale(-1, 1)"]));
                    break;
                case 8: # center / bottom / right triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($halfTileWidthPlusHalfSideLength, $tileHeightMinusHalfHexHeight) scale(1, -1)"]));
                    break;
                case 9: # center / bottom / left triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($tileWidthMinusHalfTileWidthMinusHalfSideLength, $tileHeightMinusHalfHexHeight) scale(-1, -1)"]));
                    break;
                case 10: # left / middle triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($halfSideLength, $halfTileHeightMinusHalfSideLength)"]));
                    break;
                case 11: # right / middle triangle
                    $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($tileWidthMinusHalfSideLength, $halfTileHeightMinusHalfSideLength) scale(-1, 1)"]));
                    break;
                case 12: # left / top square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "translate($halfSideLength, $halfSideLength) rotate(-30, 0, 0)"]));
                    break;
                case 13: # right / top square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(-1, 1) translate($negativeTileWidthPlusHalfSideLength, $halfSideLength) rotate(-30, 0, 0)"]));
                    break;
                case 14: # left / center-top square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "translate($halfSideLength, $halfTileHeightMinusHalfSideLengthMinusSideLength) rotate(30, 0, $sideLength)"]));
                    break;
                case 15: # right / center-top square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(-1, 1) translate($negativeTileWidthPlusHalfSideLength, $halfTileHeightMinusHalfSideLengthMinusSideLength) rotate(30, 0, $sideLength)"]));
                    break;
                case 16: # left / center-top square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(1, -1) translate($halfSideLength, $negativeTileHeightPlusHalfTileHeightMinusHalfSideLengthMinusSideLength) rotate(30, 0, $sideLength)"]));
                    break;
                case 17: # right / center-bottom square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(-1, -1) translate($negativeTileWidthPlusHalfSideLength, $negativeTileHeightPlusHalfTileHeightMinusHalfSideLengthMinusSideLength) rotate(30, 0, $sideLength)"]));
                    break;
                case 18: # left / bottom square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(1, -1) translate($halfSideLength, $negativeTileHeightPlusHalfSideLength) rotate(-30, 0, 0)"]));
                    break;
                case 19: # right / bottom square
                    $this->svg->addRectangle(0, 0, $sideLength, $sideLength, array_merge($styles, ['transform' => "scale(-1, -1) translate($negativeTileWidthPlusHalfSideLength, $negativeTileHeightPlusHalfSideLength) rotate(-30, 0, 0)"]));
                    break;
            }
        }
    }

    // build* functions
    protected function buildChevronShape($width, $height)
    {
        $e = $height * 0.66;
        $halfWidth = $width / 2;
        $heightMinusE = $height - $e;
        return [
            new Polyline("0,0,$halfWidth,$heightMinusE,$halfWidth,$height,0,$e,0,0"),
            new Polyline("$halfWidth,$heightMinusE,$width,0,$width,$e,$halfWidth,$height,$halfWidth,$heightMinusE")
        ];
    }

    protected function buildOctogonShape($squareSize)
    {
        $s = $squareSize;
        $c = $s * 0.33;
        $sMinusC = $s - $c;
        return "$c,0,$sMinusC,0,$s,$c,$s,$sMinusC,$sMinusC,$s,$c,$s,0,$sMinusC,0,$c,$c,0";
    }

    protected function buildHexagonShape($sideLength)
    {
        $c = $sideLength;
        $a = $c/2;
        $b = sin(60 * M_PI / 180) * $c;
        $twoB = $b * 2;
        $twoC = $c * 2;
        $aPlusC = $a + $c;
        return "0,$b,$a,0,$aPlusC,0,$twoC,$b,$aPlusC,$twoB,$a,$twoB,0,$b";
    }

    protected function buildPlusShape($squareSize)
    {
        return [
            new Rectangle($squareSize, 0, $squareSize, $squareSize*3),
            new Rectangle(0, $squareSize, $squareSize*3, $squareSize),
        ];
    }

    protected function buildTriangleShape($sideLength, $height)
    {
        $halfWidth = $sideLength / 2;
        return "$halfWidth, 0, $sideLength, $height, 0, $height, $halfWidth, 0";
    }

    protected function buildRotatedTriangleShape($sideLength, $width)
    {
        $halfHeight = $sideLength / 2;
        return "0, 0, $width, $halfHeight, 0, $sideLength, 0, 0";
    }

    protected function buildRightTriangleShape($sideLength)
    {
        return "0, 0, $sideLength, $sideLength, 0, $sideLength, 0, 0";
    }

    protected function buildDiamondShape($width, $height)
    {
        $halfWidth = $width / 2;
        $halfHeight = $height / 2;
        return "$halfWidth, 0, $width, $halfHeight, $halfWidth, $height, 0, $halfHeight";
    }

    // draw* functions
    protected function drawInnerMosaicTile($x, $y, $triangleSize, $vals)
    {
        $triangle = $this->buildRightTriangleShape($triangleSize);
        $opacity = $this->opacity($vals[0]);
        $fill = $this->fillColor($vals[0]);
        $styles = [
            'stroke' => self::STROKE_COLOR,
            'stroke-opacity' => self::STROKE_OPACITY,
            'fill-opacity' => $opacity,
            'fill' => $fill,
        ];
        $xPlusTriangleSize = $x + $triangleSize;
        $yPlusTwoTriangleSize = $y + $triangleSize * 2;
        $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTriangleSize, $y) scale(-1, 1)"]))
            ->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTriangleSize, $yPlusTwoTriangleSize) scale(1, -1)"]));

        $opacity = $this->opacity($vals[1]);
        $fill = $this->fillColor($vals[1]);
        $styles = [
            'stroke' => self::STROKE_COLOR,
            'stroke-opacity' => self::STROKE_OPACITY,
            'fill-opacity' => $opacity,
            'fill' => $fill,
        ];
        $xPlusTriangleSize = $x + $triangleSize;
        $yPlusTwoTriangleSize = $y + $triangleSize * 2;
        $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTriangleSize, $yPlusTwoTriangleSize) scale(-1, -1)"]))
            ->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTriangleSize, $y) scale(1, 1)"]));

        return $this;
    }

    protected function drawOuterMosaicTile($x, $y, $triangleSize, $val)
    {
        $triangle = $this->buildRightTriangleShape($triangleSize);
        $opacity = $this->opacity($val);
        $fill = $this->fillColor($val);
        $styles = [
            'stroke' => self::STROKE_COLOR,
            'stroke-opacity' => self::STROKE_OPACITY,
            'fill-opacity' => $opacity,
            'fill' => $fill,
        ];

        $yPlusTriangleSize = $y + $triangleSize;
        $xPlusTwoTriangleSize = $x + $triangleSize * 2;
        $this->svg->addPolyline($triangle, array_merge($styles, ['transform' => "translate($x, $yPlusTriangleSize) scale(1, -1)"]))
            ->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTwoTriangleSize, $yPlusTriangleSize) scale(-1, -1)"]))
            ->addPolyline($triangle, array_merge($styles, ['transform' => "translate($x, $yPlusTriangleSize) scale(1, 1)"]))
            ->addPolyline($triangle, array_merge($styles, ['transform' => "translate($xPlusTwoTriangleSize, $yPlusTriangleSize) scale(-1, 1)"]));
    }

    // Utility Functions

    protected function fillColor($val)
    {
        return ($val % 2 == 0) ? self::FILL_COLOR_LIGHT : self::FILL_COLOR_DARK;
    }

    protected function opacity($val)
    {
        return $this->map($val, 0, 15, self::OPACITY_MIN, self::OPACITY_MAX);
    }

    protected function hexVal($index, $len)
    {
        return hexdec(substr($this->hash, $index, $len));
    }

    // PHP implementation of Processing's map function
    // http://processing.org/reference/map_.html
    protected function map($value, $vMin, $vMax, $dMin, $dMax)
    {
        $vValue = floatval($value);
        $vRange = $vMax - $vMin;
        $dRange = $dMax - $dMin;
        return ($vValue - $vMin) * $dRange / $vRange + $dMin;
    }

    // Color Functions
    protected function hexToHSL($color)
    {
        $color = trim($color, '#');
        $R = hexdec($color[0].$color[1]);
        $G = hexdec($color[2].$color[3]);
        $B = hexdec($color[4].$color[5]);

        $HSL = array();

        $var_R = ($R / 255);
        $var_G = ($G / 255);
        $var_B = ($B / 255);

        $var_Min = min($var_R, $var_G, $var_B);
        $var_Max = max($var_R, $var_G, $var_B);
        $del_Max = $var_Max - $var_Min;

        $L = ($var_Max + $var_Min)/2;

        if ($del_Max == 0)
        {
            $H = 0;
            $S = 0;
        }
        else
        {
            if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
            else            $S = $del_Max / ( 2 - $var_Max - $var_Min );

            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

            if      ($var_R == $var_Max) $H = $del_B - $del_G;
            else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

            if ($H<0) $H++;
            if ($H>1) $H--;
        }

        $HSL['h'] = ($H*360);
        $HSL['s'] = $S;
        $HSL['l'] = $L;

        return $HSL;
    }

    protected function hexToRGB($hex) {
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    protected function rgbToHSL($r, $g, $b) {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        if ($max == $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }
            $h /= 6;
        }
        $h = floor($h * 360);
        $s = floor($s * 100);
        $l = floor($l * 100);
        return ['h' => $h, 's' => $s, 'l' => $l];
    }

    protected function hslToRGB ($h, $s, $l) {
        $h += 360;
        $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
        $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
        $m = $l - ( $c / 2 );

        if ( $h < 60 ) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ( $h < 120 ) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ( $h < 180 ) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ( $h < 240 ) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ( $h < 300 ) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }

        $r = ( $r + $m ) * 255;
        $g = ( $g + $m ) * 255;
        $b = ( $b + $m  ) * 255;

        return array( 'r' => floor( $r ), 'g' => floor( $g ), 'b' => floor( $b ) );

    }

    //NOT USED
    protected function rgbToHex($r, $g, $b) {
        $hex = "#";
        $hex .= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
        return $hex;
    }


}
