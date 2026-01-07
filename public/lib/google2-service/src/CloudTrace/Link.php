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

class Link extends \Google\Model
{
  /**
   * The relationship of the two spans is unknown.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The linked span is a child of the current span.
   */
  public const TYPE_CHILD_LINKED_SPAN = 'CHILD_LINKED_SPAN';
  /**
   * The linked span is a parent of the current span.
   */
  public const TYPE_PARENT_LINKED_SPAN = 'PARENT_LINKED_SPAN';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * The `[SPAN_ID]` for a span within a trace.
   *
   * @var string
   */
  public $spanId;
  /**
   * The `[TRACE_ID]` for a trace within a project.
   *
   * @var string
   */
  public $traceId;
  /**
   * The relationship of the current span relative to the linked span.
   *
   * @var string
   */
  public $type;

  /**
   * A set of attributes on the link. Up to 32 attributes can be specified per
   * link.
   *
   * @param Attributes $attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The `[SPAN_ID]` for a span within a trace.
   *
   * @param string $spanId
   */
  public function setSpanId($spanId)
  {
    $this->spanId = $spanId;
  }
  /**
   * @return string
   */
  public function getSpanId()
  {
    return $this->spanId;
  }
  /**
   * The `[TRACE_ID]` for a trace within a project.
   *
   * @param string $traceId
   */
  public function setTraceId($traceId)
  {
    $this->traceId = $traceId;
  }
  /**
   * @return string
   */
  public function getTraceId()
  {
    return $this->traceId;
  }
  /**
   * The relationship of the current span relative to the linked span.
   *
   * Accepted values: TYPE_UNSPECIFIED, CHILD_LINKED_SPAN, PARENT_LINKED_SPAN
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Link::class, 'Google_Service_CloudTrace_Link');
