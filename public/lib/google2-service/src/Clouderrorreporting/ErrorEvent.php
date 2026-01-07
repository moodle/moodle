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

namespace Google\Service\Clouderrorreporting;

class ErrorEvent extends \Google\Model
{
  protected $contextType = ErrorContext::class;
  protected $contextDataType = '';
  /**
   * Time when the event occurred as provided in the error report. If the report
   * did not contain a timestamp, the time the error was received by the Error
   * Reporting system is used.
   *
   * @var string
   */
  public $eventTime;
  /**
   * The stack trace that was reported or logged by the service.
   *
   * @var string
   */
  public $message;
  protected $serviceContextType = ServiceContext::class;
  protected $serviceContextDataType = '';

  /**
   * Data about the context in which the error occurred.
   *
   * @param ErrorContext $context
   */
  public function setContext(ErrorContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return ErrorContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Time when the event occurred as provided in the error report. If the report
   * did not contain a timestamp, the time the error was received by the Error
   * Reporting system is used.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The stack trace that was reported or logged by the service.
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
   * The `ServiceContext` for which this error was reported.
   *
   * @param ServiceContext $serviceContext
   */
  public function setServiceContext(ServiceContext $serviceContext)
  {
    $this->serviceContext = $serviceContext;
  }
  /**
   * @return ServiceContext
   */
  public function getServiceContext()
  {
    return $this->serviceContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorEvent::class, 'Google_Service_Clouderrorreporting_ErrorEvent');
