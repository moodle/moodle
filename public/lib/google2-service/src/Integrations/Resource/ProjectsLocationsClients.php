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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaChangeCustomerConfigResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDeprovisionClientRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaProvisionClientRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaReplaceServiceAccountRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaSwitchEncryptionRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaSwitchVariableMaskingRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaToggleHttpRequest;
use Google\Service\Integrations\GoogleProtobufEmpty;

/**
 * The "clients" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $clients = $integrationsService->projects_locations_clients;
 *  </code>
 */
class ProjectsLocationsClients extends \Google\Service\Resource
{
  /**
   * Updates the client customer configuration for the given project and location
   * resource name (clients.changeConfig)
   *
   * @param string $parent Required. Required: Format -
   * projects/{project}/locations/{location}
   * @param GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaChangeCustomerConfigResponse
   * @throws \Google\Service\Exception
   */
  public function changeConfig($parent, GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('changeConfig', [$params], GoogleCloudIntegrationsV1alphaChangeCustomerConfigResponse::class);
  }
  /**
   * Perform the deprovisioning steps to disable a user GCP project to use IP and
   * purge all related data in a wipeout-compliant way. (clients.deprovision)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * deprovisioned.
   * @param GoogleCloudIntegrationsV1alphaDeprovisionClientRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function deprovision($parent, GoogleCloudIntegrationsV1alphaDeprovisionClientRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deprovision', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Perform the provisioning steps to enable a user GCP project to use IP. If GCP
   * project already registered on IP end via Apigee Integration, provisioning
   * will fail. (clients.provision)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaProvisionClientRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function provision($parent, GoogleCloudIntegrationsV1alphaProvisionClientRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provision', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Perform post provisioning steps after client is provisioned.
   * (clients.provisionClientPostProcessor)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorResponse
   * @throws \Google\Service\Exception
   */
  public function provisionClientPostProcessor($parent, GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provisionClientPostProcessor', [$params], GoogleCloudIntegrationsV1alphaProvisionClientPostProcessorResponse::class);
  }
  /**
   * Update run-as service account for provisioned client (clients.replace)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaReplaceServiceAccountRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function replace($parent, GoogleCloudIntegrationsV1alphaReplaceServiceAccountRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('replace', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Update client from GMEK to CMEK (clients.switchProjectsLocationsClients)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaSwitchEncryptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function switchProjectsLocationsClients($parent, GoogleCloudIntegrationsV1alphaSwitchEncryptionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('switch', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Update variable masking for provisioned client
   * (clients.switchVariableMasking)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaSwitchVariableMaskingRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function switchVariableMasking($parent, GoogleCloudIntegrationsV1alphaSwitchVariableMaskingRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('switchVariableMasking', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Enable/Disable http call for provisioned client (clients.toggleHttp)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param GoogleCloudIntegrationsV1alphaToggleHttpRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function toggleHttp($parent, GoogleCloudIntegrationsV1alphaToggleHttpRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('toggleHttp', [$params], GoogleProtobufEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClients::class, 'Google_Service_Integrations_Resource_ProjectsLocationsClients');
