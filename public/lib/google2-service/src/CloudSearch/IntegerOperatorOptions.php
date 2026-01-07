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

class IntegerOperatorOptions extends \Google\Model
{
  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property using the greater-than operator. For example, if
   * greaterThanOperatorName is *priorityabove* and the property's name is
   * *priorityVal*, then queries like *priorityabove:* show results only where
   * the value of the property named *priorityVal* is greater than **. The
   * operator name can only contain lowercase letters (a-z). The maximum length
   * is 32 characters.
   *
   * @var string
   */
  public $greaterThanOperatorName;
  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property using the less-than operator. For example, if
   * lessThanOperatorName is *prioritybelow* and the property's name is
   * *priorityVal*, then queries like *prioritybelow:* show results only where
   * the value of the property named *priorityVal* is less than **. The operator
   * name can only contain lowercase letters (a-z). The maximum length is 32
   * characters.
   *
   * @var string
   */
  public $lessThanOperatorName;
  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property. For example, if operatorName is *priority* and the
   * property's name is *priorityVal*, then queries like *priority:* show
   * results only where the value of the property named *priorityVal* matches
   * **. By contrast, a search that uses the same ** without an operator returns
   * all items where ** matches the value of any String properties or text
   * within the content field for the item. The operator name can only contain
   * lowercase letters (a-z). The maximum length is 32 characters.
   *
   * @var string
   */
  public $operatorName;

  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property using the greater-than operator. For example, if
   * greaterThanOperatorName is *priorityabove* and the property's name is
   * *priorityVal*, then queries like *priorityabove:* show results only where
   * the value of the property named *priorityVal* is greater than **. The
   * operator name can only contain lowercase letters (a-z). The maximum length
   * is 32 characters.
   *
   * @param string $greaterThanOperatorName
   */
  public function setGreaterThanOperatorName($greaterThanOperatorName)
  {
    $this->greaterThanOperatorName = $greaterThanOperatorName;
  }
  /**
   * @return string
   */
  public function getGreaterThanOperatorName()
  {
    return $this->greaterThanOperatorName;
  }
  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property using the less-than operator. For example, if
   * lessThanOperatorName is *prioritybelow* and the property's name is
   * *priorityVal*, then queries like *prioritybelow:* show results only where
   * the value of the property named *priorityVal* is less than **. The operator
   * name can only contain lowercase letters (a-z). The maximum length is 32
   * characters.
   *
   * @param string $lessThanOperatorName
   */
  public function setLessThanOperatorName($lessThanOperatorName)
  {
    $this->lessThanOperatorName = $lessThanOperatorName;
  }
  /**
   * @return string
   */
  public function getLessThanOperatorName()
  {
    return $this->lessThanOperatorName;
  }
  /**
   * Indicates the operator name required in the query in order to isolate the
   * integer property. For example, if operatorName is *priority* and the
   * property's name is *priorityVal*, then queries like *priority:* show
   * results only where the value of the property named *priorityVal* matches
   * **. By contrast, a search that uses the same ** without an operator returns
   * all items where ** matches the value of any String properties or text
   * within the content field for the item. The operator name can only contain
   * lowercase letters (a-z). The maximum length is 32 characters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntegerOperatorOptions::class, 'Google_Service_CloudSearch_IntegerOperatorOptions');
