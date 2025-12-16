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

namespace Google\Service\DataTransfer;

class ApplicationTransferParam extends \Google\Collection
{
  protected $collection_key = 'value';
  /**
   * The type of the transfer parameter, such as `PRIVACY_LEVEL`.
   *
   * @var string
   */
  public $key;
  /**
   * The value of the transfer parameter, such as `PRIVATE` or `SHARED`.
   *
   * @var string[]
   */
  public $value;

  /**
   * The type of the transfer parameter, such as `PRIVACY_LEVEL`.
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
   * The value of the transfer parameter, such as `PRIVATE` or `SHARED`.
   *
   * @param string[] $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string[]
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationTransferParam::class, 'Google_Service_DataTransfer_ApplicationTransferParam');
