<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudNaturalLanguage;

class XPSVisualization extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const COLOR_MAP_COLOR_MAP_UNSPECIFIED = 'COLOR_MAP_UNSPECIFIED';
  /**
   * Positive: green. Negative: pink.
   */
  public const COLOR_MAP_PINK_GREEN = 'PINK_GREEN';
  /**
   * Viridis color map: A perceptually uniform color mapping which is easier to
   * see by those with colorblindness and progresses from yellow to green to
   * blue. Positive: yellow. Negative: blue.
   */
  public const COLOR_MAP_VIRIDIS = 'VIRIDIS';
  /**
   * Positive: red. Negative: red.
   */
  public const COLOR_MAP_RED = 'RED';
  /**
   * Positive: green. Negative: green.
   */
  public const COLOR_MAP_GREEN = 'GREEN';
  /**
   * Positive: green. Negative: red.
   */
  public const COLOR_MAP_RED_GREEN = 'RED_GREEN';
  /**
   * PiYG palette.
   */
  public const COLOR_MAP_PINK_WHITE_GREEN = 'PINK_WHITE_GREEN';
  /**
   * Default value. This is the same as NONE.
   */
  public const OVERLAY_TYPE_OVERLAY_TYPE_UNSPECIFIED = 'OVERLAY_TYPE_UNSPECIFIED';
  /**
   * No overlay.
   */
  public const OVERLAY_TYPE_NONE = 'NONE';
  /**
   * The attributions are shown on top of the original image.
   */
  public const OVERLAY_TYPE_ORIGINAL = 'ORIGINAL';
  /**
   * The attributions are shown on top of grayscaled version of the original
   * image.
   */
  public const OVERLAY_TYPE_GRAYSCALE = 'GRAYSCALE';
  /**
   * The attributions are used as a mask to reveal predictive parts of the image
   * and hide the un-predictive parts.
   */
  public const OVERLAY_TYPE_MASK_BLACK = 'MASK_BLACK';
  /**
   * Default value. This is the same as POSITIVE.
   */
  public const POLARITY_POLARITY_UNSPECIFIED = 'POLARITY_UNSPECIFIED';
  /**
   * Highlights the pixels/outlines that were most influential to the model's
   * prediction.
   */
  public const POLARITY_POSITIVE = 'POSITIVE';
  /**
   * Setting polarity to negative highlights areas that does not lead to the
   * models's current prediction.
   */
  public const POLARITY_NEGATIVE = 'NEGATIVE';
  /**
   * Shows both positive and negative attributions.
   */
  public const POLARITY_BOTH = 'BOTH';
  /**
   * Should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Shows which pixel contributed to the image prediction.
   */
  public const TYPE_PIXELS = 'PIXELS';
  /**
   * Shows which region contributed to the image prediction by outlining the
   * region.
   */
  public const TYPE_OUTLINES = 'OUTLINES';
  /**
   * Excludes attributions below the specified percentile, from the highlighted
   * areas. Defaults to 62.
   *
   * @var float
   */
  public $clipPercentLowerbound;
  /**
   * Excludes attributions above the specified percentile from the highlighted
   * areas. Using the clip_percent_upperbound and clip_percent_lowerbound
   * together can be useful for filtering out noise and making it easier to see
   * areas of strong attribution. Defaults to 99.9.
   *
   * @var float
   */
  public $clipPercentUpperbound;
  /**
   * The color scheme used for the highlighted areas. Defaults to PINK_GREEN for
   * Integrated Gradients attribution, which shows positive attributions in
   * green and negative in pink. Defaults to VIRIDIS for XRAI attribution, which
   * highlights the most influential regions in yellow and the least influential
   * in blue.
   *
   * @var string
   */
  public $colorMap;
  /**
   * How the original image is displayed in the visualization. Adjusting the
   * overlay can help increase visual clarity if the original image makes it
   * difficult to view the visualization. Defaults to NONE.
   *
   * @var string
   */
  public $overlayType;
  /**
   * Whether to only highlight pixels with positive contributions, negative or
   * both. Defaults to POSITIVE.
   *
   * @var string
   */
  public $polarity;
  /**
   * Type of the image visualization. Only applicable to Integrated Gradients
   * attribution. OUTLINES shows regions of attribution, while PIXELS shows per-
   * pixel attribution. Defaults to OUTLINES.
   *
   * @var string
   */
  public $type;

  /**
   * Excludes attributions below the specified percentile, from the highlighted
   * areas. Defaults to 62.
   *
   * @param float $clipPercentLowerbound
   */
  public function setClipPercentLowerbound($clipPercentLowerbound)
  {
    $this->clipPercentLowerbound = $clipPercentLowerbound;
  }
  /**
   * @return float
   */
  public function getClipPercentLowerbound()
  {
    return $this->clipPercentLowerbound;
  }
  /**
   * Excludes attributions above the specified percentile from the highlighted
   * areas. Using the clip_percent_upperbound and clip_percent_lowerbound
   * together can be useful for filtering out noise and making it easier to see
   * areas of strong attribution. Defaults to 99.9.
   *
   * @param float $clipPercentUpperbound
   */
  public function setClipPercentUpperbound($clipPercentUpperbound)
  {
    $this->clipPercentUpperbound = $clipPercentUpperbound;
  }
  /**
   * @return float
   */
  public function getClipPercentUpperbound()
  {
    return $this->clipPercentUpperbound;
  }
  /**
   * The color scheme used for the highlighted areas. Defaults to PINK_GREEN for
   * Integrated Gradients attribution, which shows positive attributions in
   * green and negative in pink. Defaults to VIRIDIS for XRAI attribution, which
   * highlights the most influential regions in yellow and the least influential
   * in blue.
   *
   * Accepted values: COLOR_MAP_UNSPECIFIED, PINK_GREEN, VIRIDIS, RED, GREEN,
   * RED_GREEN, PINK_WHITE_GREEN
   *
   * @param self::COLOR_MAP_* $colorMap
   */
  public function setColorMap($colorMap)
  {
    $this->colorMap = $colorMap;
  }
  /**
   * @return self::COLOR_MAP_*
   */
  public function getColorMap()
  {
    return $this->colorMap;
  }
  /**
   * How the original image is displayed in the visualization. Adjusting the
   * overlay can help increase visual clarity if the original image makes it
   * difficult to view the visualization. Defaults to NONE.
   *
   * Accepted values: OVERLAY_TYPE_UNSPECIFIED, NONE, ORIGINAL, GRAYSCALE,
   * MASK_BLACK
   *
   * @param self::OVERLAY_TYPE_* $overlayType
   */
  public function setOverlayType($overlayType)
  {
    $this->overlayType = $overlayType;
  }
  /**
   * @return self::OVERLAY_TYPE_*
   */
  public function getOverlayType()
  {
    return $this->overlayType;
  }
  /**
   * Whether to only highlight pixels with positive contributions, negative or
   * both. Defaults to POSITIVE.
   *
   * Accepted values: POLARITY_UNSPECIFIED, POSITIVE, NEGATIVE, BOTH
   *
   * @param self::POLARITY_* $polarity
   */
  public function setPolarity($polarity)
  {
    $this->polarity = $polarity;
  }
  /**
   * @return self::POLARITY_*
   */
  public function getPolarity()
  {
    return $this->polarity;
  }
  /**
   * Type of the image visualization. Only applicable to Integrated Gradients
   * attribution. OUTLINES shows regions of attribution, while PIXELS shows per-
   * pixel attribution. Defaults to OUTLINES.
   *
   * Accepted values: TYPE_UNSPECIFIED, PIXELS, OUTLINES
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVisualization::class, 'Google_Service_CloudNaturalLanguage_XPSVisualization');
