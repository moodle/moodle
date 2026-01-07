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

class ReportedErrorEvent extends \Google\Model
{
  protected $contextType = ErrorContext::class;
  protected $contextDataType = '';
  /**
   * Optional. Time when the event occurred. If not provided, the time when the
   * event was received by the Error Reporting system is used. If provided, the
   * time must not exceed the [logs retention
   * period](https://cloud.google.com/logging/quotas#logs_retention_periods) in
   * the past, or be more than 24 hours in the future. If an invalid time is
   * provided, then an error is returned.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. The error message. If no `context.reportLocation` is provided,
   * the message must contain a header (typically consisting of the exception
   * type name and an error message) and an exception stack trace in one of the
   * supported programming languages and formats. Supported languages are Java,
   * Python, JavaScript, Ruby, C#, PHP, and Go. Supported stack trace formats
   * are: * **Java**: Must be the return value of [`Throwable.printStackTrace()`
   * ](https://docs.oracle.com/javase/7/docs/api/java/lang/Throwable.html#printS
   * tackTrace%28%29). * **Python**: Must be the return value of [`traceback.for
   * mat_exc()`](https://docs.python.org/2/library/traceback.html#traceback.form
   * at_exc). * **JavaScript**: Must be the value of
   * [`error.stack`](https://github.com/v8/v8/wiki/Stack-Trace-API) as returned
   * by V8. * **Ruby**: Must contain frames returned by
   * [`Exception.backtrace`](https://ruby-
   * doc.org/core-2.2.0/Exception.html#method-i-backtrace). * **C#**: Must be
   * the return value of [`Exception.ToString()`](https://msdn.microsoft.com/en-
   * us/library/system.exception.tostring.aspx). * **PHP**: Must be prefixed
   * with `"PHP (Notice|Parse error|Fatal error|Warning): "` and contain the
   * result of
   * [`(string)$exception`](https://php.net/manual/en/exception.tostring.php). *
   * **Go**: Must be the return value of
   * [`debug.Stack()`](https://pkg.go.dev/runtime/debug#Stack).
   *
   * @var string
   */
  public $message;
  protected $serviceContextType = ServiceContext::class;
  protected $serviceContextDataType = '';

  /**
   * Optional. A description of the context in which the error occurred.
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
   * Optional. Time when the event occurred. If not provided, the time when the
   * event was received by the Error Reporting system is used. If provided, the
   * time must not exceed the [logs retention
   * period](https://cloud.google.com/logging/quotas#logs_retention_periods) in
   * the past, or be more than 24 hours in the future. If an invalid time is
   * provided, then an error is returned.
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
   * Required. The error message. If no `context.reportLocation` is provided,
   * the message must contain a header (typically consisting of the exception
   * type name and an error message) and an exception stack trace in one of the
   * supported programming languages and formats. Supported languages are Java,
   * Python, JavaScript, Ruby, C#, PHP, and Go. Supported stack trace formats
   * are: * **Java**: Must be the return value of [`Throwable.printStackTrace()`
   * ](https://docs.oracle.com/javase/7/docs/api/java/lang/Throwable.html#printS
   * tackTrace%28%29). * **Python**: Must be the return value of [`traceback.for
   * mat_exc()`](https://docs.python.org/2/library/traceback.html#traceback.form
   * at_exc). * **JavaScript**: Must be the value of
   * [`error.stack`](https://github.com/v8/v8/wiki/Stack-Trace-API) as returned
   * by V8. * **Ruby**: Must contain frames returned by
   * [`Exception.backtrace`](https://ruby-
   * doc.org/core-2.2.0/Exception.html#method-i-backtrace). * **C#**: Must be
   * the return value of [`Exception.ToString()`](https://msdn.microsoft.com/en-
   * us/library/system.exception.tostring.aspx). * **PHP**: Must be prefixed
   * with `"PHP (Notice|Parse error|Fatal error|Warning): "` and contain the
   * result of
   * [`(string)$exception`](https://php.net/manual/en/exception.tostring.php). *
   * **Go**: Must be the return value of
   * [`debug.Stack()`](https://pkg.go.dev/runtime/debug#Stack).
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
   * Required. The service context in which this error has occurred.
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
class_alias(ReportedErrorEvent::class, 'Google_Service_Clouderrorreporting_ReportedErrorEvent');
