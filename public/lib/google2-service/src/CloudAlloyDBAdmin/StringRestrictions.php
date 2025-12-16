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

namespace Google\Service\CloudAlloyDBAdmin;

class StringRestrictions extends \Google\Collection
{
  protected $collection_key = 'allowedValues';
  /**
   * The list of allowed values, if bounded. This field will be empty if there
   * is a unbounded number of allowed values.
   *
   * @var string[]
   */
  public $allowedValues;

  /**
   * The list of allowed values, if bounded. This field will be empty if there
   * is a unbounded number of allowed values.
   *
   * @param string[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StringRestrictions::class, 'Google_Service_CloudAlloyDBAdmin_StringRestrictions');
