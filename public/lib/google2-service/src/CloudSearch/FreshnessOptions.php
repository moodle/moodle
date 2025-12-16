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

class FreshnessOptions extends \Google\Model
{
  /**
   * The duration after which an object should be considered stale. The default
   * value is 180 days (in seconds).
   *
   * @var string
   */
  public $freshnessDuration;
  /**
   * This property indicates the freshness level of the object in the index. If
   * set, this property must be a top-level property within the property
   * definitions and it must be a timestamp type or date type. Otherwise, the
   * Indexing API uses updateTime as the freshness indicator. The maximum length
   * is 256 characters. When a property is used to calculate freshness, the
   * value defaults to 2 years from the current time.
   *
   * @var string
   */
  public $freshnessProperty;

  /**
   * The duration after which an object should be considered stale. The default
   * value is 180 days (in seconds).
   *
   * @param string $freshnessDuration
   */
  public function setFreshnessDuration($freshnessDuration)
  {
    $this->freshnessDuration = $freshnessDuration;
  }
  /**
   * @return string
   */
  public function getFreshnessDuration()
  {
    return $this->freshnessDuration;
  }
  /**
   * This property indicates the freshness level of the object in the index. If
   * set, this property must be a top-level property within the property
   * definitions and it must be a timestamp type or date type. Otherwise, the
   * Indexing API uses updateTime as the freshness indicator. The maximum length
   * is 256 characters. When a property is used to calculate freshness, the
   * value defaults to 2 years from the current time.
   *
   * @param string $freshnessProperty
   */
  public function setFreshnessProperty($freshnessProperty)
  {
    $this->freshnessProperty = $freshnessProperty;
  }
  /**
   * @return string
   */
  public function getFreshnessProperty()
  {
    return $this->freshnessProperty;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreshnessOptions::class, 'Google_Service_CloudSearch_FreshnessOptions');
