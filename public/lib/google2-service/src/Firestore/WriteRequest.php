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

class WriteRequest extends \Google\Collection
{
  protected $collection_key = 'writes';
  /**
   * Labels associated with this write request.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The ID of the write stream to resume. This may only be set in the first
   * message. When left empty, a new write stream will be created.
   *
   * @var string
   */
  public $streamId;
  /**
   * A stream token that was previously sent by the server. The client should
   * set this field to the token from the most recent WriteResponse it has
   * received. This acknowledges that the client has received responses up to
   * this token. After sending this token, earlier tokens may not be used
   * anymore. The server may close the stream if there are too many
   * unacknowledged responses. Leave this field unset when creating a new
   * stream. To resume a stream at a specific point, set this field and the
   * `stream_id` field. Leave this field unset when creating a new stream.
   *
   * @var string
   */
  public $streamToken;
  protected $writesType = Write::class;
  protected $writesDataType = 'array';

  /**
   * Labels associated with this write request.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The ID of the write stream to resume. This may only be set in the first
   * message. When left empty, a new write stream will be created.
   *
   * @param string $streamId
   */
  public function setStreamId($streamId)
  {
    $this->streamId = $streamId;
  }
  /**
   * @return string
   */
  public function getStreamId()
  {
    return $this->streamId;
  }
  /**
   * A stream token that was previously sent by the server. The client should
   * set this field to the token from the most recent WriteResponse it has
   * received. This acknowledges that the client has received responses up to
   * this token. After sending this token, earlier tokens may not be used
   * anymore. The server may close the stream if there are too many
   * unacknowledged responses. Leave this field unset when creating a new
   * stream. To resume a stream at a specific point, set this field and the
   * `stream_id` field. Leave this field unset when creating a new stream.
   *
   * @param string $streamToken
   */
  public function setStreamToken($streamToken)
  {
    $this->streamToken = $streamToken;
  }
  /**
   * @return string
   */
  public function getStreamToken()
  {
    return $this->streamToken;
  }
  /**
   * The writes to apply. Always executed atomically and in order. This must be
   * empty on the first request. This may be empty on the last request. This
   * must not be empty on all other requests.
   *
   * @param Write[] $writes
   */
  public function setWrites($writes)
  {
    $this->writes = $writes;
  }
  /**
   * @return Write[]
   */
  public function getWrites()
  {
    return $this->writes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteRequest::class, 'Google_Service_Firestore_WriteRequest');
