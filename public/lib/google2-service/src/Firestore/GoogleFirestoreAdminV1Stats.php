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

class GoogleFirestoreAdminV1Stats extends \Google\Model
{
  /**
   * Output only. The total number of documents contained in the backup.
   *
   * @var string
   */
  public $documentCount;
  /**
   * Output only. The total number of index entries contained in the backup.
   *
   * @var string
   */
  public $indexCount;
  /**
   * Output only. Summation of the size of all documents and index entries in
   * the backup, measured in bytes.
   *
   * @var string
   */
  public $sizeBytes;

  /**
   * Output only. The total number of documents contained in the backup.
   *
   * @param string $documentCount
   */
  public function setDocumentCount($documentCount)
  {
    $this->documentCount = $documentCount;
  }
  /**
   * @return string
   */
  public function getDocumentCount()
  {
    return $this->documentCount;
  }
  /**
   * Output only. The total number of index entries contained in the backup.
   *
   * @param string $indexCount
   */
  public function setIndexCount($indexCount)
  {
    $this->indexCount = $indexCount;
  }
  /**
   * @return string
   */
  public function getIndexCount()
  {
    return $this->indexCount;
  }
  /**
   * Output only. Summation of the size of all documents and index entries in
   * the backup, measured in bytes.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1Stats::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1Stats');
