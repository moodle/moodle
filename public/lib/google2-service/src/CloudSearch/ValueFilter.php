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

namespace Google\Service\CloudSearch;

class ValueFilter extends \Google\Model
{
  /**
   * The `operator_name` applied to the query, such as *price_greater_than*. The
   * filter can work against both types of filters defined in the schema for
   * your data source: 1. `operator_name`, where the query filters results by
   * the property that matches the value. 2. `greater_than_operator_name` or
   * `less_than_operator_name` in your schema. The query filters the results for
   * the property values that are greater than or less than the supplied value
   * in the query.
   *
   * @var string
   */
  public $operatorName;
  protected $valueType = Value::class;
  protected $valueDataType = '';

  /**
   * The `operator_name` applied to the query, such as *price_greater_than*. The
   * filter can work against both types of filters defined in the schema for
   * your data source: 1. `operator_name`, where the query filters results by
   * the property that matches the value. 2. `greater_than_operator_name` or
   * `less_than_operator_name` in your schema. The query filters the results for
   * the property values that are greater than or less than the supplied value
   * in the query.
   *
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
  /**
   * The value to be compared with.
   *
   * @param Value $value
   */
  public function setValue(Value $value)
  {
    $this->value = $value;
  }
  /**
   * @return Value
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueFilter::class, 'Google_Service_CloudSearch_ValueFilter');
