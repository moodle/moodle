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

namespace Google\Service\AnalyticsData;

class AudienceExport extends \Google\Collection
{
  /**
   * Unspecified state will never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The AudienceExport is currently creating and will be available in the
   * future. Creating occurs immediately after the CreateAudienceExport call.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The AudienceExport is fully created and ready for querying. An
   * AudienceExport is updated to active asynchronously from a request; this
   * occurs some time (for example 15 minutes) after the initial create call.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The AudienceExport failed to be created. It is possible that re-requesting
   * this audience export will succeed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'dimensions';
  /**
   * Required. The audience resource name. This resource name identifies the
   * audience being listed and is shared between the Analytics Data & Admin
   * APIs. Format: `properties/{property}/audiences/{audience}`
   *
   * @var string
   */
  public $audience;
  /**
   * Output only. The descriptive display name for this audience. For example,
   * "Purchasers".
   *
   * @var string
   */
  public $audienceDisplayName;
  /**
   * Output only. The time when CreateAudienceExport was called and the
   * AudienceExport began the `CREATING` state.
   *
   * @var string
   */
  public $beginCreatingTime;
  /**
   * Output only. The total quota tokens charged during creation of the
   * AudienceExport. Because this token count is based on activity from the
   * `CREATING` state, this tokens charged will be fixed once an AudienceExport
   * enters the `ACTIVE` or `FAILED` states.
   *
   * @var int
   */
  public $creationQuotaTokensCharged;
  protected $dimensionsType = V1betaAudienceDimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * Output only. Error message is populated when an audience export fails
   * during creation. A common reason for such a failure is quota exhaustion.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Output only. Identifier. The audience export resource name assigned during
   * creation. This resource name identifies this `AudienceExport`. Format:
   * `properties/{property}/audienceExports/{audience_export}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The percentage completed for this audience export ranging
   * between 0 to 100.
   *
   * @var 
   */
  public $percentageCompleted;
  /**
   * Output only. The total number of rows in the AudienceExport result.
   *
   * @var int
   */
  public $rowCount;
  /**
   * Output only. The current state for this AudienceExport.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The audience resource name. This resource name identifies the
   * audience being listed and is shared between the Analytics Data & Admin
   * APIs. Format: `properties/{property}/audiences/{audience}`
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * Output only. The descriptive display name for this audience. For example,
   * "Purchasers".
   *
   * @param string $audienceDisplayName
   */
  public function setAudienceDisplayName($audienceDisplayName)
  {
    $this->audienceDisplayName = $audienceDisplayName;
  }
  /**
   * @return string
   */
  public function getAudienceDisplayName()
  {
    return $this->audienceDisplayName;
  }
  /**
   * Output only. The time when CreateAudienceExport was called and the
   * AudienceExport began the `CREATING` state.
   *
   * @param string $beginCreatingTime
   */
  public function setBeginCreatingTime($beginCreatingTime)
  {
    $this->beginCreatingTime = $beginCreatingTime;
  }
  /**
   * @return string
   */
  public function getBeginCreatingTime()
  {
    return $this->beginCreatingTime;
  }
  /**
   * Output only. The total quota tokens charged during creation of the
   * AudienceExport. Because this token count is based on activity from the
   * `CREATING` state, this tokens charged will be fixed once an AudienceExport
   * enters the `ACTIVE` or `FAILED` states.
   *
   * @param int $creationQuotaTokensCharged
   */
  public function setCreationQuotaTokensCharged($creationQuotaTokensCharged)
  {
    $this->creationQuotaTokensCharged = $creationQuotaTokensCharged;
  }
  /**
   * @return int
   */
  public function getCreationQuotaTokensCharged()
  {
    return $this->creationQuotaTokensCharged;
  }
  /**
   * Required. The dimensions requested and displayed in the query response.
   *
   * @param V1betaAudienceDimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return V1betaAudienceDimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Output only. Error message is populated when an audience export fails
   * during creation. A common reason for such a failure is quota exhaustion.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. Identifier. The audience export resource name assigned during
   * creation. This resource name identifies this `AudienceExport`. Format:
   * `properties/{property}/audienceExports/{audience_export}`
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
  public function setPercentageCompleted($percentageCompleted)
  {
    $this->percentageCompleted = $percentageCompleted;
  }
  public function getPercentageCompleted()
  {
    return $this->percentageCompleted;
  }
  /**
   * Output only. The total number of rows in the AudienceExport result.
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * Output only. The current state for this AudienceExport.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudienceExport::class, 'Google_Service_AnalyticsData_AudienceExport');
