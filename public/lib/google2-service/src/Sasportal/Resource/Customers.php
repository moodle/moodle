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

namespace Google\Service\Sasportal\Resource;

use Google\Service\Sasportal\SasPortalCustomer;
use Google\Service\Sasportal\SasPortalListCustomersResponse;
use Google\Service\Sasportal\SasPortalListGcpProjectDeploymentsResponse;
use Google\Service\Sasportal\SasPortalListLegacyOrganizationsResponse;
use Google\Service\Sasportal\SasPortalMigrateOrganizationRequest;
use Google\Service\Sasportal\SasPortalOperation;
use Google\Service\Sasportal\SasPortalProvisionDeploymentRequest;
use Google\Service\Sasportal\SasPortalProvisionDeploymentResponse;
use Google\Service\Sasportal\SasPortalSetupSasAnalyticsRequest;

/**
 * The "customers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $sasportalService = new Google\Service\Sasportal(...);
 *   $customers = $sasportalService->customers;
 *  </code>
 */
class Customers extends \Google\Service\Resource
{
  /**
   * Returns a requested customer. (customers.get)
   *
   * @param string $name Required. The name of the customer.
   * @param array $optParams Optional parameters.
   * @return SasPortalCustomer
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SasPortalCustomer::class);
  }
  /**
   * Returns a list of requested customers. (customers.listCustomers)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of customers to return in the
   * response.
   * @opt_param string pageToken A pagination token returned from a previous call
   * to ListCustomers that indicates where this listing should continue from.
   * @return SasPortalListCustomersResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomers($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], SasPortalListCustomersResponse::class);
  }
  /**
   * Returns a list of SAS deployments associated with current GCP project.
   * Includes whether SAS analytics has been enabled or not.
   * (customers.listGcpProjectDeployments)
   *
   * @param array $optParams Optional parameters.
   * @return SasPortalListGcpProjectDeploymentsResponse
   * @throws \Google\Service\Exception
   */
  public function listGcpProjectDeployments($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('listGcpProjectDeployments', [$params], SasPortalListGcpProjectDeploymentsResponse::class);
  }
  /**
   * Returns a list of legacy organizations. (customers.listLegacyOrganizations)
   *
   * @param array $optParams Optional parameters.
   * @return SasPortalListLegacyOrganizationsResponse
   * @throws \Google\Service\Exception
   */
  public function listLegacyOrganizations($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('listLegacyOrganizations', [$params], SasPortalListLegacyOrganizationsResponse::class);
  }
  /**
   * Migrates a SAS organization to the cloud. This will create GCP projects for
   * each deployment and associate them. The SAS Organization is linked to the gcp
   * project that called the command. go/sas-legacy-customer-migration
   * (customers.migrateOrganization)
   *
   * @param SasPortalMigrateOrganizationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SasPortalOperation
   * @throws \Google\Service\Exception
   */
  public function migrateOrganization(SasPortalMigrateOrganizationRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('migrateOrganization', [$params], SasPortalOperation::class);
  }
  /**
   * Updates an existing customer. (customers.patch)
   *
   * @param string $name Output only. Resource name of the customer.
   * @param SasPortalCustomer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Fields to be updated.
   * @return SasPortalCustomer
   * @throws \Google\Service\Exception
   */
  public function patch($name, SasPortalCustomer $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], SasPortalCustomer::class);
  }
  /**
   * Creates a new SAS deployment through the GCP workflow. Creates a SAS
   * organization if an organization match is not found.
   * (customers.provisionDeployment)
   *
   * @param SasPortalProvisionDeploymentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SasPortalProvisionDeploymentResponse
   * @throws \Google\Service\Exception
   */
  public function provisionDeployment(SasPortalProvisionDeploymentRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provisionDeployment', [$params], SasPortalProvisionDeploymentResponse::class);
  }
  /**
   * Setups the a GCP Project to receive SAS Analytics messages via GCP Pub/Sub
   * with a subscription to BigQuery. All the Pub/Sub topics and BigQuery tables
   * are created automatically as part of this service.
   * (customers.setupSasAnalytics)
   *
   * @param SasPortalSetupSasAnalyticsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SasPortalOperation
   * @throws \Google\Service\Exception
   */
  public function setupSasAnalytics(SasPortalSetupSasAnalyticsRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setupSasAnalytics', [$params], SasPortalOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Customers::class, 'Google_Service_Sasportal_Resource_Customers');
