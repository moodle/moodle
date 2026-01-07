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

namespace Google\Service\DisplayVideo\Resource;

use Google\Service\DisplayVideo\ActivateManualTriggerRequest;
use Google\Service\DisplayVideo\DeactivateManualTriggerRequest;
use Google\Service\DisplayVideo\ListManualTriggersResponse;
use Google\Service\DisplayVideo\ManualTrigger;

/**
 * The "manualTriggers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $manualTriggers = $displayvideoService->advertisers_manualTriggers;
 *  </code>
 */
class AdvertisersManualTriggers extends \Google\Service\Resource
{
  /**
   * Activates a manual trigger. Each activation of the manual trigger must be at
   * least 5 minutes apart, otherwise an error will be returned. **Warning:** Line
   * Items using manual triggers no longer serve in Display & Video 360. This
   * method will sunset on August 1, 2023. Read our [feature deprecation
   * announcement](/display-video/api/deprecations#features.manual_triggers) for
   * more information. (manualTriggers.activate)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * manual trigger belongs.
   * @param string $triggerId Required. The ID of the manual trigger to activate.
   * @param ActivateManualTriggerRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ManualTrigger
   */
  public function activate($advertiserId, $triggerId, ActivateManualTriggerRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'triggerId' => $triggerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('activate', [$params], ManualTrigger::class);
  }
  /**
   * Creates a new manual trigger. Returns the newly created manual trigger if
   * successful. **Warning:** Line Items using manual triggers no longer serve in
   * Display & Video 360. This method will sunset on August 1, 2023. Read our
   * [feature deprecation announcement](/display-
   * video/api/deprecations#features.manual_triggers) for more information.
   * (manualTriggers.create)
   *
   * @param string $advertiserId Required. Immutable. The unique ID of the
   * advertiser that the manual trigger belongs to.
   * @param ManualTrigger $postBody
   * @param array $optParams Optional parameters.
   * @return ManualTrigger
   */
  public function create($advertiserId, ManualTrigger $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ManualTrigger::class);
  }
  /**
   * Deactivates a manual trigger. **Warning:** Line Items using manual triggers
   * no longer serve in Display & Video 360. This method will sunset on August 1,
   * 2023. Read our [feature deprecation announcement](/display-
   * video/api/deprecations#features.manual_triggers) for more information.
   * (manualTriggers.deactivate)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * manual trigger belongs.
   * @param string $triggerId Required. The ID of the manual trigger to
   * deactivate.
   * @param DeactivateManualTriggerRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ManualTrigger
   */
  public function deactivate($advertiserId, $triggerId, DeactivateManualTriggerRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'triggerId' => $triggerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deactivate', [$params], ManualTrigger::class);
  }
  /**
   * Gets a manual trigger. **Warning:** Line Items using manual triggers no
   * longer serve in Display & Video 360. This method will sunset on August 1,
   * 2023. Read our [feature deprecation announcement](/display-
   * video/api/deprecations#features.manual_triggers) for more information.
   * (manualTriggers.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser this manual
   * trigger belongs to.
   * @param string $triggerId Required. The ID of the manual trigger to fetch.
   * @param array $optParams Optional parameters.
   * @return ManualTrigger
   */
  public function get($advertiserId, $triggerId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'triggerId' => $triggerId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ManualTrigger::class);
  }
  /**
   * Lists manual triggers that are accessible to the current user for a given
   * advertiser ID. The order is defined by the order_by parameter. A single
   * advertiser_id is required. **Warning:** Line Items using manual triggers no
   * longer serve in Display & Video 360. This method will sunset on August 1,
   * 2023. Read our [feature deprecation announcement](/display-
   * video/api/deprecations#features.manual_triggers) for more information.
   * (manualTriggers.listAdvertisersManualTriggers)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * fetched manual triggers belong to.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering by manual trigger fields. Supported
   * syntax: * Filter expressions are made up of one or more restrictions. *
   * Restrictions can be combined by `AND` or `OR` logical operators. A sequence
   * of restrictions implicitly uses `AND`. * A restriction has the form of
   * `{field} {operator} {value}`. * All fields must use the `EQUALS (=)`
   * operator. Supported fields: * `displayName` * `state` Examples: * All active
   * manual triggers under an advertiser: `state="ACTIVE"` The length of this
   * field should be no more than 500 characters. Reference our [filter `LIST`
   * requests](/display-video/api/guides/how-tos/filters) guide for more
   * information.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `displayName` (default) * `state` The default sorting order is
   * ascending. To specify descending order for a field, a suffix "desc" should be
   * added to the field name. For example, `displayName desc`.
   * @opt_param int pageSize Requested page size. Must be between `1` and `200`.
   * If unspecified will default to `100`.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of next_page_token returned from
   * the previous call to `ListManualTriggers` method. If not specified, the first
   * page of results will be returned.
   * @return ListManualTriggersResponse
   */
  public function listAdvertisersManualTriggers($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListManualTriggersResponse::class);
  }
  /**
   * Updates a manual trigger. Returns the updated manual trigger if successful.
   * **Warning:** Line Items using manual triggers no longer serve in Display &
   * Video 360. This method will sunset on August 1, 2023. Read our [feature
   * deprecation announcement](/display-
   * video/api/deprecations#features.manual_triggers) for more information.
   * (manualTriggers.patch)
   *
   * @param string $advertiserId Required. Immutable. The unique ID of the
   * advertiser that the manual trigger belongs to.
   * @param string $triggerId Output only. The unique ID of the manual trigger.
   * @param ManualTrigger $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The mask to control which fields to
   * update.
   * @return ManualTrigger
   */
  public function patch($advertiserId, $triggerId, ManualTrigger $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'triggerId' => $triggerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ManualTrigger::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersManualTriggers::class, 'Google_Service_DisplayVideo_Resource_AdvertisersManualTriggers');
