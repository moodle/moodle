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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest extends \Google\Collection
{
  protected $collection_key = 'workflows';
  /**
   * Optional. Indicate which workflows to create
   *
   * @var string[]
   */
  public $workflows;

  /**
   * Optional. Indicate which workflows to create
   *
   * @param string[] $workflows
   */
  public function setWorkflows($workflows)
  {
    $this->workflows = $workflows;
  }
  /**
   * @return string[]
   */
  public function getWorkflows()
  {
    return $this->workflows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest');
