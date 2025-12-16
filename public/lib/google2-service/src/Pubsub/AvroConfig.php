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

class AvroConfig extends \Google\Model
{
  /**
   * Optional. When true, the output Cloud Storage file will be serialized using
   * the topic schema, if it exists.
   *
   * @var bool
   */
  public $useTopicSchema;
  /**
   * Optional. When true, write the subscription name, message_id, publish_time,
   * attributes, and ordering_key as additional fields in the output. The
   * subscription name, message_id, and publish_time fields are put in their own
   * fields while all other message properties other than data (for example, an
   * ordering_key, if present) are added as entries in the attributes map.
   *
   * @var bool
   */
  public $writeMetadata;

  /**
   * Optional. When true, the output Cloud Storage file will be serialized using
   * the topic schema, if it exists.
   *
   * @param bool $useTopicSchema
   */
  public function setUseTopicSchema($useTopicSchema)
  {
    $this->useTopicSchema = $useTopicSchema;
  }
  /**
   * @return bool
   */
  public function getUseTopicSchema()
  {
    return $this->useTopicSchema;
  }
  /**
   * Optional. When true, write the subscription name, message_id, publish_time,
   * attributes, and ordering_key as additional fields in the output. The
   * subscription name, message_id, and publish_time fields are put in their own
   * fields while all other message properties other than data (for example, an
   * ordering_key, if present) are added as entries in the attributes map.
   *
   * @param bool $writeMetadata
   */
  public function setWriteMetadata($writeMetadata)
  {
    $this->writeMetadata = $writeMetadata;
  }
  /**
   * @return bool
   */
  public function getWriteMetadata()
  {
    return $this->writeMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AvroConfig::class, 'Google_Service_Pubsub_AvroConfig');
