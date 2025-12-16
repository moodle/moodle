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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentPageLayout extends \Google\Model
{
  /**
   * Unspecified orientation.
   */
  public const ORIENTATION_ORIENTATION_UNSPECIFIED = 'ORIENTATION_UNSPECIFIED';
  /**
   * Orientation is aligned with page up.
   */
  public const ORIENTATION_PAGE_UP = 'PAGE_UP';
  /**
   * Orientation is aligned with page right. Turn the head 90 degrees clockwise
   * from upright to read.
   */
  public const ORIENTATION_PAGE_RIGHT = 'PAGE_RIGHT';
  /**
   * Orientation is aligned with page down. Turn the head 180 degrees from
   * upright to read.
   */
  public const ORIENTATION_PAGE_DOWN = 'PAGE_DOWN';
  /**
   * Orientation is aligned with page left. Turn the head 90 degrees
   * counterclockwise from upright to read.
   */
  public const ORIENTATION_PAGE_LEFT = 'PAGE_LEFT';
  protected $boundingPolyType = GoogleCloudDocumentaiV1BoundingPoly::class;
  protected $boundingPolyDataType = '';
  /**
   * Confidence of the current Layout within context of the object this layout
   * is for. e.g. confidence can be for a single token, a table, a visual
   * element, etc. depending on context. Range `[0, 1]`.
   *
   * @var float
   */
  public $confidence;
  /**
   * Detected orientation for the Layout.
   *
   * @var string
   */
  public $orientation;
  protected $textAnchorType = GoogleCloudDocumentaiV1DocumentTextAnchor::class;
  protected $textAnchorDataType = '';

  /**
   * The bounding polygon for the Layout.
   *
   * @param GoogleCloudDocumentaiV1BoundingPoly $boundingPoly
   */
  public function setBoundingPoly(GoogleCloudDocumentaiV1BoundingPoly $boundingPoly)
  {
    $this->boundingPoly = $boundingPoly;
  }
  /**
   * @return GoogleCloudDocumentaiV1BoundingPoly
   */
  public function getBoundingPoly()
  {
    return $this->boundingPoly;
  }
  /**
   * Confidence of the current Layout within context of the object this layout
   * is for. e.g. confidence can be for a single token, a table, a visual
   * element, etc. depending on context. Range `[0, 1]`.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Detected orientation for the Layout.
   *
   * Accepted values: ORIENTATION_UNSPECIFIED, PAGE_UP, PAGE_RIGHT, PAGE_DOWN,
   * PAGE_LEFT
   *
   * @param self::ORIENTATION_* $orientation
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
  }
  /**
   * @return self::ORIENTATION_*
   */
  public function getOrientation()
  {
    return $this->orientation;
  }
  /**
   * Text anchor indexing into the Document.text.
   *
   * @param GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor
   */
  public function setTextAnchor(GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor)
  {
    $this->textAnchor = $textAnchor;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentTextAnchor
   */
  public function getTextAnchor()
  {
    return $this->textAnchor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageLayout::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentPageLayout');
