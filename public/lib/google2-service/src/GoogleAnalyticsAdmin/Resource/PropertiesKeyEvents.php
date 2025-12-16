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

use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaKeyEvent;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaListKeyEventsResponse;
use Google\Service\GoogleAnalyticsAdmin\GoogleProtobufEmpty;

/**
 * The "keyEvents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsadminService = new Google\Service\GoogleAnalyticsAdmin(...);
 *   $keyEvents = $analyticsadminService->properties_keyEvents;
 *  </code>
 */
class PropertiesKeyEvents extends \Google\Service\Resource
{
  /**
   * Creates a Key Event. (keyEvents.create)
   *
   * @param string $parent Required. The resource name of the parent property
   * where this Key Event will be created. Format: properties/123
   * @param GoogleAnalyticsAdminV1betaKeyEvent $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaKeyEvent
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleAnalyticsAdminV1betaKeyEvent $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleAnalyticsAdminV1betaKeyEvent::class);
  }
  /**
   * Deletes a Key Event. (keyEvents.delete)
   *
   * @param string $name Required. The resource name of the Key Event to delete.
   * Format: properties/{property}/keyEvents/{key_event} Example:
   * "properties/123/keyEvents/456"
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
   * Retrieve a single Key Event. (keyEvents.get)
   *
   * @param string $name Required. The resource name of the Key Event to retrieve.
   * Format: properties/{property}/keyEvents/{key_event} Example:
   * "properties/123/keyEvents/456"
   * @param array $optParams Optional parameters.
   * @return GoogleAnalyticsAdminV1betaKeyEvent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAnalyticsAdminV1betaKeyEvent::class);
  }
  /**
   * Returns a list of Key Events in the specified parent property. Returns an
   * empty list if no Key Events are found. (keyEvents.listPropertiesKeyEvents)
   *
   * @param string $parent Required. The resource name of the parent property.
   * Example: 'properties/123'
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of resources to return. If
   * unspecified, at most 50 resources will be returned. The maximum value is 200;
   * (higher values will be coerced to the maximum)
   * @opt_param string pageToken A page token, received from a previous
   * `ListKeyEvents` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListKeyEvents` must match the
   * call that provided the page token.
   * @return GoogleAnalyticsAdminV1betaListKeyEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listPropertiesKeyEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAnalyticsAdminV1betaListKeyEventsResponse::class);
  }
  /**
   * Updates a Key Event. (keyEvents.patch)
   *
   * @param string $name Output only. Resource name of this key event. Format:
   * properties/{property}/keyEvents/{key_event}
   * @param GoogleAnalyticsAdminV1betaKeyEvent $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * Field names must be in snake case (e.g., "field_to_update"). Omitted fields
   * will not be updated. To replace the entire entity, use one path with the
   * string "*" to match all fields.
   * @return GoogleAnalyticsAdminV1betaKeyEvent
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleAnalyticsAdminV1betaKeyEvent $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleAnalyticsAdminV1betaKeyEvent::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertiesKeyEvents::class, 'Google_Service_GoogleAnalyticsAdmin_Resource_PropertiesKeyEvents');
