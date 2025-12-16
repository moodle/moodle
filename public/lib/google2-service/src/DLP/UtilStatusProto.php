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

namespace Google\Service\DLP;

class UtilStatusProto extends \Google\Model
{
  /**
   * copybara:strip_begin(b/383363683) copybara:strip_end_and_replace optional
   * int32 canonical_code = 6;
   *
   * @var int
   */
  public $canonicalCode;
  /**
   * Numeric code drawn from the space specified below. Often, this is the
   * canonical error space, and code is drawn from google3/util/task/codes.proto
   * copybara:strip_begin(b/383363683) copybara:strip_end_and_replace optional
   * int32 code = 1;
   *
   * @var int
   */
  public $code;
  /**
   * Detail message copybara:strip_begin(b/383363683)
   * copybara:strip_end_and_replace optional string message = 3;
   *
   * @var string
   */
  public $message;
  protected $messageSetType = Proto2BridgeMessageSet::class;
  protected $messageSetDataType = '';
  /**
   * copybara:strip_begin(b/383363683) Space to which this status belongs
   * copybara:strip_end_and_replace optional string space = 2; // Space to which
   * this status belongs
   *
   * @var string
   */
  public $space;

  /**
   * copybara:strip_begin(b/383363683) copybara:strip_end_and_replace optional
   * int32 canonical_code = 6;
   *
   * @param int $canonicalCode
   */
  public function setCanonicalCode($canonicalCode)
  {
    $this->canonicalCode = $canonicalCode;
  }
  /**
   * @return int
   */
  public function getCanonicalCode()
  {
    return $this->canonicalCode;
  }
  /**
   * Numeric code drawn from the space specified below. Often, this is the
   * canonical error space, and code is drawn from google3/util/task/codes.proto
   * copybara:strip_begin(b/383363683) copybara:strip_end_and_replace optional
   * int32 code = 1;
   *
   * @param int $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Detail message copybara:strip_begin(b/383363683)
   * copybara:strip_end_and_replace optional string message = 3;
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * message_set associates an arbitrary proto message with the status.
   * copybara:strip_begin(b/383363683) copybara:strip_end_and_replace optional
   * proto2.bridge.MessageSet message_set = 5;
   *
   * @param Proto2BridgeMessageSet $messageSet
   */
  public function setMessageSet(Proto2BridgeMessageSet $messageSet)
  {
    $this->messageSet = $messageSet;
  }
  /**
   * @return Proto2BridgeMessageSet
   */
  public function getMessageSet()
  {
    return $this->messageSet;
  }
  /**
   * copybara:strip_begin(b/383363683) Space to which this status belongs
   * copybara:strip_end_and_replace optional string space = 2; // Space to which
   * this status belongs
   *
   * @param string $space
   */
  public function setSpace($space)
  {
    $this->space = $space;
  }
  /**
   * @return string
   */
  public function getSpace()
  {
    return $this->space;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UtilStatusProto::class, 'Google_Service_DLP_UtilStatusProto');
