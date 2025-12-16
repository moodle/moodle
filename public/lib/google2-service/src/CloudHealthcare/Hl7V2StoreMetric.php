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

namespace Google\Service\CloudHealthcare;

class Hl7V2StoreMetric extends \Google\Model
{
  /**
   * The total count of HL7v2 messages in the store for the given message type.
   *
   * @var string
   */
  public $count;
  /**
   * The Hl7v2 message type this metric applies to, such as `ADT` or `ORU`.
   *
   * @var string
   */
  public $messageType;
  /**
   * The total amount of structured storage used by HL7v2 messages of this
   * message type in the store.
   *
   * @var string
   */
  public $structuredStorageSizeBytes;

  /**
   * The total count of HL7v2 messages in the store for the given message type.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The Hl7v2 message type this metric applies to, such as `ADT` or `ORU`.
   *
   * @param string $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return string
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
  /**
   * The total amount of structured storage used by HL7v2 messages of this
   * message type in the store.
   *
   * @param string $structuredStorageSizeBytes
   */
  public function setStructuredStorageSizeBytes($structuredStorageSizeBytes)
  {
    $this->structuredStorageSizeBytes = $structuredStorageSizeBytes;
  }
  /**
   * @return string
   */
  public function getStructuredStorageSizeBytes()
  {
    return $this->structuredStorageSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Hl7V2StoreMetric::class, 'Google_Service_CloudHealthcare_Hl7V2StoreMetric');
