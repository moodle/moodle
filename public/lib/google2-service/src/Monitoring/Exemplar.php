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

namespace Google\Service\Monitoring;

class Exemplar extends \Google\Collection
{
  protected $collection_key = 'attachments';
  /**
   * Contextual information about the example value. Examples are:Trace:
   * type.googleapis.com/google.monitoring.v3.SpanContextLiteral string:
   * type.googleapis.com/google.protobuf.StringValueLabels dropped during
   * aggregation: type.googleapis.com/google.monitoring.v3.DroppedLabelsThere
   * may be only a single attachment of any given message type in a single
   * exemplar, and this is enforced by the system.
   *
   * @var array[]
   */
  public $attachments;
  /**
   * The observation (sampling) time of the above value.
   *
   * @var string
   */
  public $timestamp;
  /**
   * Value of the exemplar point. This value determines to which bucket the
   * exemplar belongs.
   *
   * @var 
   */
  public $value;

  /**
   * Contextual information about the example value. Examples are:Trace:
   * type.googleapis.com/google.monitoring.v3.SpanContextLiteral string:
   * type.googleapis.com/google.protobuf.StringValueLabels dropped during
   * aggregation: type.googleapis.com/google.monitoring.v3.DroppedLabelsThere
   * may be only a single attachment of any given message type in a single
   * exemplar, and this is enforced by the system.
   *
   * @param array[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return array[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
  /**
   * The observation (sampling) time of the above value.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Exemplar::class, 'Google_Service_Monitoring_Exemplar');
