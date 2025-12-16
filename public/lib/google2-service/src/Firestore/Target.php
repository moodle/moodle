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

class Target extends \Google\Model
{
  protected $documentsType = DocumentsTarget::class;
  protected $documentsDataType = '';
  /**
   * The number of documents that last matched the query at the resume token or
   * read time. This value is only relevant when a `resume_type` is provided.
   * This value being present and greater than zero signals that the client
   * wants `ExistenceFilter.unchanged_names` to be included in the response.
   *
   * @var int
   */
  public $expectedCount;
  /**
   * If the target should be removed once it is current and consistent.
   *
   * @var bool
   */
  public $once;
  protected $queryType = QueryTarget::class;
  protected $queryDataType = '';
  /**
   * Start listening after a specific `read_time`. The client must know the
   * state of matching documents at this time.
   *
   * @var string
   */
  public $readTime;
  /**
   * A resume token from a prior TargetChange for an identical target. Using a
   * resume token with a different target is unsupported and may fail.
   *
   * @var string
   */
  public $resumeToken;
  /**
   * The target ID that identifies the target on the stream. Must be a positive
   * number and non-zero. If `target_id` is 0 (or unspecified), the server will
   * assign an ID for this target and return that in a `TargetChange::ADD`
   * event. Once a target with `target_id=0` is added, all subsequent targets
   * must also have `target_id=0`. If an `AddTarget` request with `target_id !=
   * 0` is sent to the server after a target with `target_id=0` is added, the
   * server will immediately send a response with a `TargetChange::Remove`
   * event. Note that if the client sends multiple `AddTarget` requests without
   * an ID, the order of IDs returned in `TargetChange.target_ids` are
   * undefined. Therefore, clients should provide a target ID instead of relying
   * on the server to assign one. If `target_id` is non-zero, there must not be
   * an existing active target on this stream with the same ID.
   *
   * @var int
   */
  public $targetId;

  /**
   * A target specified by a set of document names.
   *
   * @param DocumentsTarget $documents
   */
  public function setDocuments(DocumentsTarget $documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return DocumentsTarget
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * The number of documents that last matched the query at the resume token or
   * read time. This value is only relevant when a `resume_type` is provided.
   * This value being present and greater than zero signals that the client
   * wants `ExistenceFilter.unchanged_names` to be included in the response.
   *
   * @param int $expectedCount
   */
  public function setExpectedCount($expectedCount)
  {
    $this->expectedCount = $expectedCount;
  }
  /**
   * @return int
   */
  public function getExpectedCount()
  {
    return $this->expectedCount;
  }
  /**
   * If the target should be removed once it is current and consistent.
   *
   * @param bool $once
   */
  public function setOnce($once)
  {
    $this->once = $once;
  }
  /**
   * @return bool
   */
  public function getOnce()
  {
    return $this->once;
  }
  /**
   * A target specified by a query.
   *
   * @param QueryTarget $query
   */
  public function setQuery(QueryTarget $query)
  {
    $this->query = $query;
  }
  /**
   * @return QueryTarget
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Start listening after a specific `read_time`. The client must know the
   * state of matching documents at this time.
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
   * A resume token from a prior TargetChange for an identical target. Using a
   * resume token with a different target is unsupported and may fail.
   *
   * @param string $resumeToken
   */
  public function setResumeToken($resumeToken)
  {
    $this->resumeToken = $resumeToken;
  }
  /**
   * @return string
   */
  public function getResumeToken()
  {
    return $this->resumeToken;
  }
  /**
   * The target ID that identifies the target on the stream. Must be a positive
   * number and non-zero. If `target_id` is 0 (or unspecified), the server will
   * assign an ID for this target and return that in a `TargetChange::ADD`
   * event. Once a target with `target_id=0` is added, all subsequent targets
   * must also have `target_id=0`. If an `AddTarget` request with `target_id !=
   * 0` is sent to the server after a target with `target_id=0` is added, the
   * server will immediately send a response with a `TargetChange::Remove`
   * event. Note that if the client sends multiple `AddTarget` requests without
   * an ID, the order of IDs returned in `TargetChange.target_ids` are
   * undefined. Therefore, clients should provide a target ID instead of relying
   * on the server to assign one. If `target_id` is non-zero, there must not be
   * an existing active target on this stream with the same ID.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Target::class, 'Google_Service_Firestore_Target');
