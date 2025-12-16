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

namespace Google\Service\GoogleAnalyticsAdmin\Resource;

use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaAccount;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaDataSharingSettings;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaListAccountsResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaProvisionAccountTicketRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaProvisionAccountTicketResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaRunAccessReportRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaRunAccessReportResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleProtobufEmpty;

/**
 * The "accounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsadminService = new Google\Service\GoogleAnalyticsAdmin(...);
 *   $accounts = $analyticsadminService->accounts;
 *  </code>
 */
class Accounts extends \Google\Service\Resource
{
  /**
   * Marks target Account as soft-deleted (ie: "trashed") and returns it. This API
   * does not have a method to restore soft-deleted accounts. However, they can be
   * restored using the Trash Can UI. If the accounts are not restored before the
   * expiration time, the account and all child resources (eg: Properties,
   * GoogleAdsLinks, Streams, AccessBindings) will be permanently purged.
   * https://support.google.com/analytics/answer/6154772 Returns an error if the
   * target is not found. (accounts.delete)
   *
   * @param string $name Required. The name of the Account to soft-delete. Format:
   * accounts/{account} Example: "accounts/100"
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Lookup for a single Account. (accounts.get)
   *
   * @param string $name Required. The name of the account to lookup. Format:
   * accounts/{account} Example: "accounts/100"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaAccount
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAnalyticsAdminV1betaAccount::class);
  }
  /**
   * Get data sharing settings on an account. Data sharing settings are
   * singletons. (accounts.getDataSharingSettings)
   *
   * @param string $name Required. The name of the settings to lookup. Format:
   * accounts/{account}/dataSharingSettings Example:
   * `accounts/1000/dataSharingSettings`
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaDataSharingSettings
   * @throws \Google\Service\Exception
   */
  public function getDataSharingSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDataSharingSettings', [$params], GoogleAnalyticsAdminV1betaDataSharingSettings::class);
  }
  /**
   * Returns all accounts accessible by the caller. Note that these accounts might
   * not currently have GA properties. Soft-deleted (ie: "trashed") accounts are
   * excluded by default. Returns an empty list if no relevant accounts are found.
   * (accounts.listAccounts)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of resources to return. The
   * service may return fewer than this value, even if there are additional pages.
   * If unspecified, at most 50 resources will be returned. The maximum value is
   * 200; (higher values will be coerced to the maximum)
   * @opt_param string pageToken A page token, received from a previous
   * `ListAccounts` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAccounts` must match the
   * call that provided the page token.
   * @opt_param bool showDeleted Whether to include soft-deleted (ie: "trashed")
   * Accounts in the results. Accounts can be inspected to determine whether they
   * are deleted or not.
   * @return GoogleAnalyticsAdminV1betaListAccountsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccounts($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAnalyticsAdminV1betaListAccountsResponse::class);
  }
  /**
   * Updates an account. (accounts.patch)
   *
   * @param string $name Output only. Resource name of this account. Format:
   * accounts/{account} Example: "accounts/100"
   * @param GoogleAnalyticsAdminV1betaAccount $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * Field names must be in snake case (for example, "field_to_update"). Omitted
   * fields will not be updated. To replace the entire entity, use one path with
   * the string "*" to match all fields.
   * @return GoogleAnalyticsAdminV1betaAccount
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleAnalyticsAdminV1betaAccount $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleAnalyticsAdminV1betaAccount::class);
  }
  /**
   * Requests a ticket for creating an account. (accounts.provisionAccountTicket)
   *
   * @param GoogleAnalyticsAdminV1betaProvisionAccountTicketRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaProvisionAccountTicketResponse
   * @throws \Google\Service\Exception
   */
  public function provisionAccountTicket(GoogleAnalyticsAdminV1betaProvisionAccountTicketRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provisionAccountTicket', [$params], GoogleAnalyticsAdminV1betaProvisionAccountTicketResponse::class);
  }
  /**
   * Returns a customized report of data access records. The report provides
   * records of each time a user reads Google Analytics reporting data. Access
   * records are retained for up to 2 years. Data Access Reports can be requested
   * for a property. Reports may be requested for any property, but dimensions
   * that aren't related to quota can only be requested on Google Analytics 360
   * properties. This method is only available to Administrators. These data
   * access records include GA UI Reporting, GA UI Explorations, GA Data API, and
   * other products like Firebase & Admob that can retrieve data from Google
   * Analytics through a linkage. These records don't include property
   * configuration changes like adding a stream or changing a property's time
   * zone. For configuration change history, see [searchChangeHistoryEvents](https
   * ://developers.google.com/analytics/devguides/config/admin/v1/rest/v1alpha/acc
   * ounts/searchChangeHistoryEvents). To give your feedback on this API, complete
   * the [Google Analytics Access Reports feedback](https://docs.google.com/forms/
   * d/e/1FAIpQLSdmEBUrMzAEdiEKk5TV5dEHvDUZDRlgWYdQdAeSdtR4hVjEhw/viewform) form.
   * (accounts.runAccessReport)
   *
   * @param string $entity The Data Access Report supports requesting at the
   * property level or account level. If requested at the account level, Data
   * Access Reports include all access for all properties under that account. To
   * request at the property level, entity should be for example 'properties/123'
   * if "123" is your Google Analytics property ID. To request at the account
   * level, entity should be for example 'accounts/1234' if "1234" is your Google
   * Analytics Account ID.
   * @param GoogleAnalyticsAdminV1betaRunAccessReportRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaRunAccessReportResponse
   * @throws \Google\Service\Exception
   */
  public function runAccessReport($entity, GoogleAnalyticsAdminV1betaRunAccessReportRequest $postBody, $optParams = [])
  {
    $params = ['entity' => $entity, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('runAccessReport', [$params], GoogleAnalyticsAdminV1betaRunAccessReportResponse::class);
  }
  /**
   * Searches through all changes to an account or its children given the
   * specified set of filters. Only returns the subset of changes supported by the
   * API. The UI may return additional changes.
   * (accounts.searchChangeHistoryEvents)
   *
   * @param string $account Required. The account resource for which to return
   * change history resources. Format: accounts/{account} Example: `accounts/100`
   * @param GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsResponse
   * @throws \Google\Service\Exception
   */
  public function searchChangeHistoryEvents($account, GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest $postBody, $optParams = [])
  {
    $params = ['account' => $account, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchChangeHistoryEvents', [$params], GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accounts::class, 'Google_Service_GoogleAnalyticsAdmin_Resource_Accounts');
