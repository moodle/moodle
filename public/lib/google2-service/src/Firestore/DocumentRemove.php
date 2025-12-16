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

class DocumentRemove extends \Google\Collection
{
  protected $collection_key = 'removedTargetIds';
  /**
   * The resource name of the Document that has gone out of view.
   *
   * @var string
   */
  public $document;
  /**
   * The read timestamp at which the remove was observed. Greater or equal to
   * the `commit_time` of the change/delete/remove.
   *
   * @var string
   */
  public $readTime;
  /**
   * A set of target IDs for targets that previously matched this document.
   *
   * @var int[]
   */
  public $removedTargetIds;

  /**
   * The resource name of the Document that has gone out of view.
   *
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * The read timestamp at which the remove was observed. Greater or equal to
   * the `commit_time` of the change/delete/remove.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * A set of target IDs for targets that previously matched this document.
   *
   * @param int[] $removedTargetIds
   */
  public function setRemovedTargetIds($removedTargetIds)
  {
    $this->removedTargetIds = $removedTargetIds;
  }
  /**
   * @return int[]
   */
  public function getRemovedTargetIds()
  {
    return $this->removedTargetIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentRemove::class, 'Google_Service_Firestore_DocumentRemove');
