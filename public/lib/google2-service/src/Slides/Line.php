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

namespace Google\Service\Slides;

class Line extends \Google\Model
{
  /**
   * Unspecified line category.
   */
  public const LINE_CATEGORY_LINE_CATEGORY_UNSPECIFIED = 'LINE_CATEGORY_UNSPECIFIED';
  /**
   * Straight connectors, including straight connector 1.
   */
  public const LINE_CATEGORY_STRAIGHT = 'STRAIGHT';
  /**
   * Bent connectors, including bent connector 2 to 5.
   */
  public const LINE_CATEGORY_BENT = 'BENT';
  /**
   * Curved connectors, including curved connector 2 to 5.
   */
  public const LINE_CATEGORY_CURVED = 'CURVED';
  /**
   * An unspecified line type.
   */
  public const LINE_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Straight connector 1 form. Corresponds to ECMA-376 ST_ShapeType
   * 'straightConnector1'.
   */
  public const LINE_TYPE_STRAIGHT_CONNECTOR_1 = 'STRAIGHT_CONNECTOR_1';
  /**
   * Bent connector 2 form. Corresponds to ECMA-376 ST_ShapeType
   * 'bentConnector2'.
   */
  public const LINE_TYPE_BENT_CONNECTOR_2 = 'BENT_CONNECTOR_2';
  /**
   * Bent connector 3 form. Corresponds to ECMA-376 ST_ShapeType
   * 'bentConnector3'.
   */
  public const LINE_TYPE_BENT_CONNECTOR_3 = 'BENT_CONNECTOR_3';
  /**
   * Bent connector 4 form. Corresponds to ECMA-376 ST_ShapeType
   * 'bentConnector4'.
   */
  public const LINE_TYPE_BENT_CONNECTOR_4 = 'BENT_CONNECTOR_4';
  /**
   * Bent connector 5 form. Corresponds to ECMA-376 ST_ShapeType
   * 'bentConnector5'.
   */
  public const LINE_TYPE_BENT_CONNECTOR_5 = 'BENT_CONNECTOR_5';
  /**
   * Curved connector 2 form. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedConnector2'.
   */
  public const LINE_TYPE_CURVED_CONNECTOR_2 = 'CURVED_CONNECTOR_2';
  /**
   * Curved connector 3 form. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedConnector3'.
   */
  public const LINE_TYPE_CURVED_CONNECTOR_3 = 'CURVED_CONNECTOR_3';
  /**
   * Curved connector 4 form. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedConnector4'.
   */
  public const LINE_TYPE_CURVED_CONNECTOR_4 = 'CURVED_CONNECTOR_4';
  /**
   * Curved connector 5 form. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedConnector5'.
   */
  public const LINE_TYPE_CURVED_CONNECTOR_5 = 'CURVED_CONNECTOR_5';
  /**
   * Straight line. Corresponds to ECMA-376 ST_ShapeType 'line'. This line type
   * is not a connector.
   */
  public const LINE_TYPE_STRAIGHT_LINE = 'STRAIGHT_LINE';
  /**
   * The category of the line. It matches the `category` specified in
   * CreateLineRequest, and can be updated with UpdateLineCategoryRequest.
   *
   * @var string
   */
  public $lineCategory;
  protected $linePropertiesType = LineProperties::class;
  protected $linePropertiesDataType = '';
  /**
   * The type of the line.
   *
   * @var string
   */
  public $lineType;

  /**
   * The category of the line. It matches the `category` specified in
   * CreateLineRequest, and can be updated with UpdateLineCategoryRequest.
   *
   * Accepted values: LINE_CATEGORY_UNSPECIFIED, STRAIGHT, BENT, CURVED
   *
   * @param self::LINE_CATEGORY_* $lineCategory
   */
  public function setLineCategory($lineCategory)
  {
    $this->lineCategory = $lineCategory;
  }
  /**
   * @return self::LINE_CATEGORY_*
   */
  public function getLineCategory()
  {
    return $this->lineCategory;
  }
  /**
   * The properties of the line.
   *
   * @param LineProperties $lineProperties
   */
  public function setLineProperties(LineProperties $lineProperties)
  {
    $this->lineProperties = $lineProperties;
  }
  /**
   * @return LineProperties
   */
  public function getLineProperties()
  {
    return $this->lineProperties;
  }
  /**
   * The type of the line.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRAIGHT_CONNECTOR_1, BENT_CONNECTOR_2,
   * BENT_CONNECTOR_3, BENT_CONNECTOR_4, BENT_CONNECTOR_5, CURVED_CONNECTOR_2,
   * CURVED_CONNECTOR_3, CURVED_CONNECTOR_4, CURVED_CONNECTOR_5, STRAIGHT_LINE
   *
   * @param self::LINE_TYPE_* $lineType
   */
  public function setLineType($lineType)
  {
    $this->lineType = $lineType;
  }
  /**
   * @return self::LINE_TYPE_*
   */
  public function getLineType()
  {
    return $this->lineType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Line::class, 'Google_Service_Slides_Line');
