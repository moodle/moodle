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

class UpdateTopicRequest extends \Google\Model
{
  protected $topicType = Topic::class;
  protected $topicDataType = '';
  /**
   * Required. Indicates which fields in the provided topic to update. Must be
   * specified and non-empty. Note that if `update_mask` contains
   * "message_storage_policy" but the `message_storage_policy` is not set in the
   * `topic` provided above, then the updated value is determined by the policy
   * configured at the project or organization level.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The updated topic object.
   *
   * @param Topic $topic
   */
  public function setTopic(Topic $topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return Topic
   */
  public function getTopic()
  {
    return $this->topic;
  }
  /**
   * Required. Indicates which fields in the provided topic to update. Must be
   * specified and non-empty. Note that if `update_mask` contains
   * "message_storage_policy" but the `message_storage_policy` is not set in the
   * `topic` provided above, then the updated value is determined by the policy
   * configured at the project or organization level.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateTopicRequest::class, 'Google_Service_Pubsub_UpdateTopicRequest');
