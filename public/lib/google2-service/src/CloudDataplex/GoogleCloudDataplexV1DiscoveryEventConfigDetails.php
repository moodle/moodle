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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DiscoveryEventConfigDetails extends \Google\Model
{
  /**
   * A list of discovery configuration parameters in effect. The keys are the
   * field paths within DiscoverySpec. Eg. includePatterns, excludePatterns,
   * csvOptions.disableTypeInference, etc.
   *
   * @var string[]
   */
  public $parameters;

  /**
   * A list of discovery configuration parameters in effect. The keys are the
   * field paths within DiscoverySpec. Eg. includePatterns, excludePatterns,
   * csvOptions.disableTypeInference, etc.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DiscoveryEventConfigDetails::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DiscoveryEventConfigDetails');
