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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec extends \Google\Collection
{
  /**
   * Unspecified AttributeType.
   */
  public const ATTRIBUTE_TYPE_ATTRIBUTE_TYPE_UNSPECIFIED = 'ATTRIBUTE_TYPE_UNSPECIFIED';
  /**
   * The value of the numerical field will be used to dynamically update the
   * boost amount. In this case, the attribute_value (the x value) of the
   * control point will be the actual value of the numerical field for which the
   * boost_amount is specified.
   */
  public const ATTRIBUTE_TYPE_NUMERICAL = 'NUMERICAL';
  /**
   * For the freshness use case the attribute value will be the duration between
   * the current time and the date in the datetime field specified. The value
   * must be formatted as an XSD `dayTimeDuration` value (a restricted subset of
   * an ISO 8601 duration value). The pattern for this is: `nDnM]`. E.g. `5D`,
   * `3DT12H30M`, `T24H`.
   */
  public const ATTRIBUTE_TYPE_FRESHNESS = 'FRESHNESS';
  /**
   * Interpolation type is unspecified. In this case, it defaults to Linear.
   */
  public const INTERPOLATION_TYPE_INTERPOLATION_TYPE_UNSPECIFIED = 'INTERPOLATION_TYPE_UNSPECIFIED';
  /**
   * Piecewise linear interpolation will be applied.
   */
  public const INTERPOLATION_TYPE_LINEAR = 'LINEAR';
  protected $collection_key = 'controlPoints';
  /**
   * Optional. The attribute type to be used to determine the boost amount. The
   * attribute value can be derived from the field value of the specified
   * field_name. In the case of numerical it is straightforward i.e.
   * attribute_value = numerical_field_value. In the case of freshness however,
   * attribute_value = (time.now() - datetime_field_value).
   *
   * @var string
   */
  public $attributeType;
  protected $controlPointsType = GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpecControlPoint::class;
  protected $controlPointsDataType = 'array';
  /**
   * Optional. The name of the field whose value will be used to determine the
   * boost amount.
   *
   * @var string
   */
  public $fieldName;
  /**
   * Optional. The interpolation type to be applied to connect the control
   * points listed below.
   *
   * @var string
   */
  public $interpolationType;

  /**
   * Optional. The attribute type to be used to determine the boost amount. The
   * attribute value can be derived from the field value of the specified
   * field_name. In the case of numerical it is straightforward i.e.
   * attribute_value = numerical_field_value. In the case of freshness however,
   * attribute_value = (time.now() - datetime_field_value).
   *
   * Accepted values: ATTRIBUTE_TYPE_UNSPECIFIED, NUMERICAL, FRESHNESS
   *
   * @param self::ATTRIBUTE_TYPE_* $attributeType
   */
  public function setAttributeType($attributeType)
  {
    $this->attributeType = $attributeType;
  }
  /**
   * @return self::ATTRIBUTE_TYPE_*
   */
  public function getAttributeType()
  {
    return $this->attributeType;
  }
  /**
   * Optional. The control points used to define the curve. The monotonic
   * function (defined through the interpolation_type above) passes through the
   * control points listed here.
   *
   * @param GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpecControlPoint[] $controlPoints
   */
  public function setControlPoints($controlPoints)
  {
    $this->controlPoints = $controlPoints;
  }
  /**
   * @return GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpecControlPoint[]
   */
  public function getControlPoints()
  {
    return $this->controlPoints;
  }
  /**
   * Optional. The name of the field whose value will be used to determine the
   * boost amount.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * Optional. The interpolation type to be applied to connect the control
   * points listed below.
   *
   * Accepted values: INTERPOLATION_TYPE_UNSPECIFIED, LINEAR
   *
   * @param self::INTERPOLATION_TYPE_* $interpolationType
   */
  public function setInterpolationType($interpolationType)
  {
    $this->interpolationType = $interpolationType;
  }
  /**
   * @return self::INTERPOLATION_TYPE_*
   */
  public function getInterpolationType()
  {
    return $this->interpolationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpecBoostControlSpec');
