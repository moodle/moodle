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

class BatchGetDocumentsResponse extends \Google\Model
{
  protected $foundType = Document::class;
  protected $foundDataType = '';
  /**
   * A document name that was requested but does not exist. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @var string
   */
  public $missing;
  /**
   * The time at which the document was read. This may be monotically
   * increasing, in this case the previous documents in the result stream are
   * guaranteed not to have changed between their read_time and this one.
   *
   * @var string
   */
  public $readTime;
  /**
   * The transaction that was started as part of this request. Will only be set
   * in the first response, and only if BatchGetDocumentsRequest.new_transaction
   * was set in the request.
   *
   * @var string
   */
  public $transaction;

  /**
   * A document that was requested.
   *
   * @param Document $found
   */
  public function setFound(Document $found)
  {
    $this->found = $found;
  }
  /**
   * @return Document
   */
  public function getFound()
  {
    return $this->found;
  }
  /**
   * A document name that was requested but does not exist. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @param string $missing
   */
  public function setMissing($missing)
  {
    $this->missing = $missing;
  }
  /**
   * @return string
   */
  public function getMissing()
  {
    return $this->missing;
  }
  /**
   * The time at which the document was read. This may be monotically
   * increasing, in this case the previous documents in the result stream are
   * guaranteed not to have changed between their read_time and this one.
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
   * The transaction that was started as part of this request. Will only be set
   * in the first response, and only if BatchGetDocumentsRequest.new_transaction
   * was set in the request.
   *
   * @param string $transaction
   */
  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return string
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchGetDocumentsResponse::class, 'Google_Service_Firestore_BatchGetDocumentsResponse');
