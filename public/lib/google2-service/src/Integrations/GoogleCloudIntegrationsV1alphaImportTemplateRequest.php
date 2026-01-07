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

class GoogleCloudIntegrationsV1alphaImportTemplateRequest extends \Google\Model
{
  /**
   * Required. Resource Name of the integration where template needs to be
   * imported/inserted.
   *
   * @var string
   */
  public $integration;
  protected $subIntegrationsType = GoogleCloudIntegrationsV1alphaUseTemplateRequestIntegrationDetails::class;
  protected $subIntegrationsDataType = 'map';

  /**
   * Required. Resource Name of the integration where template needs to be
   * imported/inserted.
   *
   * @param string $integration
   */
  public function setIntegration($integration)
  {
    $this->integration = $integration;
  }
  /**
   * @return string
   */
  public function getIntegration()
  {
    return $this->integration;
  }
  /**
   * Optional. Sub Integration which would be created via templates.
   *
   * @param GoogleCloudIntegrationsV1alphaUseTemplateRequestIntegrationDetails[] $subIntegrations
   */
  public function setSubIntegrations($subIntegrations)
  {
    $this->subIntegrations = $subIntegrations;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaUseTemplateRequestIntegrationDetails[]
   */
  public function getSubIntegrations()
  {
    return $this->subIntegrations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaImportTemplateRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaImportTemplateRequest');
