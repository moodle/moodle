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

namespace Google\Service\Logging;

class TailLogEntriesRequest extends \Google\Collection
{
  protected $collection_key = 'resourceNames';
  /**
   * Optional. The amount of time to buffer log entries at the server before
   * being returned to prevent out of order results due to late arriving log
   * entries. Valid values are between 0-60000 milliseconds. Defaults to 2000
   * milliseconds.
   *
   * @var string
   */
  public $bufferWindow;
  /**
   * Optional. Only log entries that match the filter are returned. An empty
   * filter matches all log entries in the resources listed in resource_names.
   * Referencing a parent resource that is not listed in resource_names will
   * cause the filter to return no results. The maximum length of a filter is
   * 20,000 characters.
   *
   * @var string
   */
  public $filter;
  /**
   * Required. Name of a parent resource from which to retrieve log entries:
   * projects/[PROJECT_ID] organizations/[ORGANIZATION_ID]
   * billingAccounts/[BILLING_ACCOUNT_ID] folders/[FOLDER_ID]May alternatively
   * be one or more views: projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets
   * /[BUCKET_ID]/views/[VIEW_ID] organizations/[ORGANIZATION_ID]/locations/[LOC
   * ATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] billingAccounts/[BILLING_ACCO
   * UNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] folders
   * /[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID]
   *
   * @var string[]
   */
  public $resourceNames;

  /**
   * Optional. The amount of time to buffer log entries at the server before
   * being returned to prevent out of order results due to late arriving log
   * entries. Valid values are between 0-60000 milliseconds. Defaults to 2000
   * milliseconds.
   *
   * @param string $bufferWindow
   */
  public function setBufferWindow($bufferWindow)
  {
    $this->bufferWindow = $bufferWindow;
  }
  /**
   * @return string
   */
  public function getBufferWindow()
  {
    return $this->bufferWindow;
  }
  /**
   * Optional. Only log entries that match the filter are returned. An empty
   * filter matches all log entries in the resources listed in resource_names.
   * Referencing a parent resource that is not listed in resource_names will
   * cause the filter to return no results. The maximum length of a filter is
   * 20,000 characters.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. Name of a parent resource from which to retrieve log entries:
   * projects/[PROJECT_ID] organizations/[ORGANIZATION_ID]
   * billingAccounts/[BILLING_ACCOUNT_ID] folders/[FOLDER_ID]May alternatively
   * be one or more views: projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets
   * /[BUCKET_ID]/views/[VIEW_ID] organizations/[ORGANIZATION_ID]/locations/[LOC
   * ATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] billingAccounts/[BILLING_ACCO
   * UNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] folders
   * /[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID]
   *
   * @param string[] $resourceNames
   */
  public function setResourceNames($resourceNames)
  {
    $this->resourceNames = $resourceNames;
  }
  /**
   * @return string[]
   */
  public function getResourceNames()
  {
    return $this->resourceNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TailLogEntriesRequest::class, 'Google_Service_Logging_TailLogEntriesRequest');
