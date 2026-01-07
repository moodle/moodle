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

namespace Google\Service\Pubsub;

class PullRequest extends \Google\Model
{
  /**
   * Required. The maximum number of messages to return for this request. Must
   * be a positive integer. The Pub/Sub system may return fewer than the number
   * specified.
   *
   * @var int
   */
  public $maxMessages;
  /**
   * Optional. If this field set to true, the system will respond immediately
   * even if it there are no messages available to return in the `Pull`
   * response. Otherwise, the system may wait (for a bounded amount of time)
   * until at least one message is available, rather than returning no messages.
   * Warning: setting this field to `true` is discouraged because it adversely
   * impacts the performance of `Pull` operations. We recommend that users do
   * not set this field.
   *
   * @deprecated
   * @var bool
   */
  public $returnImmediately;

  /**
   * Required. The maximum number of messages to return for this request. Must
   * be a positive integer. The Pub/Sub system may return fewer than the number
   * specified.
   *
   * @param int $maxMessages
   */
  public function setMaxMessages($maxMessages)
  {
    $this->maxMessages = $maxMessages;
  }
  /**
   * @return int
   */
  public function getMaxMessages()
  {
    return $this->maxMessages;
  }
  /**
   * Optional. If this field set to true, the system will respond immediately
   * even if it there are no messages available to return in the `Pull`
   * response. Otherwise, the system may wait (for a bounded amount of time)
   * until at least one message is available, rather than returning no messages.
   * Warning: setting this field to `true` is discouraged because it adversely
   * impacts the performance of `Pull` operations. We recommend that users do
   * not set this field.
   *
   * @deprecated
   * @param bool $returnImmediately
   */
  public function setReturnImmediately($returnImmediately)
  {
    $this->returnImmediately = $returnImmediately;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getReturnImmediately()
  {
    return $this->returnImmediately;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PullRequest::class, 'Google_Service_Pubsub_PullRequest');
