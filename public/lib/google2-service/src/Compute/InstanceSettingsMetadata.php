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

class InstanceSettingsMetadata extends \Google\Model
{
  /**
   * A metadata key/value items map. The total size of all keys and values must
   * be less than 512KB.
   *
   * @var string[]
   */
  public $items;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#metadata
   * for metadata.
   *
   * @var string
   */
  public $kind;

  /**
   * A metadata key/value items map. The total size of all keys and values must
   * be less than 512KB.
   *
   * @param string[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return string[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#metadata
   * for metadata.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceSettingsMetadata::class, 'Google_Service_Compute_InstanceSettingsMetadata');
