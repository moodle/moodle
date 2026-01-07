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

class GoogleCloudDatacatalogV1TableSpec extends \Google\Model
{
  /**
   * Output only. If the table is date-sharded, that is, it matches the
   * `[prefix]YYYYMMDD` name pattern, this field is the Data Catalog resource
   * name of the date-sharded grouped entry. For example: `projects/{PROJECT_ID}
   * /locations/{LOCATION}/entrygroups/{ENTRY_GROUP_ID}/entries/{ENTRY_ID}`.
   * Otherwise, `grouped_entry` is empty.
   *
   * @var string
   */
  public $groupedEntry;

  /**
   * Output only. If the table is date-sharded, that is, it matches the
   * `[prefix]YYYYMMDD` name pattern, this field is the Data Catalog resource
   * name of the date-sharded grouped entry. For example: `projects/{PROJECT_ID}
   * /locations/{LOCATION}/entrygroups/{ENTRY_GROUP_ID}/entries/{ENTRY_ID}`.
   * Otherwise, `grouped_entry` is empty.
   *
   * @param string $groupedEntry
   */
  public function setGroupedEntry($groupedEntry)
  {
    $this->groupedEntry = $groupedEntry;
  }
  /**
   * @return string
   */
  public function getGroupedEntry()
  {
    return $this->groupedEntry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TableSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TableSpec');
