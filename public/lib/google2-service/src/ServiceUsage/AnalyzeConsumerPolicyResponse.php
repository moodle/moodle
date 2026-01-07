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

class AnalyzeConsumerPolicyResponse extends \Google\Collection
{
  protected $collection_key = 'analysis';
  protected $analysisType = Analysis::class;
  protected $analysisDataType = 'array';

  /**
   * The list of analyses returned from performing the intended policy update
   * analysis. The analysis is grouped by service name and different analysis
   * types. The empty analysis list means that the consumer policy can be
   * updated without any warnings or blockers.
   *
   * @param Analysis[] $analysis
   */
  public function setAnalysis($analysis)
  {
    $this->analysis = $analysis;
  }
  /**
   * @return Analysis[]
   */
  public function getAnalysis()
  {
    return $this->analysis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeConsumerPolicyResponse::class, 'Google_Service_ServiceUsage_AnalyzeConsumerPolicyResponse');
