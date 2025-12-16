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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1DataUpdateAction extends \Google\Model
{
  /**
   * Map of (K, V) -> (valid name of the field, new value of the field) E.g.,
   * ("age", "60") entry triggers update of field age with a value of 60. If the
   * field is not present then new entry is added. During update action
   * execution, value strings will be casted to appropriate types.
   *
   * @var string[]
   */
  public $entries;

  /**
   * Map of (K, V) -> (valid name of the field, new value of the field) E.g.,
   * ("age", "60") entry triggers update of field age with a value of 60. If the
   * field is not present then new entry is added. During update action
   * execution, value strings will be casted to appropriate types.
   *
   * @param string[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return string[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1DataUpdateAction::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1DataUpdateAction');
