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

namespace Google\Service\Dataflow;

class SdkBug extends \Google\Model
{
  /**
   * A bug of unknown severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * A minor bug that that may reduce reliability or performance for some jobs.
   * Impact will be minimal or non-existent for most jobs.
   */
  public const SEVERITY_NOTICE = 'NOTICE';
  /**
   * A bug that has some likelihood of causing performance degradation, data
   * loss, or job failures.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * A bug with extremely significant impact. Jobs may fail erroneously,
   * performance may be severely degraded, and data loss may be very likely.
   */
  public const SEVERITY_SEVERE = 'SEVERE';
  /**
   * Unknown issue with this SDK.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Catch-all for SDK bugs that don't fit in the below categories.
   */
  public const TYPE_GENERAL = 'GENERAL';
  /**
   * Using this version of the SDK may result in degraded performance.
   */
  public const TYPE_PERFORMANCE = 'PERFORMANCE';
  /**
   * Using this version of the SDK may cause data loss.
   */
  public const TYPE_DATALOSS = 'DATALOSS';
  /**
   * Output only. How severe the SDK bug is.
   *
   * @var string
   */
  public $severity;
  /**
   * Output only. Describes the impact of this SDK bug.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Link to more information on the bug.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. How severe the SDK bug is.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, NOTICE, WARNING, SEVERE
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
   * Output only. Describes the impact of this SDK bug.
   *
   * Accepted values: TYPE_UNSPECIFIED, GENERAL, PERFORMANCE, DATALOSS
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
  /**
   * Output only. Link to more information on the bug.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SdkBug::class, 'Google_Service_Dataflow_SdkBug');
