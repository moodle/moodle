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

use Google\Service\CloudResourceManager\Operation;
use Google\Service\CloudResourceManager\TagBindingCollection;

/**
 * The "tagBindingCollections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudresourcemanagerService = new Google\Service\CloudResourceManager(...);
 *   $tagBindingCollections = $cloudresourcemanagerService->locations_tagBindingCollections;
 *  </code>
 */
class LocationsTagBindingCollections extends \Google\Service\Resource
{
  /**
   * Returns tag bindings directly attached to a GCP resource.
   * (tagBindingCollections.get)
   *
   * @param string $name Required. The full name of the TagBindingCollection in
   * format: `locations/{location}/tagBindingCollections/{encoded-full-resource-
   * name}` where the enoded-full-resource-name is the UTF-8 encoded name of the
   * resource the TagBindings are bound to. E.g. "locations/global/tagBindingColle
   * ctions/%2f%2fcloudresourcemanager.googleapis.com%2fprojects%2f123"
   * @param array $optParams Optional parameters.
   * @return TagBindingCollection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TagBindingCollection::class);
  }
  /**
   * Updates tag bindings directly attached to a GCP resource. Update_mask can be
   * kept empty or "*". (tagBindingCollections.patch)
   *
   * @param string $name Identifier. The name of the TagBindingCollection,
   * following the convention:
   * `locations/{location}/tagBindingCollections/{encoded-full-resource-name}`
   * where the encoded-full-resource-name is the UTF-8 encoded name of the GCP
   * resource the TagBindings are bound to. "locations/global/tagBindingCollection
   * s/%2f%2fcloudresourcemanager.googleapis.com%2fprojects%2f123"
   * @param TagBindingCollection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. An update mask to selectively update
   * fields.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, TagBindingCollection $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationsTagBindingCollections::class, 'Google_Service_CloudResourceManager_Resource_LocationsTagBindingCollections');
