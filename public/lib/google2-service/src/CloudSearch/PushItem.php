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

class PushItem extends \Google\Model
{
  /**
   * Default UNSPECIFIED. Specifies that the push operation should not modify
   * ItemStatus
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Indicates that the repository document has been modified or updated since
   * the previous update call. This changes status to MODIFIED state for an
   * existing item. If this is called on a non existing item, the status is
   * changed to NEW_ITEM.
   */
  public const TYPE_MODIFIED = 'MODIFIED';
  /**
   * Item in the repository has not been modified since the last update call.
   * This push operation will set status to ACCEPTED state.
   */
  public const TYPE_NOT_MODIFIED = 'NOT_MODIFIED';
  /**
   * Connector is facing a repository error regarding this item. Change status
   * to REPOSITORY_ERROR state. Item is unreserved and rescheduled at a future
   * time determined by exponential backoff.
   */
  public const TYPE_REPOSITORY_ERROR = 'REPOSITORY_ERROR';
  /**
   * Call push with REQUEUE only for items that have been reserved. This action
   * unreserves the item and resets its available time to the wall clock time.
   */
  public const TYPE_REQUEUE = 'REQUEUE';
  /**
   * Content hash of the item according to the repository. If specified, this is
   * used to determine how to modify this item's status. Setting this field and
   * the type field results in argument error. The maximum length is 2048
   * characters.
   *
   * @var string
   */
  public $contentHash;
  /**
   * The metadata hash of the item according to the repository. If specified,
   * this is used to determine how to modify this item's status. Setting this
   * field and the type field results in argument error. The maximum length is
   * 2048 characters.
   *
   * @var string
   */
  public $metadataHash;
  /**
   * Provides additional document state information for the connector, such as
   * an alternate repository ID and other metadata. The maximum length is 8192
   * bytes.
   *
   * @var string
   */
  public $payload;
  /**
   * Queue to which this item belongs. The `default` queue is chosen if this
   * field is not specified. The maximum length is 512 characters.
   *
   * @var string
   */
  public $queue;
  protected $repositoryErrorType = RepositoryError::class;
  protected $repositoryErrorDataType = '';
  /**
   * Structured data hash of the item according to the repository. If specified,
   * this is used to determine how to modify this item's status. Setting this
   * field and the type field results in argument error. The maximum length is
   * 2048 characters.
   *
   * @var string
   */
  public $structuredDataHash;
  /**
   * The type of the push operation that defines the push behavior.
   *
   * @var string
   */
  public $type;

  /**
   * Content hash of the item according to the repository. If specified, this is
   * used to determine how to modify this item's status. Setting this field and
   * the type field results in argument error. The maximum length is 2048
   * characters.
   *
   * @param string $contentHash
   */
  public function setContentHash($contentHash)
  {
    $this->contentHash = $contentHash;
  }
  /**
   * @return string
   */
  public function getContentHash()
  {
    return $this->contentHash;
  }
  /**
   * The metadata hash of the item according to the repository. If specified,
   * this is used to determine how to modify this item's status. Setting this
   * field and the type field results in argument error. The maximum length is
   * 2048 characters.
   *
   * @param string $metadataHash
   */
  public function setMetadataHash($metadataHash)
  {
    $this->metadataHash = $metadataHash;
  }
  /**
   * @return string
   */
  public function getMetadataHash()
  {
    return $this->metadataHash;
  }
  /**
   * Provides additional document state information for the connector, such as
   * an alternate repository ID and other metadata. The maximum length is 8192
   * bytes.
   *
   * @param string $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return string
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Queue to which this item belongs. The `default` queue is chosen if this
   * field is not specified. The maximum length is 512 characters.
   *
   * @param string $queue
   */
  public function setQueue($queue)
  {
    $this->queue = $queue;
  }
  /**
   * @return string
   */
  public function getQueue()
  {
    return $this->queue;
  }
  /**
   * Populate this field to store Connector or repository error details. This
   * information is displayed in the Admin Console. This field may only be
   * populated when the Type is REPOSITORY_ERROR.
   *
   * @param RepositoryError $repositoryError
   */
  public function setRepositoryError(RepositoryError $repositoryError)
  {
    $this->repositoryError = $repositoryError;
  }
  /**
   * @return RepositoryError
   */
  public function getRepositoryError()
  {
    return $this->repositoryError;
  }
  /**
   * Structured data hash of the item according to the repository. If specified,
   * this is used to determine how to modify this item's status. Setting this
   * field and the type field results in argument error. The maximum length is
   * 2048 characters.
   *
   * @param string $structuredDataHash
   */
  public function setStructuredDataHash($structuredDataHash)
  {
    $this->structuredDataHash = $structuredDataHash;
  }
  /**
   * @return string
   */
  public function getStructuredDataHash()
  {
    return $this->structuredDataHash;
  }
  /**
   * The type of the push operation that defines the push behavior.
   *
   * Accepted values: UNSPECIFIED, MODIFIED, NOT_MODIFIED, REPOSITORY_ERROR,
   * REQUEUE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PushItem::class, 'Google_Service_CloudSearch_PushItem');
