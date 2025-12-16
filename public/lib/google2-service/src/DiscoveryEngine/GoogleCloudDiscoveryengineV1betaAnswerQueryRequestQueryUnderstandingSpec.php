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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec extends \Google\Model
{
  protected $queryClassificationSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryClassificationSpec::class;
  protected $queryClassificationSpecDataType = '';
  protected $queryRephraserSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpec::class;
  protected $queryRephraserSpecDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryClassificationSpec
   */
  public function setQueryClassificationSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryClassificationSpec $queryClassificationSpec)
  {
    $this->queryClassificationSpec = $queryClassificationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryClassificationSpec
   */
  public function getQueryClassificationSpec()
  {
    return $this->queryClassificationSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpec
   */
  public function setQueryRephraserSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpec $queryRephraserSpec)
  {
    $this->queryRephraserSpec = $queryRephraserSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpec
   */
  public function getQueryRephraserSpec()
  {
    return $this->queryRephraserSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec');
