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

namespace Google\Service\Logging;

class RequestLog extends \Google\Collection
{
  protected $collection_key = 'sourceReference';
  /**
   * App Engine release version.
   *
   * @var string
   */
  public $appEngineRelease;
  /**
   * Application that handled this request.
   *
   * @var string
   */
  public $appId;
  /**
   * An indication of the relative cost of serving this request.
   *
   * @var 
   */
  public $cost;
  /**
   * Time when the request finished.
   *
   * @var string
   */
  public $endTime;
  /**
   * Whether this request is finished or active.
   *
   * @var bool
   */
  public $finished;
  /**
   * Whether this is the first RequestLog entry for this request. If an active
   * request has several RequestLog entries written to Stackdriver Logging, then
   * this field will be set for one of them.
   *
   * @var bool
   */
  public $first;
  /**
   * Internet host and port number of the resource being requested.
   *
   * @var string
   */
  public $host;
  /**
   * HTTP version of request. Example: "HTTP/1.1".
   *
   * @var string
   */
  public $httpVersion;
  /**
   * An identifier for the instance that handled the request.
   *
   * @var string
   */
  public $instanceId;
  /**
   * If the instance processing this request belongs to a manually scaled
   * module, then this is the 0-based index of the instance. Otherwise, this
   * value is -1.
   *
   * @var int
   */
  public $instanceIndex;
  /**
   * Origin IP address.
   *
   * @var string
   */
  public $ip;
  /**
   * Latency of the request.
   *
   * @var string
   */
  public $latency;
  protected $lineType = LogLine::class;
  protected $lineDataType = 'array';
  /**
   * Number of CPU megacycles used to process request.
   *
   * @var string
   */
  public $megaCycles;
  /**
   * Request method. Example: "GET", "HEAD", "PUT", "POST", "DELETE".
   *
   * @var string
   */
  public $method;
  /**
   * Module of the application that handled this request.
   *
   * @var string
   */
  public $moduleId;
  /**
   * The logged-in user who made the request.Most likely, this is the part of
   * the user's email before the @ sign. The field value is the same for
   * different requests from the same user, but different users can have similar
   * names. This information is also available to the application via the App
   * Engine Users API.This field will be populated starting with App Engine
   * 1.9.21.
   *
   * @var string
   */
  public $nickname;
  /**
   * Time this request spent in the pending request queue.
   *
   * @var string
   */
  public $pendingTime;
  /**
   * Referrer URL of request.
   *
   * @var string
   */
  public $referrer;
  /**
   * Globally unique identifier for a request, which is based on the request
   * start time. Request IDs for requests which started later will compare
   * greater as strings than those for requests which started earlier.
   *
   * @var string
   */
  public $requestId;
  /**
   * Contains the path and query portion of the URL that was requested. For
   * example, if the URL was "http://example.com/app?name=val", the resource
   * would be "/app?name=val". The fragment identifier, which is identified by
   * the # character, is not included.
   *
   * @var string
   */
  public $resource;
  /**
   * Size in bytes sent back to client by request.
   *
   * @var string
   */
  public $responseSize;
  protected $sourceReferenceType = SourceReference::class;
  protected $sourceReferenceDataType = 'array';
  /**
   * Stackdriver Trace span identifier for this request.
   *
   * @var string
   */
  public $spanId;
  /**
   * Time when the request started.
   *
   * @var string
   */
  public $startTime;
  /**
   * HTTP response status code. Example: 200, 404.
   *
   * @var int
   */
  public $status;
  /**
   * Task name of the request, in the case of an offline request.
   *
   * @var string
   */
  public $taskName;
  /**
   * Queue name of the request, in the case of an offline request.
   *
   * @var string
   */
  public $taskQueueName;
  /**
   * Stackdriver Trace identifier for this request.
   *
   * @var string
   */
  public $traceId;
  /**
   * If true, the value in the 'trace_id' field was sampled for storage in a
   * trace backend.
   *
   * @var bool
   */
  public $traceSampled;
  /**
   * File or class that handled the request.
   *
   * @var string
   */
  public $urlMapEntry;
  /**
   * User agent that made the request.
   *
   * @var string
   */
  public $userAgent;
  /**
   * Version of the application that handled this request.
   *
   * @var string
   */
  public $versionId;
  /**
   * Whether this was a loading request for the instance.
   *
   * @var bool
   */
  public $wasLoadingRequest;

  /**
   * App Engine release version.
   *
   * @param string $appEngineRelease
   */
  public function setAppEngineRelease($appEngineRelease)
  {
    $this->appEngineRelease = $appEngineRelease;
  }
  /**
   * @return string
   */
  public function getAppEngineRelease()
  {
    return $this->appEngineRelease;
  }
  /**
   * Application that handled this request.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  public function setCost($cost)
  {
    $this->cost = $cost;
  }
  public function getCost()
  {
    return $this->cost;
  }
  /**
   * Time when the request finished.
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
   * Whether this request is finished or active.
   *
   * @param bool $finished
   */
  public function setFinished($finished)
  {
    $this->finished = $finished;
  }
  /**
   * @return bool
   */
  public function getFinished()
  {
    return $this->finished;
  }
  /**
   * Whether this is the first RequestLog entry for this request. If an active
   * request has several RequestLog entries written to Stackdriver Logging, then
   * this field will be set for one of them.
   *
   * @param bool $first
   */
  public function setFirst($first)
  {
    $this->first = $first;
  }
  /**
   * @return bool
   */
  public function getFirst()
  {
    return $this->first;
  }
  /**
   * Internet host and port number of the resource being requested.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * HTTP version of request. Example: "HTTP/1.1".
   *
   * @param string $httpVersion
   */
  public function setHttpVersion($httpVersion)
  {
    $this->httpVersion = $httpVersion;
  }
  /**
   * @return string
   */
  public function getHttpVersion()
  {
    return $this->httpVersion;
  }
  /**
   * An identifier for the instance that handled the request.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * If the instance processing this request belongs to a manually scaled
   * module, then this is the 0-based index of the instance. Otherwise, this
   * value is -1.
   *
   * @param int $instanceIndex
   */
  public function setInstanceIndex($instanceIndex)
  {
    $this->instanceIndex = $instanceIndex;
  }
  /**
   * @return int
   */
  public function getInstanceIndex()
  {
    return $this->instanceIndex;
  }
  /**
   * Origin IP address.
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }
  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }
  /**
   * Latency of the request.
   *
   * @param string $latency
   */
  public function setLatency($latency)
  {
    $this->latency = $latency;
  }
  /**
   * @return string
   */
  public function getLatency()
  {
    return $this->latency;
  }
  /**
   * A list of log lines emitted by the application while serving this request.
   *
   * @param LogLine[] $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }
  /**
   * @return LogLine[]
   */
  public function getLine()
  {
    return $this->line;
  }
  /**
   * Number of CPU megacycles used to process request.
   *
   * @param string $megaCycles
   */
  public function setMegaCycles($megaCycles)
  {
    $this->megaCycles = $megaCycles;
  }
  /**
   * @return string
   */
  public function getMegaCycles()
  {
    return $this->megaCycles;
  }
  /**
   * Request method. Example: "GET", "HEAD", "PUT", "POST", "DELETE".
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Module of the application that handled this request.
   *
   * @param string $moduleId
   */
  public function setModuleId($moduleId)
  {
    $this->moduleId = $moduleId;
  }
  /**
   * @return string
   */
  public function getModuleId()
  {
    return $this->moduleId;
  }
  /**
   * The logged-in user who made the request.Most likely, this is the part of
   * the user's email before the @ sign. The field value is the same for
   * different requests from the same user, but different users can have similar
   * names. This information is also available to the application via the App
   * Engine Users API.This field will be populated starting with App Engine
   * 1.9.21.
   *
   * @param string $nickname
   */
  public function setNickname($nickname)
  {
    $this->nickname = $nickname;
  }
  /**
   * @return string
   */
  public function getNickname()
  {
    return $this->nickname;
  }
  /**
   * Time this request spent in the pending request queue.
   *
   * @param string $pendingTime
   */
  public function setPendingTime($pendingTime)
  {
    $this->pendingTime = $pendingTime;
  }
  /**
   * @return string
   */
  public function getPendingTime()
  {
    return $this->pendingTime;
  }
  /**
   * Referrer URL of request.
   *
   * @param string $referrer
   */
  public function setReferrer($referrer)
  {
    $this->referrer = $referrer;
  }
  /**
   * @return string
   */
  public function getReferrer()
  {
    return $this->referrer;
  }
  /**
   * Globally unique identifier for a request, which is based on the request
   * start time. Request IDs for requests which started later will compare
   * greater as strings than those for requests which started earlier.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Contains the path and query portion of the URL that was requested. For
   * example, if the URL was "http://example.com/app?name=val", the resource
   * would be "/app?name=val". The fragment identifier, which is identified by
   * the # character, is not included.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Size in bytes sent back to client by request.
   *
   * @param string $responseSize
   */
  public function setResponseSize($responseSize)
  {
    $this->responseSize = $responseSize;
  }
  /**
   * @return string
   */
  public function getResponseSize()
  {
    return $this->responseSize;
  }
  /**
   * Source code for the application that handled this request. There can be
   * more than one source reference per deployed application if source code is
   * distributed among multiple repositories.
   *
   * @param SourceReference[] $sourceReference
   */
  public function setSourceReference($sourceReference)
  {
    $this->sourceReference = $sourceReference;
  }
  /**
   * @return SourceReference[]
   */
  public function getSourceReference()
  {
    return $this->sourceReference;
  }
  /**
   * Stackdriver Trace span identifier for this request.
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
   * Time when the request started.
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
   * HTTP response status code. Example: 200, 404.
   *
   * @param int $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Task name of the request, in the case of an offline request.
   *
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * Queue name of the request, in the case of an offline request.
   *
   * @param string $taskQueueName
   */
  public function setTaskQueueName($taskQueueName)
  {
    $this->taskQueueName = $taskQueueName;
  }
  /**
   * @return string
   */
  public function getTaskQueueName()
  {
    return $this->taskQueueName;
  }
  /**
   * Stackdriver Trace identifier for this request.
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
   * If true, the value in the 'trace_id' field was sampled for storage in a
   * trace backend.
   *
   * @param bool $traceSampled
   */
  public function setTraceSampled($traceSampled)
  {
    $this->traceSampled = $traceSampled;
  }
  /**
   * @return bool
   */
  public function getTraceSampled()
  {
    return $this->traceSampled;
  }
  /**
   * File or class that handled the request.
   *
   * @param string $urlMapEntry
   */
  public function setUrlMapEntry($urlMapEntry)
  {
    $this->urlMapEntry = $urlMapEntry;
  }
  /**
   * @return string
   */
  public function getUrlMapEntry()
  {
    return $this->urlMapEntry;
  }
  /**
   * User agent that made the request.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * Version of the application that handled this request.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
  /**
   * Whether this was a loading request for the instance.
   *
   * @param bool $wasLoadingRequest
   */
  public function setWasLoadingRequest($wasLoadingRequest)
  {
    $this->wasLoadingRequest = $wasLoadingRequest;
  }
  /**
   * @return bool
   */
  public function getWasLoadingRequest()
  {
    return $this->wasLoadingRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestLog::class, 'Google_Service_Logging_RequestLog');
