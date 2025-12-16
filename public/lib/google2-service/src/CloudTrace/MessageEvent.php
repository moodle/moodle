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

namespace Google\Service\CloudTrace;

class MessageEvent extends \Google\Model
{
  /**
   * Unknown event type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Indicates a sent message.
   */
  public const TYPE_SENT = 'SENT';
  /**
   * Indicates a received message.
   */
  public const TYPE_RECEIVED = 'RECEIVED';
  /**
   * The number of compressed bytes sent or received. If missing, the compressed
   * size is assumed to be the same size as the uncompressed size.
   *
   * @var string
   */
  public $compressedSizeBytes;
  /**
   * An identifier for the MessageEvent's message that can be used to match
   * `SENT` and `RECEIVED` MessageEvents.
   *
   * @var string
   */
  public $id;
  /**
   * Type of MessageEvent. Indicates whether the message was sent or received.
   *
   * @var string
   */
  public $type;
  /**
   * The number of uncompressed bytes sent or received.
   *
   * @var string
   */
  public $uncompressedSizeBytes;

  /**
   * The number of compressed bytes sent or received. If missing, the compressed
   * size is assumed to be the same size as the uncompressed size.
   *
   * @param string $compressedSizeBytes
   */
  public function setCompressedSizeBytes($compressedSizeBytes)
  {
    $this->compressedSizeBytes = $compressedSizeBytes;
  }
  /**
   * @return string
   */
  public function getCompressedSizeBytes()
  {
    return $this->compressedSizeBytes;
  }
  /**
   * An identifier for the MessageEvent's message that can be used to match
   * `SENT` and `RECEIVED` MessageEvents.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Type of MessageEvent. Indicates whether the message was sent or received.
   *
   * Accepted values: TYPE_UNSPECIFIED, SENT, RECEIVED
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
  /**
   * The number of uncompressed bytes sent or received.
   *
   * @param string $uncompressedSizeBytes
   */
  public function setUncompressedSizeBytes($uncompressedSizeBytes)
  {
    $this->uncompressedSizeBytes = $uncompressedSizeBytes;
  }
  /**
   * @return string
   */
  public function getUncompressedSizeBytes()
  {
    return $this->uncompressedSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MessageEvent::class, 'Google_Service_CloudTrace_MessageEvent');
