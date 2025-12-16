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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ListOptimalTrialsResponse extends \Google\Collection
{
  protected $collection_key = 'optimalTrials';
  protected $optimalTrialsType = GoogleCloudAiplatformV1Trial::class;
  protected $optimalTrialsDataType = 'array';

  /**
   * The pareto-optimal Trials for multiple objective Study or the optimal trial
   * for single objective Study. The definition of pareto-optimal can be checked
   * in wiki page. https://en.wikipedia.org/wiki/Pareto_efficiency
   *
   * @param GoogleCloudAiplatformV1Trial[] $optimalTrials
   */
  public function setOptimalTrials($optimalTrials)
  {
    $this->optimalTrials = $optimalTrials;
  }
  /**
   * @return GoogleCloudAiplatformV1Trial[]
   */
  public function getOptimalTrials()
  {
    return $this->optimalTrials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ListOptimalTrialsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ListOptimalTrialsResponse');
