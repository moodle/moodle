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

namespace Google\Service\CloudSupport;

class ListAttachmentsResponse extends \Google\Collection
{
  protected $collection_key = 'attachments';
  protected $attachmentsType = Attachment::class;
  protected $attachmentsDataType = 'array';
  /**
   * A token to retrieve the next page of results. Set this in the `page_token`
   * field of subsequent `cases.attachments.list` requests. If unspecified,
   * there are no more results to retrieve.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of attachments associated with a case.
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
   * A token to retrieve the next page of results. Set this in the `page_token`
   * field of subsequent `cases.attachments.list` requests. If unspecified,
   * there are no more results to retrieve.
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
class_alias(ListAttachmentsResponse::class, 'Google_Service_CloudSupport_ListAttachmentsResponse');
