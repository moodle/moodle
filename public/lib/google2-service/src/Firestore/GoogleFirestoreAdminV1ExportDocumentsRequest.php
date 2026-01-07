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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1ExportDocumentsRequest extends \Google\Collection
{
  protected $collection_key = 'namespaceIds';
  /**
   * IDs of the collection groups to export. Unspecified means all collection
   * groups. Each collection group in this list must be unique.
   *
   * @var string[]
   */
  public $collectionIds;
  /**
   * An empty list represents all namespaces. This is the preferred usage for
   * databases that don't use namespaces. An empty string element represents the
   * default namespace. This should be used if the database has data in non-
   * default namespaces, but doesn't want to include them. Each namespace in
   * this list must be unique.
   *
   * @var string[]
   */
  public $namespaceIds;
  /**
   * The output URI. Currently only supports Google Cloud Storage URIs of the
   * form: `gs://BUCKET_NAME[/NAMESPACE_PATH]`, where `BUCKET_NAME` is the name
   * of the Google Cloud Storage bucket and `NAMESPACE_PATH` is an optional
   * Google Cloud Storage namespace path. When choosing a name, be sure to
   * consider Google Cloud Storage naming guidelines:
   * https://cloud.google.com/storage/docs/naming. If the URI is a bucket
   * (without a namespace path), a prefix will be generated based on the start
   * time.
   *
   * @var string
   */
  public $outputUriPrefix;
  /**
   * The timestamp that corresponds to the version of the database to be
   * exported. The timestamp must be in the past, rounded to the minute and not
   * older than earliestVersionTime. If specified, then the exported documents
   * will represent a consistent view of the database at the provided time.
   * Otherwise, there are no guarantees about the consistency of the exported
   * documents.
   *
   * @var string
   */
  public $snapshotTime;

  /**
   * IDs of the collection groups to export. Unspecified means all collection
   * groups. Each collection group in this list must be unique.
   *
   * @param string[] $collectionIds
   */
  public function setCollectionIds($collectionIds)
  {
    $this->collectionIds = $collectionIds;
  }
  /**
   * @return string[]
   */
  public function getCollectionIds()
  {
    return $this->collectionIds;
  }
  /**
   * An empty list represents all namespaces. This is the preferred usage for
   * databases that don't use namespaces. An empty string element represents the
   * default namespace. This should be used if the database has data in non-
   * default namespaces, but doesn't want to include them. Each namespace in
   * this list must be unique.
   *
   * @param string[] $namespaceIds
   */
  public function setNamespaceIds($namespaceIds)
  {
    $this->namespaceIds = $namespaceIds;
  }
  /**
   * @return string[]
   */
  public function getNamespaceIds()
  {
    return $this->namespaceIds;
  }
  /**
   * The output URI. Currently only supports Google Cloud Storage URIs of the
   * form: `gs://BUCKET_NAME[/NAMESPACE_PATH]`, where `BUCKET_NAME` is the name
   * of the Google Cloud Storage bucket and `NAMESPACE_PATH` is an optional
   * Google Cloud Storage namespace path. When choosing a name, be sure to
   * consider Google Cloud Storage naming guidelines:
   * https://cloud.google.com/storage/docs/naming. If the URI is a bucket
   * (without a namespace path), a prefix will be generated based on the start
   * time.
   *
   * @param string $outputUriPrefix
   */
  public function setOutputUriPrefix($outputUriPrefix)
  {
    $this->outputUriPrefix = $outputUriPrefix;
  }
  /**
   * @return string
   */
  public function getOutputUriPrefix()
  {
    return $this->outputUriPrefix;
  }
  /**
   * The timestamp that corresponds to the version of the database to be
   * exported. The timestamp must be in the past, rounded to the minute and not
   * older than earliestVersionTime. If specified, then the exported documents
   * will represent a consistent view of the database at the provided time.
   * Otherwise, there are no guarantees about the consistency of the exported
   * documents.
   *
   * @param string $snapshotTime
   */
  public function setSnapshotTime($snapshotTime)
  {
    $this->snapshotTime = $snapshotTime;
  }
  /**
   * @return string
   */
  public function getSnapshotTime()
  {
    return $this->snapshotTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1ExportDocumentsRequest::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1ExportDocumentsRequest');
