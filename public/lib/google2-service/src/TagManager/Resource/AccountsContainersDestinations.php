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

namespace Google\Service\TagManager\Resource;

use Google\Service\TagManager\Destination;
use Google\Service\TagManager\ListDestinationsResponse;

/**
 * The "destinations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tagmanagerService = new Google\Service\TagManager(...);
 *   $destinations = $tagmanagerService->accounts_containers_destinations;
 *  </code>
 */
class AccountsContainersDestinations extends \Google\Service\Resource
{
  /**
   * Gets a Destination. (destinations.get)
   *
   * @param string $path Google Tag Destination's API relative path.
   * @param array $optParams Optional parameters.
   * @return Destination
   * @throws \Google\Service\Exception
   */
  public function get($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Destination::class);
  }
  /**
   * Adds a Destination to this Container and removes it from the Container to
   * which it is currently linked. (destinations.link)
   *
   * @param string $parent GTM parent Container's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowUserPermissionFeatureUpdate Must be set to true to allow
   * features.user_permissions to change from false to true. If this operation
   * causes an update but this bit is false, the operation will fail.
   * @opt_param string destinationId Destination ID to be linked to the current
   * container.
   * @return Destination
   * @throws \Google\Service\Exception
   */
  public function link($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('link', [$params], Destination::class);
  }
  /**
   * Lists all Destinations linked to a GTM Container.
   * (destinations.listAccountsContainersDestinations)
   *
   * @param string $parent GTM parent Container's API relative path.
   * @param array $optParams Optional parameters.
   * @return ListDestinationsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsContainersDestinations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDestinationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsContainersDestinations::class, 'Google_Service_TagManager_Resource_AccountsContainersDestinations');
