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

namespace Google\Service\ToolResults;

class NonSdkApiUsageViolation extends \Google\Collection
{
  protected $collection_key = 'apiSignatures';
  /**
   * Signatures of a subset of those hidden API's.
   *
   * @var string[]
   */
  public $apiSignatures;
  /**
   * Total number of unique hidden API's accessed.
   *
   * @var int
   */
  public $uniqueApis;

  /**
   * Signatures of a subset of those hidden API's.
   *
   * @param string[] $apiSignatures
   */
  public function setApiSignatures($apiSignatures)
  {
    $this->apiSignatures = $apiSignatures;
  }
  /**
   * @return string[]
   */
  public function getApiSignatures()
  {
    return $this->apiSignatures;
  }
  /**
   * Total number of unique hidden API's accessed.
   *
   * @param int $uniqueApis
   */
  public function setUniqueApis($uniqueApis)
  {
    $this->uniqueApis = $uniqueApis;
  }
  /**
   * @return int
   */
  public function getUniqueApis()
  {
    return $this->uniqueApis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NonSdkApiUsageViolation::class, 'Google_Service_ToolResults_NonSdkApiUsageViolation');
