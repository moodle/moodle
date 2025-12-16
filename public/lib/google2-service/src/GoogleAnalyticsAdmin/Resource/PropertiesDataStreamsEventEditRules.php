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

use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaReorderEventEditRulesRequest;
use Google\Service\GoogleAnalyticsAdmin\GoogleProtobufEmpty;

/**
 * The "eventEditRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsadminService = new Google\Service\GoogleAnalyticsAdmin(...);
 *   $eventEditRules = $analyticsadminService->properties_dataStreams_eventEditRules;
 *  </code>
 */
class PropertiesDataStreamsEventEditRules extends \Google\Service\Resource
{
  /**
   * Changes the processing order of event edit rules on the specified stream.
   * (eventEditRules.reorder)
   *
   * @param string $parent Required. Example format:
   * properties/123/dataStreams/456
   * @param GoogleAnalyticsAdminV1betaReorderEventEditRulesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function reorder($parent, GoogleAnalyticsAdminV1betaReorderEventEditRulesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reorder', [$params], GoogleProtobufEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertiesDataStreamsEventEditRules::class, 'Google_Service_GoogleAnalyticsAdmin_Resource_PropertiesDataStreamsEventEditRules');
