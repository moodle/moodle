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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource extends \Google\Collection
{
  protected $collection_key = 'identityMappingEntries';
  protected $identityMappingEntriesType = GoogleCloudDiscoveryengineV1IdentityMappingEntry::class;
  protected $identityMappingEntriesDataType = 'array';

  /**
   * A maximum of 10000 entries can be purged at one time
   *
   * @param GoogleCloudDiscoveryengineV1IdentityMappingEntry[] $identityMappingEntries
   */
  public function setIdentityMappingEntries($identityMappingEntries)
  {
    $this->identityMappingEntries = $identityMappingEntries;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1IdentityMappingEntry[]
   */
  public function getIdentityMappingEntries()
  {
    return $this->identityMappingEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource');
