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

class ExistenceFilter extends \Google\Model
{
  /**
   * The total count of documents that match target_id. If different from the
   * count of documents in the client that match, the client must manually
   * determine which documents no longer match the target. The client can use
   * the `unchanged_names` bloom filter to assist with this determination by
   * testing ALL the document names against the filter; if the document name is
   * NOT in the filter, it means the document no longer matches the target.
   *
   * @var int
   */
  public $count;
  /**
   * The target ID to which this filter applies.
   *
   * @var int
   */
  public $targetId;
  protected $unchangedNamesType = BloomFilter::class;
  protected $unchangedNamesDataType = '';

  /**
   * The total count of documents that match target_id. If different from the
   * count of documents in the client that match, the client must manually
   * determine which documents no longer match the target. The client can use
   * the `unchanged_names` bloom filter to assist with this determination by
   * testing ALL the document names against the filter; if the document name is
   * NOT in the filter, it means the document no longer matches the target.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The target ID to which this filter applies.
   *
   * @param int $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return int
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * A bloom filter that, despite its name, contains the UTF-8 byte encodings of
   * the resource names of ALL the documents that match target_id, in the form
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   * This bloom filter may be omitted at the server's discretion, such as if it
   * is deemed that the client will not make use of it or if it is too
   * computationally expensive to calculate or transmit. Clients must gracefully
   * handle this field being absent by falling back to the logic used before
   * this field existed; that is, re-add the target without a resume token to
   * figure out which documents in the client's cache are out of sync.
   *
   * @param BloomFilter $unchangedNames
   */
  public function setUnchangedNames(BloomFilter $unchangedNames)
  {
    $this->unchangedNames = $unchangedNames;
  }
  /**
   * @return BloomFilter
   */
  public function getUnchangedNames()
  {
    return $this->unchangedNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExistenceFilter::class, 'Google_Service_Firestore_ExistenceFilter');
