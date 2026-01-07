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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2FileStoreCollection extends \Google\Model
{
  protected $includeRegexesType = GooglePrivacyDlpV2FileStoreRegexes::class;
  protected $includeRegexesDataType = '';
  protected $includeTagsType = GooglePrivacyDlpV2TagFilters::class;
  protected $includeTagsDataType = '';

  /**
   * Optional. A collection of regular expressions to match a file store
   * against.
   *
   * @param GooglePrivacyDlpV2FileStoreRegexes $includeRegexes
   */
  public function setIncludeRegexes(GooglePrivacyDlpV2FileStoreRegexes $includeRegexes)
  {
    $this->includeRegexes = $includeRegexes;
  }
  /**
   * @return GooglePrivacyDlpV2FileStoreRegexes
   */
  public function getIncludeRegexes()
  {
    return $this->includeRegexes;
  }
  /**
   * Optional. To be included in the collection, a resource must meet all of the
   * following requirements: - If tag filters are provided, match all provided
   * tag filters. - If one or more patterns are specified, match at least one
   * pattern. For a resource to match the tag filters, the resource must have
   * all of the provided tags attached. Tags refer to Resource Manager tags
   * bound to the resource or its ancestors. For more information, see [Manage
   * schedules](https://cloud.google.com/sensitive-data-protection/docs/profile-
   * project-cloud-storage#manage-schedules).
   *
   * @param GooglePrivacyDlpV2TagFilters $includeTags
   */
  public function setIncludeTags(GooglePrivacyDlpV2TagFilters $includeTags)
  {
    $this->includeTags = $includeTags;
  }
  /**
   * @return GooglePrivacyDlpV2TagFilters
   */
  public function getIncludeTags()
  {
    return $this->includeTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2FileStoreCollection::class, 'Google_Service_DLP_GooglePrivacyDlpV2FileStoreCollection');
