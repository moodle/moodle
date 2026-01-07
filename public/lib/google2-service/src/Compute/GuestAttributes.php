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

namespace Google\Service\Compute;

class GuestAttributes extends \Google\Model
{
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#guestAttributes for guest attributes entry.
   *
   * @var string
   */
  public $kind;
  /**
   * The path to be queried. This can be the default namespace ('') or a nested
   * namespace ('\/') or a specified key ('\/\').
   *
   * @var string
   */
  public $queryPath;
  protected $queryValueType = GuestAttributesValue::class;
  protected $queryValueDataType = '';
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The key to search for.
   *
   * @var string
   */
  public $variableKey;
  /**
   * Output only. [Output Only] The value found for the requested key.
   *
   * @var string
   */
  public $variableValue;

  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#guestAttributes for guest attributes entry.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The path to be queried. This can be the default namespace ('') or a nested
   * namespace ('\/') or a specified key ('\/\').
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
   * Output only. [Output Only] The value of the requested queried path.
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
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The key to search for.
   *
   * @param string $variableKey
   */
  public function setVariableKey($variableKey)
  {
    $this->variableKey = $variableKey;
  }
  /**
   * @return string
   */
  public function getVariableKey()
  {
    return $this->variableKey;
  }
  /**
   * Output only. [Output Only] The value found for the requested key.
   *
   * @param string $variableValue
   */
  public function setVariableValue($variableValue)
  {
    $this->variableValue = $variableValue;
  }
  /**
   * @return string
   */
  public function getVariableValue()
  {
    return $this->variableValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestAttributes::class, 'Google_Service_Compute_GuestAttributes');
