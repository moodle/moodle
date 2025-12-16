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

use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaConversionEvent;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaListConversionEventsResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleProtobufEmpty;

/**
 * The "conversionEvents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsadminService = new Google\Service\GoogleAnalyticsAdmin(...);
 *   $conversionEvents = $analyticsadminService->properties_conversionEvents;
 *  </code>
 */
class PropertiesConversionEvents extends \Google\Service\Resource
{
  /**
   * Deprecated: Use `CreateKeyEvent` instead. Creates a conversion event with the
   * specified attributes. (conversionEvents.create)
   *
   * @param string $parent Required. The resource name of the parent property
   * where this conversion event will be created. Format: properties/123
   * @param GoogleAnalyticsAdminV1betaConversionEvent $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaConversionEvent
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleAnalyticsAdminV1betaConversionEvent $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleAnalyticsAdminV1betaConversionEvent::class);
  }
  /**
   * Deprecated: Use `DeleteKeyEvent` instead. Deletes a conversion event in a
   * property. (conversionEvents.delete)
   *
   * @param string $name Required. The resource name of the conversion event to
   * delete. Format: properties/{property}/conversionEvents/{conversion_event}
   * Example: "properties/123/conversionEvents/456"
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
   * Deprecated: Use `GetKeyEvent` instead. Retrieve a single conversion event.
   * (conversionEvents.get)
   *
   * @param string $name Required. The resource name of the conversion event to
   * retrieve. Format: properties/{property}/conversionEvents/{conversion_event}
   * Example: "properties/123/conversionEvents/456"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaConversionEvent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAnalyticsAdminV1betaConversionEvent::class);
  }
  /**
   * Deprecated: Use `ListKeyEvents` instead. Returns a list of conversion events
   * in the specified parent property. Returns an empty list if no conversion
   * events are found. (conversionEvents.listPropertiesConversionEvents)
   *
   * @param string $parent Required. The resource name of the parent property.
   * Example: 'properties/123'
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of resources to return. If
   * unspecified, at most 50 resources will be returned. The maximum value is 200;
   * (higher values will be coerced to the maximum)
   * @opt_param string pageToken A page token, received from a previous
   * `ListConversionEvents` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListConversionEvents` must
   * match the call that provided the page token.
   * @return GoogleAnalyticsAdminV1betaListConversionEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listPropertiesConversionEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAnalyticsAdminV1betaListConversionEventsResponse::class);
  }
  /**
   * Deprecated: Use `UpdateKeyEvent` instead. Updates a conversion event with the
   * specified attributes. (conversionEvents.patch)
   *
   * @param string $name Output only. Resource name of this conversion event.
   * Format: properties/{property}/conversionEvents/{conversion_event}
   * @param GoogleAnalyticsAdminV1betaConversionEvent $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * Field names must be in snake case (e.g., "field_to_update"). Omitted fields
   * will not be updated. To replace the entire entity, use one path with the
   * string "*" to match all fields.
   * @return GoogleAnalyticsAdminV1betaConversionEvent
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleAnalyticsAdminV1betaConversionEvent $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleAnalyticsAdminV1betaConversionEvent::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertiesConversionEvents::class, 'Google_Service_GoogleAnalyticsAdmin_Resource_PropertiesConversionEvents');
