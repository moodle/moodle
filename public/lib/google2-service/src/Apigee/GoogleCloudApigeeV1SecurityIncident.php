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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityIncident extends \Google\Collection
{
  /**
   * The incident observability is unspecified.
   */
  public const OBSERVABILITY_OBSERVABILITY_UNSPECIFIED = 'OBSERVABILITY_UNSPECIFIED';
  /**
   * The incident is currently active. Can change to this status from archived.
   */
  public const OBSERVABILITY_ACTIVE = 'ACTIVE';
  /**
   * The incident is currently archived and was archived by the customer.
   */
  public const OBSERVABILITY_ARCHIVED = 'ARCHIVED';
  /**
   * Risk Level Unspecified.
   */
  public const RISK_LEVEL_RISK_LEVEL_UNSPECIFIED = 'RISK_LEVEL_UNSPECIFIED';
  /**
   * Risk level of the incident is low.
   */
  public const RISK_LEVEL_LOW = 'LOW';
  /**
   * Risk level of the incident is moderate.
   */
  public const RISK_LEVEL_MODERATE = 'MODERATE';
  /**
   * Risk level of the incident is severe.
   */
  public const RISK_LEVEL_SEVERE = 'SEVERE';
  protected $collection_key = 'detectionTypes';
  /**
   * Output only. Detection types which are part of the incident. Examples:
   * Flooder, OAuth Abuser, Static Content Scraper, Anomaly Detection.
   *
   * @var string[]
   */
  public $detectionTypes;
  /**
   * Optional. Display name of the security incident.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The time when events associated with the incident were first
   * detected.
   *
   * @var string
   */
  public $firstDetectedTime;
  /**
   * Output only. The time when events associated with the incident were last
   * detected.
   *
   * @var string
   */
  public $lastDetectedTime;
  /**
   * Output only. The time when the incident observability was last changed.
   *
   * @var string
   */
  public $lastObservabilityChangeTime;
  /**
   * Immutable. Name of the security incident resource. Format:
   * organizations/{org}/environments/{environment}/securityIncidents/{incident}
   * Example: organizations/apigee-
   * org/environments/dev/securityIncidents/1234-5678-9101-1111
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Indicates if the user archived this incident.
   *
   * @var string
   */
  public $observability;
  /**
   * Output only. Risk level of the incident.
   *
   * @var string
   */
  public $riskLevel;
  /**
   * Total traffic detected as part of the incident.
   *
   * @var string
   */
  public $trafficCount;

  /**
   * Output only. Detection types which are part of the incident. Examples:
   * Flooder, OAuth Abuser, Static Content Scraper, Anomaly Detection.
   *
   * @param string[] $detectionTypes
   */
  public function setDetectionTypes($detectionTypes)
  {
    $this->detectionTypes = $detectionTypes;
  }
  /**
   * @return string[]
   */
  public function getDetectionTypes()
  {
    return $this->detectionTypes;
  }
  /**
   * Optional. Display name of the security incident.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The time when events associated with the incident were first
   * detected.
   *
   * @param string $firstDetectedTime
   */
  public function setFirstDetectedTime($firstDetectedTime)
  {
    $this->firstDetectedTime = $firstDetectedTime;
  }
  /**
   * @return string
   */
  public function getFirstDetectedTime()
  {
    return $this->firstDetectedTime;
  }
  /**
   * Output only. The time when events associated with the incident were last
   * detected.
   *
   * @param string $lastDetectedTime
   */
  public function setLastDetectedTime($lastDetectedTime)
  {
    $this->lastDetectedTime = $lastDetectedTime;
  }
  /**
   * @return string
   */
  public function getLastDetectedTime()
  {
    return $this->lastDetectedTime;
  }
  /**
   * Output only. The time when the incident observability was last changed.
   *
   * @param string $lastObservabilityChangeTime
   */
  public function setLastObservabilityChangeTime($lastObservabilityChangeTime)
  {
    $this->lastObservabilityChangeTime = $lastObservabilityChangeTime;
  }
  /**
   * @return string
   */
  public function getLastObservabilityChangeTime()
  {
    return $this->lastObservabilityChangeTime;
  }
  /**
   * Immutable. Name of the security incident resource. Format:
   * organizations/{org}/environments/{environment}/securityIncidents/{incident}
   * Example: organizations/apigee-
   * org/environments/dev/securityIncidents/1234-5678-9101-1111
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
   * Optional. Indicates if the user archived this incident.
   *
   * Accepted values: OBSERVABILITY_UNSPECIFIED, ACTIVE, ARCHIVED
   *
   * @param self::OBSERVABILITY_* $observability
   */
  public function setObservability($observability)
  {
    $this->observability = $observability;
  }
  /**
   * @return self::OBSERVABILITY_*
   */
  public function getObservability()
  {
    return $this->observability;
  }
  /**
   * Output only. Risk level of the incident.
   *
   * Accepted values: RISK_LEVEL_UNSPECIFIED, LOW, MODERATE, SEVERE
   *
   * @param self::RISK_LEVEL_* $riskLevel
   */
  public function setRiskLevel($riskLevel)
  {
    $this->riskLevel = $riskLevel;
  }
  /**
   * @return self::RISK_LEVEL_*
   */
  public function getRiskLevel()
  {
    return $this->riskLevel;
  }
  /**
   * Total traffic detected as part of the incident.
   *
   * @param string $trafficCount
   */
  public function setTrafficCount($trafficCount)
  {
    $this->trafficCount = $trafficCount;
  }
  /**
   * @return string
   */
  public function getTrafficCount()
  {
    return $this->trafficCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityIncident::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityIncident');
