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

class Span extends \Google\Model
{
  /**
   * Unspecified. Do NOT use as default. Implementations MAY assume
   * SpanKind.INTERNAL to be default.
   */
  public const SPAN_KIND_SPAN_KIND_UNSPECIFIED = 'SPAN_KIND_UNSPECIFIED';
  /**
   * Indicates that the span is used internally. Default value.
   */
  public const SPAN_KIND_INTERNAL = 'INTERNAL';
  /**
   * Indicates that the span covers server-side handling of an RPC or other
   * remote network request.
   */
  public const SPAN_KIND_SERVER = 'SERVER';
  /**
   * Indicates that the span covers the client-side wrapper around an RPC or
   * other remote request.
   */
  public const SPAN_KIND_CLIENT = 'CLIENT';
  /**
   * Indicates that the span describes producer sending a message to a broker.
   * Unlike client and server, there is no direct critical path latency
   * relationship between producer and consumer spans (e.g. publishing a message
   * to a pubsub service).
   */
  public const SPAN_KIND_PRODUCER = 'PRODUCER';
  /**
   * Indicates that the span describes consumer receiving a message from a
   * broker. Unlike client and server, there is no direct critical path latency
   * relationship between producer and consumer spans (e.g. receiving a message
   * from a pubsub service subscription).
   */
  public const SPAN_KIND_CONSUMER = 'CONSUMER';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * Optional. The number of child spans that were generated while this span was
   * active. If set, allows implementation to detect missing child spans.
   *
   * @var int
   */
  public $childSpanCount;
  protected $displayNameType = TruncatableString::class;
  protected $displayNameDataType = '';
  /**
   * Required. The end time of the span. On the client side, this is the time
   * kept by the local machine where the span execution ends. On the server
   * side, this is the time when the server application handler stops running.
   *
   * @var string
   */
  public $endTime;
  protected $linksType = Links::class;
  protected $linksDataType = '';
  /**
   * Required. The resource name of the span in the following format: *
   * `projects/[PROJECT_ID]/traces/[TRACE_ID]/spans/[SPAN_ID]` `[TRACE_ID]` is a
   * unique identifier for a trace within a project; it is a 32-character
   * hexadecimal encoding of a 16-byte array. It should not be zero. `[SPAN_ID]`
   * is a unique identifier for a span within a trace; it is a 16-character
   * hexadecimal encoding of an 8-byte array. It should not be zero. .
   *
   * @var string
   */
  public $name;
  /**
   * The `[SPAN_ID]` of this span's parent span. If this is a root span, then
   * this field must be empty.
   *
   * @var string
   */
  public $parentSpanId;
  /**
   * Optional. Set this parameter to indicate whether this span is in the same
   * process as its parent. If you do not set this parameter, Trace is unable to
   * take advantage of this helpful information.
   *
   * @var bool
   */
  public $sameProcessAsParentSpan;
  /**
   * Required. The `[SPAN_ID]` portion of the span's resource name.
   *
   * @var string
   */
  public $spanId;
  /**
   * Optional. Distinguishes between spans generated in a particular context.
   * For example, two spans with the same name may be distinguished using
   * `CLIENT` (caller) and `SERVER` (callee) to identify an RPC call.
   *
   * @var string
   */
  public $spanKind;
  protected $stackTraceType = StackTrace::class;
  protected $stackTraceDataType = '';
  /**
   * Required. The start time of the span. On the client side, this is the time
   * kept by the local machine where the span execution starts. On the server
   * side, this is the time when the server's application handler starts
   * running.
   *
   * @var string
   */
  public $startTime;
  protected $statusType = Status::class;
  protected $statusDataType = '';
  protected $timeEventsType = TimeEvents::class;
  protected $timeEventsDataType = '';

  /**
   * A set of attributes on the span. You can have up to 32 attributes per span.
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
   * Optional. The number of child spans that were generated while this span was
   * active. If set, allows implementation to detect missing child spans.
   *
   * @param int $childSpanCount
   */
  public function setChildSpanCount($childSpanCount)
  {
    $this->childSpanCount = $childSpanCount;
  }
  /**
   * @return int
   */
  public function getChildSpanCount()
  {
    return $this->childSpanCount;
  }
  /**
   * Required. A description of the span's operation (up to 128 bytes). Cloud
   * Trace displays the description in the Cloud console. For example, the
   * display name can be a qualified method name or a file name and a line
   * number where the operation is called. A best practice is to use the same
   * display name within an application and at the same call point. This makes
   * it easier to correlate spans in different traces.
   *
   * @param TruncatableString $displayName
   */
  public function setDisplayName(TruncatableString $displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return TruncatableString
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The end time of the span. On the client side, this is the time
   * kept by the local machine where the span execution ends. On the server
   * side, this is the time when the server application handler stops running.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Links associated with the span. You can have up to 128 links per Span.
   *
   * @param Links $links
   */
  public function setLinks(Links $links)
  {
    $this->links = $links;
  }
  /**
   * @return Links
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * Required. The resource name of the span in the following format: *
   * `projects/[PROJECT_ID]/traces/[TRACE_ID]/spans/[SPAN_ID]` `[TRACE_ID]` is a
   * unique identifier for a trace within a project; it is a 32-character
   * hexadecimal encoding of a 16-byte array. It should not be zero. `[SPAN_ID]`
   * is a unique identifier for a span within a trace; it is a 16-character
   * hexadecimal encoding of an 8-byte array. It should not be zero. .
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The `[SPAN_ID]` of this span's parent span. If this is a root span, then
   * this field must be empty.
   *
   * @param string $parentSpanId
   */
  public function setParentSpanId($parentSpanId)
  {
    $this->parentSpanId = $parentSpanId;
  }
  /**
   * @return string
   */
  public function getParentSpanId()
  {
    return $this->parentSpanId;
  }
  /**
   * Optional. Set this parameter to indicate whether this span is in the same
   * process as its parent. If you do not set this parameter, Trace is unable to
   * take advantage of this helpful information.
   *
   * @param bool $sameProcessAsParentSpan
   */
  public function setSameProcessAsParentSpan($sameProcessAsParentSpan)
  {
    $this->sameProcessAsParentSpan = $sameProcessAsParentSpan;
  }
  /**
   * @return bool
   */
  public function getSameProcessAsParentSpan()
  {
    return $this->sameProcessAsParentSpan;
  }
  /**
   * Required. The `[SPAN_ID]` portion of the span's resource name.
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
   * Optional. Distinguishes between spans generated in a particular context.
   * For example, two spans with the same name may be distinguished using
   * `CLIENT` (caller) and `SERVER` (callee) to identify an RPC call.
   *
   * Accepted values: SPAN_KIND_UNSPECIFIED, INTERNAL, SERVER, CLIENT, PRODUCER,
   * CONSUMER
   *
   * @param self::SPAN_KIND_* $spanKind
   */
  public function setSpanKind($spanKind)
  {
    $this->spanKind = $spanKind;
  }
  /**
   * @return self::SPAN_KIND_*
   */
  public function getSpanKind()
  {
    return $this->spanKind;
  }
  /**
   * Stack trace captured at the start of the span.
   *
   * @param StackTrace $stackTrace
   */
  public function setStackTrace(StackTrace $stackTrace)
  {
    $this->stackTrace = $stackTrace;
  }
  /**
   * @return StackTrace
   */
  public function getStackTrace()
  {
    return $this->stackTrace;
  }
  /**
   * Required. The start time of the span. On the client side, this is the time
   * kept by the local machine where the span execution starts. On the server
   * side, this is the time when the server's application handler starts
   * running.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Optional. The final status for this span.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * A set of time events. You can have up to 32 annotations and 128 message
   * events per span.
   *
   * @param TimeEvents $timeEvents
   */
  public function setTimeEvents(TimeEvents $timeEvents)
  {
    $this->timeEvents = $timeEvents;
  }
  /**
   * @return TimeEvents
   */
  public function getTimeEvents()
  {
    return $this->timeEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Span::class, 'Google_Service_CloudTrace_Span');
