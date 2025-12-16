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

class WonderMessageMapping extends \Google\Collection
{
  protected $collection_key = 'wonderCardMessageId';
  /**
   * @var string[]
   */
  public $wonderCardMessageId;

  /**
   * @param string[]
   */
  public function setWonderCardMessageId($wonderCardMessageId)
  {
    $this->wonderCardMessageId = $wonderCardMessageId;
  }
  /**
   * @return string[]
   */
  public function getWonderCardMessageId()
  {
    return $this->wonderCardMessageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WonderMessageMapping::class, 'Google_Service_CloudSearch_WonderMessageMapping');
