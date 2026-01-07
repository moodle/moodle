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

use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaDataRetentionSettings;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaListPropertiesResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaProperty;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaRunAccessReportRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaRunAccessReportResponse;

/**
 * The "properties" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsadminService = new Google\Service\GoogleAnalyticsAdmin(...);
 *   $properties = $analyticsadminService->properties;
 *  </code>
 */
class Properties extends \Google\Service\Resource
{
  /**
   * Acknowledges the terms of user data collection for the specified property.
   * This acknowledgement must be completed (either in the Google Analytics UI or
   * through this API) before MeasurementProtocolSecret resources may be created.
   * (properties.acknowledgeUserDataCollection)
   *
   * @param string $property Required. The property for which to acknowledge user
   * data collection.
   * @param GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionResponse
   * @throws \Google\Service\Exception
   */
  public function acknowledgeUserDataCollection($property, GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest $postBody, $optParams = [])
  {
    $params = ['property' => $property, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('acknowledgeUserDataCollection', [$params], GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionResponse::class);
  }
  /**
   * Creates a Google Analytics property with the specified location and
   * attributes. (properties.create)
   *
   * @param GoogleAnalyticsAdminV1betaProperty $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaProperty
   * @throws \Google\Service\Exception
   */
  public function create(GoogleAnalyticsAdminV1betaProperty $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleAnalyticsAdminV1betaProperty::class);
  }
  /**
   * Marks target Property as soft-deleted (ie: "trashed") and returns it. This
   * API does not have a method to restore soft-deleted properties. However, they
   * can be restored using the Trash Can UI. If the properties are not restored
   * before the expiration time, the Property and all child resources (eg:
   * GoogleAdsLinks, Streams, AccessBindings) will be permanently purged.
   * https://support.google.com/analytics/answer/6154772 Returns an error if the
   * target is not found. (properties.delete)
   *
   * @param string $name Required. The name of the Property to soft-delete.
   * Format: properties/{property_id} Example: "properties/1000"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaProperty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleAnalyticsAdminV1betaProperty::class);
  }
  /**
   * Lookup for a single GA Property. (properties.get)
   *
   * @param string $name Required. The name of the property to lookup. Format:
   * properties/{property_id} Example: "properties/1000"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaProperty
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAnalyticsAdminV1betaProperty::class);
  }
  /**
   * Returns the singleton data retention settings for this property.
   * (properties.getDataRetentionSettings)
   *
   * @param string $name Required. The name of the settings to lookup. Format:
   * properties/{property}/dataRetentionSettings Example:
   * "properties/1000/dataRetentionSettings"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaDataRetentionSettings
   * @throws \Google\Service\Exception
   */
  public function getDataRetentionSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDataRetentionSettings', [$params], GoogleAnalyticsAdminV1betaDataRetentionSettings::class);
  }
  /**
   * Returns child Properties under the specified parent Account. Properties will
   * be excluded if the caller does not have access. Soft-deleted (ie: "trashed")
   * properties are excluded by default. Returns an empty list if no relevant
   * properties are found. (properties.listProperties)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Required. An expression for filtering the results of
   * the request. Fields eligible for filtering are: `parent:`(The resource name
   * of the parent account/property) or `ancestor:`(The resource name of the
   * parent account) or `firebase_project:`(The id or number of the linked
   * firebase project). Some examples of filters: ``` | Filter | Description |
   * |-----------------------------|-------------------------------------------| |
   * parent:accounts/123 | The account with account id: 123. | |
   * parent:properties/123 | The property with property id: 123. | |
   * ancestor:accounts/123 | The account with account id: 123. | |
   * firebase_project:project-id | The firebase project with id: project-id. | |
   * firebase_project:123 | The firebase project with number: 123. | ```
   * @opt_param int pageSize The maximum number of resources to return. The
   * service may return fewer than this value, even if there are additional pages.
   * If unspecified, at most 50 resources will be returned. The maximum value is
   * 200; (higher values will be coerced to the maximum)
   * @opt_param string pageToken A page token, received from a previous
   * `ListProperties` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListProperties` must match the
   * call that provided the page token.
   * @opt_param bool showDeleted Whether to include soft-deleted (ie: "trashed")
   * Properties in the results. Properties can be inspected to determine whether
   * they are deleted or not.
   * @return GoogleAnalyticsAdminV1betaListPropertiesResponse
   * @throws \Google\Service\Exception
   */
  public function listProperties($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAnalyticsAdminV1betaListPropertiesResponse::class);
  }
  /**
   * Updates a property. (properties.patch)
   *
   * @param string $name Output only. Resource name of this property. Format:
   * properties/{property_id} Example: "properties/1000"
   * @param GoogleAnalyticsAdminV1betaProperty $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * Field names must be in snake case (e.g., "field_to_update"). Omitted fields
   * will not be updated. To replace the entire entity, use one path with the
   * string "*" to match all fields.
   * @return GoogleAnalyticsAdminV1betaProperty
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleAnalyticsAdminV1betaProperty $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleAnalyticsAdminV1betaProperty::class);
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
   * (properties.runAccessReport)
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
   * Updates the singleton data retention settings for this property.
   * (properties.updateDataRetentionSettings)
   *
   * @param string $name Output only. Resource name for this DataRetentionSetting
   * resource. Format: properties/{property}/dataRetentionSettings
   * @param GoogleAnalyticsAdminV1betaDataRetentionSettings $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * Field names must be in snake case (e.g., "field_to_update"). Omitted fields
   * will not be updated. To replace the entire entity, use one path with the
   * string "*" to match all fields.
   * @return GoogleAnalyticsAdminV1betaDataRetentionSettings
   * @throws \Google\Service\Exception
   */
  public function updateDataRetentionSettings($name, GoogleAnalyticsAdminV1betaDataRetentionSettings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateDataRetentionSettings', [$params], GoogleAnalyticsAdminV1betaDataRetentionSettings::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Properties::class, 'Google_Service_GoogleAnalyticsAdmin_Resource_Properties');
