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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig extends \Google\Model
{
  protected $headerIndexAlgorithmType = GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm::class;
  protected $headerIndexAlgorithmDataType = '';

  /**
   * Resolves the client ip based on a custom header.
   *
   * @param GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm $headerIndexAlgorithm
   */
  public function setHeaderIndexAlgorithm(GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm $headerIndexAlgorithm)
  {
    $this->headerIndexAlgorithm = $headerIndexAlgorithm;
  }
  /**
   * @return GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm
   */
  public function getHeaderIndexAlgorithm()
  {
    return $this->headerIndexAlgorithm;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig');
