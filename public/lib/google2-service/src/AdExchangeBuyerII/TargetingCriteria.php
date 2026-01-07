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

namespace Google\Service\AdExchangeBuyerII;

class TargetingCriteria extends \Google\Collection
{
  protected $collection_key = 'inclusions';
  protected $exclusionsType = TargetingValue::class;
  protected $exclusionsDataType = 'array';
  protected $inclusionsType = TargetingValue::class;
  protected $inclusionsDataType = 'array';
  /**
   * The key representing the shared targeting criterion. Targeting criteria
   * defined by Google ad servers will begin with GOOG_. Third parties may
   * define their own keys. A list of permissible keys along with the acceptable
   * values will be provided as part of the external documentation.
   *
   * @var string
   */
  public $key;

  /**
   * The list of values to exclude from targeting. Each value is AND'd together.
   *
   * @param TargetingValue[] $exclusions
   */
  public function setExclusions($exclusions)
  {
    $this->exclusions = $exclusions;
  }
  /**
   * @return TargetingValue[]
   */
  public function getExclusions()
  {
    return $this->exclusions;
  }
  /**
   * The list of value to include as part of the targeting. Each value is OR'd
   * together.
   *
   * @param TargetingValue[] $inclusions
   */
  public function setInclusions($inclusions)
  {
    $this->inclusions = $inclusions;
  }
  /**
   * @return TargetingValue[]
   */
  public function getInclusions()
  {
    return $this->inclusions;
  }
  /**
   * The key representing the shared targeting criterion. Targeting criteria
   * defined by Google ad servers will begin with GOOG_. Third parties may
   * define their own keys. A list of permissible keys along with the acceptable
   * values will be provided as part of the external documentation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetingCriteria::class, 'Google_Service_AdExchangeBuyerII_TargetingCriteria');
