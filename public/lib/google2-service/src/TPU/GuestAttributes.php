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

namespace Google\Service\TPU;

class GuestAttributes extends \Google\Model
{
  /**
   * The path to be queried. This can be the default namespace ('/') or a nested
   * namespace ('/\/') or a specified key ('/\/\')
   *
   * @var string
   */
  public $queryPath;
  protected $queryValueType = GuestAttributesValue::class;
  protected $queryValueDataType = '';

  /**
   * The path to be queried. This can be the default namespace ('/') or a nested
   * namespace ('/\/') or a specified key ('/\/\')
   *
   * @param string $queryPath
   */
  public function setQueryPath($queryPath)
  {
    $this->queryPath = $queryPath;
  }
  /**
   * @return string
   */
  public function getQueryPath()
  {
    return $this->queryPath;
  }
  /**
   * The value of the requested queried path.
   *
   * @param GuestAttributesValue $queryValue
   */
  public function setQueryValue(GuestAttributesValue $queryValue)
  {
    $this->queryValue = $queryValue;
  }
  /**
   * @return GuestAttributesValue
   */
  public function getQueryValue()
  {
    return $this->queryValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestAttributes::class, 'Google_Service_TPU_GuestAttributes');
