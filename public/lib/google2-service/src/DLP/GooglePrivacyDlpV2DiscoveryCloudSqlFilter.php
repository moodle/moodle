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

class GooglePrivacyDlpV2DiscoveryCloudSqlFilter extends \Google\Model
{
  protected $collectionType = GooglePrivacyDlpV2DatabaseResourceCollection::class;
  protected $collectionDataType = '';
  protected $databaseResourceReferenceType = GooglePrivacyDlpV2DatabaseResourceReference::class;
  protected $databaseResourceReferenceDataType = '';
  protected $othersType = GooglePrivacyDlpV2AllOtherDatabaseResources::class;
  protected $othersDataType = '';

  /**
   * A specific set of database resources for this filter to apply to.
   *
   * @param GooglePrivacyDlpV2DatabaseResourceCollection $collection
   */
  public function setCollection(GooglePrivacyDlpV2DatabaseResourceCollection $collection)
  {
    $this->collection = $collection;
  }
  /**
   * @return GooglePrivacyDlpV2DatabaseResourceCollection
   */
  public function getCollection()
  {
    return $this->collection;
  }
  /**
   * The database resource to scan. Targets including this can only include one
   * target (the target with this database resource reference).
   *
   * @param GooglePrivacyDlpV2DatabaseResourceReference $databaseResourceReference
   */
  public function setDatabaseResourceReference(GooglePrivacyDlpV2DatabaseResourceReference $databaseResourceReference)
  {
    $this->databaseResourceReference = $databaseResourceReference;
  }
  /**
   * @return GooglePrivacyDlpV2DatabaseResourceReference
   */
  public function getDatabaseResourceReference()
  {
    return $this->databaseResourceReference;
  }
  /**
   * Catch-all. This should always be the last target in the list because
   * anything above it will apply first. Should only appear once in a
   * configuration. If none is specified, a default one will be added
   * automatically.
   *
   * @param GooglePrivacyDlpV2AllOtherDatabaseResources $others
   */
  public function setOthers(GooglePrivacyDlpV2AllOtherDatabaseResources $others)
  {
    $this->others = $others;
  }
  /**
   * @return GooglePrivacyDlpV2AllOtherDatabaseResources
   */
  public function getOthers()
  {
    return $this->others;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryCloudSqlFilter::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryCloudSqlFilter');
