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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV2betaAnalysisResult extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $blockersType = GoogleApiServiceusageV2betaImpact::class;
  protected $blockersDataType = 'array';
  protected $warningsType = GoogleApiServiceusageV2betaImpact::class;
  protected $warningsDataType = 'array';

  /**
   * Blocking information that would prevent the policy changes at runtime.
   *
   * @param GoogleApiServiceusageV2betaImpact[] $blockers
   */
  public function setBlockers($blockers)
  {
    $this->blockers = $blockers;
  }
  /**
   * @return GoogleApiServiceusageV2betaImpact[]
   */
  public function getBlockers()
  {
    return $this->blockers;
  }
  /**
   * Warning information indicating that the policy changes might be unsafe, but
   * will not block the changes at runtime.
   *
   * @param GoogleApiServiceusageV2betaImpact[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return GoogleApiServiceusageV2betaImpact[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV2betaAnalysisResult::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV2betaAnalysisResult');
