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

namespace Google\Service\Firestore;

class FieldTransform extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const SET_TO_SERVER_VALUE_SERVER_VALUE_UNSPECIFIED = 'SERVER_VALUE_UNSPECIFIED';
  /**
   * The time at which the server processed the request, with millisecond
   * precision. If used on multiple fields (same or different documents) in a
   * transaction, all the fields will get the same server timestamp.
   */
  public const SET_TO_SERVER_VALUE_REQUEST_TIME = 'REQUEST_TIME';
  protected $appendMissingElementsType = ArrayValue::class;
  protected $appendMissingElementsDataType = '';
  /**
   * The path of the field. See Document.fields for the field path syntax
   * reference.
   *
   * @var string
   */
  public $fieldPath;
  protected $incrementType = Value::class;
  protected $incrementDataType = '';
  protected $maximumType = Value::class;
  protected $maximumDataType = '';
  protected $minimumType = Value::class;
  protected $minimumDataType = '';
  protected $removeAllFromArrayType = ArrayValue::class;
  protected $removeAllFromArrayDataType = '';
  /**
   * Sets the field to the given server value.
   *
   * @var string
   */
  public $setToServerValue;

  /**
   * Append the given elements in order if they are not already present in the
   * current field value. If the field is not an array, or if the field does not
   * yet exist, it is first set to the empty array. Equivalent numbers of
   * different types (e.g. 3L and 3.0) are considered equal when checking if a
   * value is missing. NaN is equal to NaN, and Null is equal to Null. If the
   * input contains multiple equivalent values, only the first will be
   * considered. The corresponding transform_result will be the null value.
   *
   * @param ArrayValue $appendMissingElements
   */
  public function setAppendMissingElements(ArrayValue $appendMissingElements)
  {
    $this->appendMissingElements = $appendMissingElements;
  }
  /**
   * @return ArrayValue
   */
  public function getAppendMissingElements()
  {
    return $this->appendMissingElements;
  }
  /**
   * The path of the field. See Document.fields for the field path syntax
   * reference.
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * Adds the given value to the field's current value. This must be an integer
   * or a double value. If the field is not an integer or double, or if the
   * field does not yet exist, the transformation will set the field to the
   * given value. If either of the given value or the current field value are
   * doubles, both values will be interpreted as doubles. Double arithmetic and
   * representation of double values follow IEEE 754 semantics. If there is
   * positive/negative integer overflow, the field is resolved to the largest
   * magnitude positive/negative integer.
   *
   * @param Value $increment
   */
  public function setIncrement(Value $increment)
  {
    $this->increment = $increment;
  }
  /**
   * @return Value
   */
  public function getIncrement()
  {
    return $this->increment;
  }
  /**
   * Sets the field to the maximum of its current value and the given value.
   * This must be an integer or a double value. If the field is not an integer
   * or double, or if the field does not yet exist, the transformation will set
   * the field to the given value. If a maximum operation is applied where the
   * field and the input value are of mixed types (that is - one is an integer
   * and one is a double) the field takes on the type of the larger operand. If
   * the operands are equivalent (e.g. 3 and 3.0), the field does not change. 0,
   * 0.0, and -0.0 are all zero. The maximum of a zero stored value and zero
   * input value is always the stored value. The maximum of any numeric value x
   * and NaN is NaN.
   *
   * @param Value $maximum
   */
  public function setMaximum(Value $maximum)
  {
    $this->maximum = $maximum;
  }
  /**
   * @return Value
   */
  public function getMaximum()
  {
    return $this->maximum;
  }
  /**
   * Sets the field to the minimum of its current value and the given value.
   * This must be an integer or a double value. If the field is not an integer
   * or double, or if the field does not yet exist, the transformation will set
   * the field to the input value. If a minimum operation is applied where the
   * field and the input value are of mixed types (that is - one is an integer
   * and one is a double) the field takes on the type of the smaller operand. If
   * the operands are equivalent (e.g. 3 and 3.0), the field does not change. 0,
   * 0.0, and -0.0 are all zero. The minimum of a zero stored value and zero
   * input value is always the stored value. The minimum of any numeric value x
   * and NaN is NaN.
   *
   * @param Value $minimum
   */
  public function setMinimum(Value $minimum)
  {
    $this->minimum = $minimum;
  }
  /**
   * @return Value
   */
  public function getMinimum()
  {
    return $this->minimum;
  }
  /**
   * Remove all of the given elements from the array in the field. If the field
   * is not an array, or if the field does not yet exist, it is set to the empty
   * array. Equivalent numbers of the different types (e.g. 3L and 3.0) are
   * considered equal when deciding whether an element should be removed. NaN is
   * equal to NaN, and Null is equal to Null. This will remove all equivalent
   * values if there are duplicates. The corresponding transform_result will be
   * the null value.
   *
   * @param ArrayValue $removeAllFromArray
   */
  public function setRemoveAllFromArray(ArrayValue $removeAllFromArray)
  {
    $this->removeAllFromArray = $removeAllFromArray;
  }
  /**
   * @return ArrayValue
   */
  public function getRemoveAllFromArray()
  {
    return $this->removeAllFromArray;
  }
  /**
   * Sets the field to the given server value.
   *
   * Accepted values: SERVER_VALUE_UNSPECIFIED, REQUEST_TIME
   *
   * @param self::SET_TO_SERVER_VALUE_* $setToServerValue
   */
  public function setSetToServerValue($setToServerValue)
  {
    $this->setToServerValue = $setToServerValue;
  }
  /**
   * @return self::SET_TO_SERVER_VALUE_*
   */
  public function getSetToServerValue()
  {
    return $this->setToServerValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldTransform::class, 'Google_Service_Firestore_FieldTransform');
