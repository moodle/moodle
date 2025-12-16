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

namespace Google\Service\CloudSearch;

class DataSource extends \Google\Collection
{
  protected $collection_key = 'operationIds';
  /**
   * If true, sets the datasource to read-only mode. In read-only mode, the
   * Indexing API rejects any requests to index or delete items in this source.
   * Enabling read-only mode does not stop the processing of previously accepted
   * data.
   *
   * @var bool
   */
  public $disableModifications;
  /**
   * Disable serving any search or assist results.
   *
   * @var bool
   */
  public $disableServing;
  /**
   * Required. Display name of the datasource The maximum length is 300
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * List of service accounts that have indexing access.
   *
   * @var string[]
   */
  public $indexingServiceAccounts;
  protected $itemsVisibilityType = GSuitePrincipal::class;
  protected $itemsVisibilityDataType = 'array';
  /**
   * The name of the datasource resource. Format: datasources/{source_id}. The
   * name is ignored when creating a datasource.
   *
   * @var string
   */
  public $name;
  /**
   * IDs of the Long Running Operations (LROs) currently running for this
   * schema.
   *
   * @var string[]
   */
  public $operationIds;
  /**
   * Can a user request to get thumbnail URI for Items indexed in this data
   * source.
   *
   * @var bool
   */
  public $returnThumbnailUrls;
  /**
   * A short name or alias for the source. This value will be used to match the
   * 'source' operator. For example, if the short name is ** then queries like
   * *source:* will only return results for this source. The value must be
   * unique across all datasources. The value must only contain alphanumeric
   * characters (a-zA-Z0-9). The value cannot start with 'google' and cannot be
   * one of the following: mail, gmail, docs, drive, groups, sites, calendar,
   * hangouts, gplus, keep, people, teams. Its maximum length is 32 characters.
   *
   * @var string
   */
  public $shortName;

  /**
   * If true, sets the datasource to read-only mode. In read-only mode, the
   * Indexing API rejects any requests to index or delete items in this source.
   * Enabling read-only mode does not stop the processing of previously accepted
   * data.
   *
   * @param bool $disableModifications
   */
  public function setDisableModifications($disableModifications)
  {
    $this->disableModifications = $disableModifications;
  }
  /**
   * @return bool
   */
  public function getDisableModifications()
  {
    return $this->disableModifications;
  }
  /**
   * Disable serving any search or assist results.
   *
   * @param bool $disableServing
   */
  public function setDisableServing($disableServing)
  {
    $this->disableServing = $disableServing;
  }
  /**
   * @return bool
   */
  public function getDisableServing()
  {
    return $this->disableServing;
  }
  /**
   * Required. Display name of the datasource The maximum length is 300
   * characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * List of service accounts that have indexing access.
   *
   * @param string[] $indexingServiceAccounts
   */
  public function setIndexingServiceAccounts($indexingServiceAccounts)
  {
    $this->indexingServiceAccounts = $indexingServiceAccounts;
  }
  /**
   * @return string[]
   */
  public function getIndexingServiceAccounts()
  {
    return $this->indexingServiceAccounts;
  }
  /**
   * This field restricts visibility to items at the datasource level. Items
   * within the datasource are restricted to the union of users and groups
   * included in this field. Note that, this does not ensure access to a
   * specific item, as users need to have ACL permissions on the contained
   * items. This ensures a high level access on the entire datasource, and that
   * the individual items are not shared outside this visibility.
   *
   * @param GSuitePrincipal[] $itemsVisibility
   */
  public function setItemsVisibility($itemsVisibility)
  {
    $this->itemsVisibility = $itemsVisibility;
  }
  /**
   * @return GSuitePrincipal[]
   */
  public function getItemsVisibility()
  {
    return $this->itemsVisibility;
  }
  /**
   * The name of the datasource resource. Format: datasources/{source_id}. The
   * name is ignored when creating a datasource.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * IDs of the Long Running Operations (LROs) currently running for this
   * schema.
   *
   * @param string[] $operationIds
   */
  public function setOperationIds($operationIds)
  {
    $this->operationIds = $operationIds;
  }
  /**
   * @return string[]
   */
  public function getOperationIds()
  {
    return $this->operationIds;
  }
  /**
   * Can a user request to get thumbnail URI for Items indexed in this data
   * source.
   *
   * @param bool $returnThumbnailUrls
   */
  public function setReturnThumbnailUrls($returnThumbnailUrls)
  {
    $this->returnThumbnailUrls = $returnThumbnailUrls;
  }
  /**
   * @return bool
   */
  public function getReturnThumbnailUrls()
  {
    return $this->returnThumbnailUrls;
  }
  /**
   * A short name or alias for the source. This value will be used to match the
   * 'source' operator. For example, if the short name is ** then queries like
   * *source:* will only return results for this source. The value must be
   * unique across all datasources. The value must only contain alphanumeric
   * characters (a-zA-Z0-9). The value cannot start with 'google' and cannot be
   * one of the following: mail, gmail, docs, drive, groups, sites, calendar,
   * hangouts, gplus, keep, people, teams. Its maximum length is 32 characters.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSource::class, 'Google_Service_CloudSearch_DataSource');
