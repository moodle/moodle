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

namespace Google\Service\CloudAsset;

class ResourceOwners extends \Google\Collection
{
  protected $collection_key = 'resourceOwners';
  /**
   * List of resource owners.
   *
   * @var string[]
   */
  public $resourceOwners;

  /**
   * List of resource owners.
   *
   * @param string[] $resourceOwners
   */
  public function setResourceOwners($resourceOwners)
  {
    $this->resourceOwners = $resourceOwners;
  }
  /**
   * @return string[]
   */
  public function getResourceOwners()
  {
    return $this->resourceOwners;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceOwners::class, 'Google_Service_CloudAsset_ResourceOwners');
