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

namespace Google\Service\DatabaseMigrationService;

class SequenceEntity extends \Google\Model
{
  /**
   * Indicates number of entries to cache / precreate.
   *
   * @var string
   */
  public $cache;
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * Indicates whether the sequence value should cycle through.
   *
   * @var bool
   */
  public $cycle;
  /**
   * Increment value for the sequence.
   *
   * @var string
   */
  public $increment;
  /**
   * Maximum number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @var string
   */
  public $maxValue;
  /**
   * Minimum number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @var string
   */
  public $minValue;
  /**
   * Start number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @var string
   */
  public $startValue;

  /**
   * Indicates number of entries to cache / precreate.
   *
   * @param string $cache
   */
  public function setCache($cache)
  {
    $this->cache = $cache;
  }
  /**
   * @return string
   */
  public function getCache()
  {
    return $this->cache;
  }
  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * Indicates whether the sequence value should cycle through.
   *
   * @param bool $cycle
   */
  public function setCycle($cycle)
  {
    $this->cycle = $cycle;
  }
  /**
   * @return bool
   */
  public function getCycle()
  {
    return $this->cycle;
  }
  /**
   * Increment value for the sequence.
   *
   * @param string $increment
   */
  public function setIncrement($increment)
  {
    $this->increment = $increment;
  }
  /**
   * @return string
   */
  public function getIncrement()
  {
    return $this->increment;
  }
  /**
   * Maximum number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @param string $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return string
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Minimum number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @param string $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return string
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Start number for the sequence represented as bytes to accommodate large.
   * numbers
   *
   * @param string $startValue
   */
  public function setStartValue($startValue)
  {
    $this->startValue = $startValue;
  }
  /**
   * @return string
   */
  public function getStartValue()
  {
    return $this->startValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SequenceEntity::class, 'Google_Service_DatabaseMigrationService_SequenceEntity');
