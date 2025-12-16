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

namespace Google\Service\ManagedKafka;

class ConsumerPartitionMetadata extends \Google\Model
{
  /**
   * Optional. The associated metadata for this partition, or empty if it does
   * not exist.
   *
   * @var string
   */
  public $metadata;
  /**
   * Required. The current offset for this partition, or 0 if no offset has been
   * committed.
   *
   * @var string
   */
  public $offset;

  /**
   * Optional. The associated metadata for this partition, or empty if it does
   * not exist.
   *
   * @param string $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. The current offset for this partition, or 0 if no offset has been
   * committed.
   *
   * @param string $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return string
   */
  public function getOffset()
  {
    return $this->offset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsumerPartitionMetadata::class, 'Google_Service_ManagedKafka_ConsumerPartitionMetadata');
