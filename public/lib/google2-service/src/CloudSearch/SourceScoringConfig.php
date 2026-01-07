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

class SourceScoringConfig extends \Google\Model
{
  public const SOURCE_IMPORTANCE_DEFAULT = 'DEFAULT';
  public const SOURCE_IMPORTANCE_LOW = 'LOW';
  public const SOURCE_IMPORTANCE_HIGH = 'HIGH';
  /**
   * Importance of the source.
   *
   * @var string
   */
  public $sourceImportance;

  /**
   * Importance of the source.
   *
   * Accepted values: DEFAULT, LOW, HIGH
   *
   * @param self::SOURCE_IMPORTANCE_* $sourceImportance
   */
  public function setSourceImportance($sourceImportance)
  {
    $this->sourceImportance = $sourceImportance;
  }
  /**
   * @return self::SOURCE_IMPORTANCE_*
   */
  public function getSourceImportance()
  {
    return $this->sourceImportance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceScoringConfig::class, 'Google_Service_CloudSearch_SourceScoringConfig');
