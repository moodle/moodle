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

namespace Google\Service\Spanner;

class DiagnosticMessage extends \Google\Model
{
  /**
   * Required default value.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Lowest severity level "Info".
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Middle severity level "Warning".
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Severity level signaling an error "Error"
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Severity level signaling a non recoverable error "Fatal"
   */
  public const SEVERITY_FATAL = 'FATAL';
  protected $infoType = LocalizedString::class;
  protected $infoDataType = '';
  protected $metricType = LocalizedString::class;
  protected $metricDataType = '';
  /**
   * Whether this message is specific only for the current metric. By default
   * Diagnostics are shown for all metrics, regardless which metric is the
   * currently selected metric in the UI. However occasionally a metric will
   * generate so many messages that the resulting visual clutter becomes
   * overwhelming. In this case setting this to true, will show the diagnostic
   * messages for that metric only if it is the currently selected metric.
   *
   * @var bool
   */
  public $metricSpecific;
  /**
   * The severity of the diagnostic message.
   *
   * @var string
   */
  public $severity;
  protected $shortMessageType = LocalizedString::class;
  protected $shortMessageDataType = '';

  /**
   * Information about this diagnostic information.
   *
   * @param LocalizedString $info
   */
  public function setInfo(LocalizedString $info)
  {
    $this->info = $info;
  }
  /**
   * @return LocalizedString
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * The metric.
   *
   * @param LocalizedString $metric
   */
  public function setMetric(LocalizedString $metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return LocalizedString
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Whether this message is specific only for the current metric. By default
   * Diagnostics are shown for all metrics, regardless which metric is the
   * currently selected metric in the UI. However occasionally a metric will
   * generate so many messages that the resulting visual clutter becomes
   * overwhelming. In this case setting this to true, will show the diagnostic
   * messages for that metric only if it is the currently selected metric.
   *
   * @param bool $metricSpecific
   */
  public function setMetricSpecific($metricSpecific)
  {
    $this->metricSpecific = $metricSpecific;
  }
  /**
   * @return bool
   */
  public function getMetricSpecific()
  {
    return $this->metricSpecific;
  }
  /**
   * The severity of the diagnostic message.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFO, WARNING, ERROR, FATAL
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The short message.
   *
   * @param LocalizedString $shortMessage
   */
  public function setShortMessage(LocalizedString $shortMessage)
  {
    $this->shortMessage = $shortMessage;
  }
  /**
   * @return LocalizedString
   */
  public function getShortMessage()
  {
    return $this->shortMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiagnosticMessage::class, 'Google_Service_Spanner_DiagnosticMessage');
