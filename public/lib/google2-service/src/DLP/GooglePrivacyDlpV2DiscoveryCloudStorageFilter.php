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

class GooglePrivacyDlpV2DiscoveryCloudStorageFilter extends \Google\Model
{
  protected $cloudStorageResourceReferenceType = GooglePrivacyDlpV2CloudStorageResourceReference::class;
  protected $cloudStorageResourceReferenceDataType = '';
  protected $collectionType = GooglePrivacyDlpV2FileStoreCollection::class;
  protected $collectionDataType = '';
  protected $othersType = GooglePrivacyDlpV2AllOtherResources::class;
  protected $othersDataType = '';

  /**
   * Optional. The bucket to scan. Targets including this can only include one
   * target (the target with this bucket). This enables profiling the contents
   * of a single bucket, while the other options allow for easy profiling of
   * many bucets within a project or an organization.
   *
   * @param GooglePrivacyDlpV2CloudStorageResourceReference $cloudStorageResourceReference
   */
  public function setCloudStorageResourceReference(GooglePrivacyDlpV2CloudStorageResourceReference $cloudStorageResourceReference)
  {
    $this->cloudStorageResourceReference = $cloudStorageResourceReference;
  }
  /**
   * @return GooglePrivacyDlpV2CloudStorageResourceReference
   */
  public function getCloudStorageResourceReference()
  {
    return $this->cloudStorageResourceReference;
  }
  /**
   * Optional. A specific set of buckets for this filter to apply to.
   *
   * @param GooglePrivacyDlpV2FileStoreCollection $collection
   */
  public function setCollection(GooglePrivacyDlpV2FileStoreCollection $collection)
  {
    $this->collection = $collection;
  }
  /**
   * @return GooglePrivacyDlpV2FileStoreCollection
   */
  public function getCollection()
  {
    return $this->collection;
  }
  /**
   * Optional. Catch-all. This should always be the last target in the list
   * because anything above it will apply first. Should only appear once in a
   * configuration. If none is specified, a default one will be added
   * automatically.
   *
   * @param GooglePrivacyDlpV2AllOtherResources $others
   */
  public function setOthers(GooglePrivacyDlpV2AllOtherResources $others)
  {
    $this->others = $others;
  }
  /**
   * @return GooglePrivacyDlpV2AllOtherResources
   */
  public function getOthers()
  {
    return $this->others;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryCloudStorageFilter::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryCloudStorageFilter');
