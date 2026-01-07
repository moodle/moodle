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

namespace Google\Service\CloudResourceManager\Resource;

use Google\Service\CloudResourceManager\EffectiveTagBindingCollection;

/**
 * The "effectiveTagBindingCollections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudresourcemanagerService = new Google\Service\CloudResourceManager(...);
 *   $effectiveTagBindingCollections = $cloudresourcemanagerService->locations_effectiveTagBindingCollections;
 *  </code>
 */
class LocationsEffectiveTagBindingCollections extends \Google\Service\Resource
{
  /**
   * Returns effective tag bindings on a GCP resource.
   * (effectiveTagBindingCollections.get)
   *
   * @param string $name Required. The full name of the
   * EffectiveTagBindingCollection in format:
   * `locations/{location}/effectiveTagBindingCollections/{encoded-full-resource-
   * name}` where the encoded-full-resource-name is the UTF-8 encoded name of the
   * resource the TagBindings are bound to. E.g. "locations/global/effectiveTagBin
   * dingCollections/%2f%2fcloudresourcemanager.googleapis.com%2fprojects%2f123"
   * @param array $optParams Optional parameters.
   * @return EffectiveTagBindingCollection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], EffectiveTagBindingCollection::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationsEffectiveTagBindingCollections::class, 'Google_Service_CloudResourceManager_Resource_LocationsEffectiveTagBindingCollections');
