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

class GoogleCloudDatacatalogV1TaggedEntry extends \Google\Collection
{
  protected $collection_key = 'presentTags';
  protected $absentTagsType = GoogleCloudDatacatalogV1Tag::class;
  protected $absentTagsDataType = 'array';
  protected $presentTagsType = GoogleCloudDatacatalogV1Tag::class;
  protected $presentTagsDataType = 'array';
  protected $v1EntryType = GoogleCloudDatacatalogV1Entry::class;
  protected $v1EntryDataType = '';

  /**
   * Optional. Tags that should be deleted from the Data Catalog. Caller should
   * populate template name and column only.
   *
   * @param GoogleCloudDatacatalogV1Tag[] $absentTags
   */
  public function setAbsentTags($absentTags)
  {
    $this->absentTags = $absentTags;
  }
  /**
   * @return GoogleCloudDatacatalogV1Tag[]
   */
  public function getAbsentTags()
  {
    return $this->absentTags;
  }
  /**
   * Optional. Tags that should be ingested into the Data Catalog. Caller should
   * populate template name, column and fields.
   *
   * @param GoogleCloudDatacatalogV1Tag[] $presentTags
   */
  public function setPresentTags($presentTags)
  {
    $this->presentTags = $presentTags;
  }
  /**
   * @return GoogleCloudDatacatalogV1Tag[]
   */
  public function getPresentTags()
  {
    return $this->presentTags;
  }
  /**
   * Non-encrypted Data Catalog v1 Entry.
   *
   * @param GoogleCloudDatacatalogV1Entry $v1Entry
   */
  public function setV1Entry(GoogleCloudDatacatalogV1Entry $v1Entry)
  {
    $this->v1Entry = $v1Entry;
  }
  /**
   * @return GoogleCloudDatacatalogV1Entry
   */
  public function getV1Entry()
  {
    return $this->v1Entry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TaggedEntry::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TaggedEntry');
