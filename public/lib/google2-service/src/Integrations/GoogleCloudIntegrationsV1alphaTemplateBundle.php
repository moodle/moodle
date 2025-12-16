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

class GoogleCloudIntegrationsV1alphaTemplateBundle extends \Google\Collection
{
  protected $collection_key = 'subIntegrationVersionTemplates';
  protected $integrationVersionTemplateType = GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate::class;
  protected $integrationVersionTemplateDataType = '';
  protected $subIntegrationVersionTemplatesType = GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate::class;
  protected $subIntegrationVersionTemplatesDataType = 'array';

  /**
   * Required. Main integration templates of the template bundle.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate $integrationVersionTemplate
   */
  public function setIntegrationVersionTemplate(GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate $integrationVersionTemplate)
  {
    $this->integrationVersionTemplate = $integrationVersionTemplate;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate
   */
  public function getIntegrationVersionTemplate()
  {
    return $this->integrationVersionTemplate;
  }
  /**
   * Optional. Sub integration templates which would be added along with main
   * integration.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate[] $subIntegrationVersionTemplates
   */
  public function setSubIntegrationVersionTemplates($subIntegrationVersionTemplates)
  {
    $this->subIntegrationVersionTemplates = $subIntegrationVersionTemplates;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersionTemplate[]
   */
  public function getSubIntegrationVersionTemplates()
  {
    return $this->subIntegrationVersionTemplates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaTemplateBundle::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTemplateBundle');
