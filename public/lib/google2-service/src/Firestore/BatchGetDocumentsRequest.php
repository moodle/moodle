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

class BatchGetDocumentsRequest extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * The names of the documents to retrieve. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   * The request will fail if any of the document is not a child resource of the
   * given `database`. Duplicate names will be elided.
   *
   * @var string[]
   */
  public $documents;
  protected $maskType = DocumentMask::class;
  protected $maskDataType = '';
  protected $newTransactionType = TransactionOptions::class;
  protected $newTransactionDataType = '';
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @var string
   */
  public $readTime;
  /**
   * Reads documents in a transaction.
   *
   * @var string
   */
  public $transaction;

  /**
   * The names of the documents to retrieve. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   * The request will fail if any of the document is not a child resource of the
   * given `database`. Duplicate names will be elided.
   *
   * @param string[] $documents
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return string[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * The fields to return. If not set, returns all fields. If a document has a
   * field that is not present in this mask, that field will not be returned in
   * the response.
   *
   * @param DocumentMask $mask
   */
  public function setMask(DocumentMask $mask)
  {
    $this->mask = $mask;
  }
  /**
   * @return DocumentMask
   */
  public function getMask()
  {
    return $this->mask;
  }
  /**
   * Starts a new transaction and reads the documents. Defaults to a read-only
   * transaction. The new transaction ID will be returned as the first response
   * in the stream.
   *
   * @param TransactionOptions $newTransaction
   */
  public function setNewTransaction(TransactionOptions $newTransaction)
  {
    $this->newTransaction = $newTransaction;
  }
  /**
   * @return TransactionOptions
   */
  public function getNewTransaction()
  {
    return $this->newTransaction;
  }
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
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
   * Reads documents in a transaction.
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
class_alias(BatchGetDocumentsRequest::class, 'Google_Service_Firestore_BatchGetDocumentsRequest');
