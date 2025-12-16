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

namespace Google\Service\ContainerAnalysis;

class FixableTotalByDigest extends \Google\Model
{
  /**
   * Unknown.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Minimal severity.
   */
  public const SEVERITY_MINIMAL = 'MINIMAL';
  /**
   * Low severity.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Medium severity.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * High severity.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Critical severity.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * The number of fixable vulnerabilities associated with this resource.
   *
   * @var string
   */
  public $fixableCount;
  /**
   * The affected resource.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * The severity for this count. SEVERITY_UNSPECIFIED indicates total across
   * all severities.
   *
   * @var string
   */
  public $severity;
  /**
   * The total number of vulnerabilities associated with this resource.
   *
   * @var string
   */
  public $totalCount;

  /**
   * The number of fixable vulnerabilities associated with this resource.
   *
   * @param string $fixableCount
   */
  public function setFixableCount($fixableCount)
  {
    $this->fixableCount = $fixableCount;
  }
  /**
   * @return string
   */
  public function getFixableCount()
  {
    return $this->fixableCount;
  }
  /**
   * The affected resource.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * The severity for this count. SEVERITY_UNSPECIFIED indicates total across
   * all severities.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, MINIMAL, LOW, MEDIUM, HIGH, CRITICAL
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
   * The total number of vulnerabilities associated with this resource.
   *
   * @param string $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return string
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FixableTotalByDigest::class, 'Google_Service_ContainerAnalysis_FixableTotalByDigest');
