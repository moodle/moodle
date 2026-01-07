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

class GoogleCloudDatacatalogV1SearchCatalogRequestScope extends \Google\Collection
{
  protected $collection_key = 'restrictedLocations';
  /**
   * If `true`, include Google Cloud public datasets in search results. By
   * default, they are excluded. See [Google Cloud Public Datasets](/public-
   * datasets) for more information.
   *
   * @var bool
   */
  public $includeGcpPublicDatasets;
  /**
   * The list of organization IDs to search within. To find your organization
   * ID, follow the steps from [Creating and managing organizations] (/resource-
   * manager/docs/creating-managing-organization).
   *
   * @var string[]
   */
  public $includeOrgIds;
  /**
   * The list of project IDs to search within. For more information on the
   * distinction between project names, IDs, and numbers, see
   * [Projects](/docs/overview/#projects).
   *
   * @var string[]
   */
  public $includeProjectIds;
  /**
   * Optional. This field is deprecated. The search mechanism for public and
   * private tag templates is the same.
   *
   * @deprecated
   * @var bool
   */
  public $includePublicTagTemplates;
  /**
   * Optional. The list of locations to search within. If empty, all locations
   * are searched. Returns an error if any location in the list isn't one of the
   * [Supported regions](https://cloud.google.com/data-
   * catalog/docs/concepts/regions#supported_regions). If a location is
   * unreachable, its name is returned in the
   * `SearchCatalogResponse.unreachable` field. To get additional information on
   * the error, repeat the search request and set the location name as the value
   * of this parameter.
   *
   * @var string[]
   */
  public $restrictedLocations;
  /**
   * Optional. If `true`, search only among starred entries. By default, all
   * results are returned, starred or not.
   *
   * @var bool
   */
  public $starredOnly;

  /**
   * If `true`, include Google Cloud public datasets in search results. By
   * default, they are excluded. See [Google Cloud Public Datasets](/public-
   * datasets) for more information.
   *
   * @param bool $includeGcpPublicDatasets
   */
  public function setIncludeGcpPublicDatasets($includeGcpPublicDatasets)
  {
    $this->includeGcpPublicDatasets = $includeGcpPublicDatasets;
  }
  /**
   * @return bool
   */
  public function getIncludeGcpPublicDatasets()
  {
    return $this->includeGcpPublicDatasets;
  }
  /**
   * The list of organization IDs to search within. To find your organization
   * ID, follow the steps from [Creating and managing organizations] (/resource-
   * manager/docs/creating-managing-organization).
   *
   * @param string[] $includeOrgIds
   */
  public function setIncludeOrgIds($includeOrgIds)
  {
    $this->includeOrgIds = $includeOrgIds;
  }
  /**
   * @return string[]
   */
  public function getIncludeOrgIds()
  {
    return $this->includeOrgIds;
  }
  /**
   * The list of project IDs to search within. For more information on the
   * distinction between project names, IDs, and numbers, see
   * [Projects](/docs/overview/#projects).
   *
   * @param string[] $includeProjectIds
   */
  public function setIncludeProjectIds($includeProjectIds)
  {
    $this->includeProjectIds = $includeProjectIds;
  }
  /**
   * @return string[]
   */
  public function getIncludeProjectIds()
  {
    return $this->includeProjectIds;
  }
  /**
   * Optional. This field is deprecated. The search mechanism for public and
   * private tag templates is the same.
   *
   * @deprecated
   * @param bool $includePublicTagTemplates
   */
  public function setIncludePublicTagTemplates($includePublicTagTemplates)
  {
    $this->includePublicTagTemplates = $includePublicTagTemplates;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIncludePublicTagTemplates()
  {
    return $this->includePublicTagTemplates;
  }
  /**
   * Optional. The list of locations to search within. If empty, all locations
   * are searched. Returns an error if any location in the list isn't one of the
   * [Supported regions](https://cloud.google.com/data-
   * catalog/docs/concepts/regions#supported_regions). If a location is
   * unreachable, its name is returned in the
   * `SearchCatalogResponse.unreachable` field. To get additional information on
   * the error, repeat the search request and set the location name as the value
   * of this parameter.
   *
   * @param string[] $restrictedLocations
   */
  public function setRestrictedLocations($restrictedLocations)
  {
    $this->restrictedLocations = $restrictedLocations;
  }
  /**
   * @return string[]
   */
  public function getRestrictedLocations()
  {
    return $this->restrictedLocations;
  }
  /**
   * Optional. If `true`, search only among starred entries. By default, all
   * results are returned, starred or not.
   *
   * @param bool $starredOnly
   */
  public function setStarredOnly($starredOnly)
  {
    $this->starredOnly = $starredOnly;
  }
  /**
   * @return bool
   */
  public function getStarredOnly()
  {
    return $this->starredOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SearchCatalogRequestScope::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SearchCatalogRequestScope');
