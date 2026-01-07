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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1DumpItem extends \Google\Model
{
  protected $taggedEntryType = GoogleCloudDatacatalogV1TaggedEntry::class;
  protected $taggedEntryDataType = '';

  /**
   * Entry and its tags.
   *
   * @param GoogleCloudDatacatalogV1TaggedEntry $taggedEntry
   */
  public function setTaggedEntry(GoogleCloudDatacatalogV1TaggedEntry $taggedEntry)
  {
    $this->taggedEntry = $taggedEntry;
  }
  /**
   * @return GoogleCloudDatacatalogV1TaggedEntry
   */
  public function getTaggedEntry()
  {
    return $this->taggedEntry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1DumpItem::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1DumpItem');
