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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryVertexDatasetConditions extends \Google\Model
{
  /**
   * Vertex AI dataset must have been created after this date. Used to avoid
   * backfilling.
   *
   * @var string
   */
  public $createdAfter;
  /**
   * Minimum age a Vertex AI dataset must have. If set, the value must be 1 hour
   * or greater.
   *
   * @var string
   */
  public $minAge;

  /**
   * Vertex AI dataset must have been created after this date. Used to avoid
   * backfilling.
   *
   * @param string $createdAfter
   */
  public function setCreatedAfter($createdAfter)
  {
    $this->createdAfter = $createdAfter;
  }
  /**
   * @return string
   */
  public function getCreatedAfter()
  {
    return $this->createdAfter;
  }
  /**
   * Minimum age a Vertex AI dataset must have. If set, the value must be 1 hour
   * or greater.
   *
   * @param string $minAge
   */
  public function setMinAge($minAge)
  {
    $this->minAge = $minAge;
  }
  /**
   * @return string
   */
  public function getMinAge()
  {
    return $this->minAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryVertexDatasetConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryVertexDatasetConditions');
