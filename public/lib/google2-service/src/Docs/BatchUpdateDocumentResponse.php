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

namespace Google\Service\Docs;

class BatchUpdateDocumentResponse extends \Google\Collection
{
  protected $collection_key = 'replies';
  /**
   * The ID of the document to which the updates were applied to.
   *
   * @var string
   */
  public $documentId;
  protected $repliesType = Response::class;
  protected $repliesDataType = 'array';
  protected $writeControlType = WriteControl::class;
  protected $writeControlDataType = '';

  /**
   * The ID of the document to which the updates were applied to.
   *
   * @param string $documentId
   */
  public function setDocumentId($documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @return string
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * The reply of the updates. This maps 1:1 with the updates, although replies
   * to some requests may be empty.
   *
   * @param Response[] $replies
   */
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return Response[]
   */
  public function getReplies()
  {
    return $this->replies;
  }
  /**
   * The updated write control after applying the request.
   *
   * @param WriteControl $writeControl
   */
  public function setWriteControl(WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  /**
   * @return WriteControl
   */
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateDocumentResponse::class, 'Google_Service_Docs_BatchUpdateDocumentResponse');
