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

namespace Google\Service\NetworkServices;

class GrpcRouteHeaderMatch extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Will only match the exact value provided.
   */
  public const TYPE_EXACT = 'EXACT';
  /**
   * Will match paths conforming to the prefix specified by value. RE2 syntax is
   * supported.
   */
  public const TYPE_REGULAR_EXPRESSION = 'REGULAR_EXPRESSION';
  /**
   * Required. The key of the header.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Specifies how to match against the value of the header. If not
   * specified, a default value of EXACT is used.
   *
   * @var string
   */
  public $type;
  /**
   * Required. The value of the header.
   *
   * @var string
   */
  public $value;

  /**
   * Required. The key of the header.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Optional. Specifies how to match against the value of the header. If not
   * specified, a default value of EXACT is used.
   *
   * Accepted values: TYPE_UNSPECIFIED, EXACT, REGULAR_EXPRESSION
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
  /**
   * Required. The value of the header.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GrpcRouteHeaderMatch::class, 'Google_Service_NetworkServices_GrpcRouteHeaderMatch');
