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

namespace Google\Service\ArtifactRegistry;

class ListAttachmentsResponse extends \Google\Collection
{
  protected $collection_key = 'attachments';
  protected $attachmentsType = Attachment::class;
  protected $attachmentsDataType = 'array';
  /**
   * The token to retrieve the next page of attachments, or empty if there are
   * no more attachments to return.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The attachments returned.
   *
   * @param Attachment[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return Attachment[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
  /**
   * The token to retrieve the next page of attachments, or empty if there are
   * no more attachments to return.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAttachmentsResponse::class, 'Google_Service_ArtifactRegistry_ListAttachmentsResponse');
