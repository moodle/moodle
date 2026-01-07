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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoSuspensionResolutionInfo extends \Google\Model
{
  public const PRODUCT_UNSPECIFIED_PRODUCT = 'UNSPECIFIED_PRODUCT';
  public const PRODUCT_IP = 'IP';
  public const PRODUCT_APIGEE = 'APIGEE';
  public const PRODUCT_SECURITY = 'SECURITY';
  public const STATUS_PENDING_UNSPECIFIED = 'PENDING_UNSPECIFIED';
  public const STATUS_REJECTED = 'REJECTED';
  public const STATUS_LIFTED = 'LIFTED';
  public const STATUS_CANCELED = 'CANCELED';
  protected $auditType = EnterpriseCrmEventbusProtoSuspensionResolutionInfoAudit::class;
  protected $auditDataType = '';
  /**
   * The event data user sends as request.
   *
   * @var string
   */
  public $clientId;
  protected $cloudKmsConfigType = EnterpriseCrmEventbusProtoCloudKmsConfig::class;
  protected $cloudKmsConfigDataType = '';
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $createdTimestamp;
  /**
   * Encrypted SuspensionResolutionInfo
   *
   * @var string
   */
  public $encryptedSuspensionResolutionInfo;
  /**
   * Required. ID of the associated execution.
   *
   * @var string
   */
  public $eventExecutionInfoId;
  protected $externalTrafficType = EnterpriseCrmEventbusProtoExternalTraffic::class;
  protected $externalTrafficDataType = '';
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $lastModifiedTimestamp;
  /**
   * Which Google product the suspension belongs to. If not set, the suspension
   * belongs to Integration Platform by default.
   *
   * @var string
   */
  public $product;
  /**
   * @var string
   */
  public $status;
  protected $suspensionConfigType = EnterpriseCrmEventbusProtoSuspensionConfig::class;
  protected $suspensionConfigDataType = '';
  /**
   * Primary key for the SuspensionResolutionInfoTable.
   *
   * @var string
   */
  public $suspensionId;
  /**
   * Required. Task number of the associated SuspensionTask.
   *
   * @var string
   */
  public $taskNumber;
  /**
   * Required. The name of the originating workflow.
   *
   * @var string
   */
  public $workflowName;
  /**
   * Wrapped dek
   *
   * @var string
   */
  public $wrappedDek;

  /**
   * @param EnterpriseCrmEventbusProtoSuspensionResolutionInfoAudit $audit
   */
  public function setAudit(EnterpriseCrmEventbusProtoSuspensionResolutionInfoAudit $audit)
  {
    $this->audit = $audit;
  }
  /**
   * @return EnterpriseCrmEventbusProtoSuspensionResolutionInfoAudit
   */
  public function getAudit()
  {
    return $this->audit;
  }
  /**
   * The event data user sends as request.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * KMS info, used by cmek/gmek integration
   *
   * @param EnterpriseCrmEventbusProtoCloudKmsConfig $cloudKmsConfig
   */
  public function setCloudKmsConfig(EnterpriseCrmEventbusProtoCloudKmsConfig $cloudKmsConfig)
  {
    $this->cloudKmsConfig = $cloudKmsConfig;
  }
  /**
   * @return EnterpriseCrmEventbusProtoCloudKmsConfig
   */
  public function getCloudKmsConfig()
  {
    return $this->cloudKmsConfig;
  }
  /**
   * Auto-generated.
   *
   * @param string $createdTimestamp
   */
  public function setCreatedTimestamp($createdTimestamp)
  {
    $this->createdTimestamp = $createdTimestamp;
  }
  /**
   * @return string
   */
  public function getCreatedTimestamp()
  {
    return $this->createdTimestamp;
  }
  /**
   * Encrypted SuspensionResolutionInfo
   *
   * @param string $encryptedSuspensionResolutionInfo
   */
  public function setEncryptedSuspensionResolutionInfo($encryptedSuspensionResolutionInfo)
  {
    $this->encryptedSuspensionResolutionInfo = $encryptedSuspensionResolutionInfo;
  }
  /**
   * @return string
   */
  public function getEncryptedSuspensionResolutionInfo()
  {
    return $this->encryptedSuspensionResolutionInfo;
  }
  /**
   * Required. ID of the associated execution.
   *
   * @param string $eventExecutionInfoId
   */
  public function setEventExecutionInfoId($eventExecutionInfoId)
  {
    $this->eventExecutionInfoId = $eventExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getEventExecutionInfoId()
  {
    return $this->eventExecutionInfoId;
  }
  /**
   * The origin of the suspension for periodic notifications.
   *
   * @param EnterpriseCrmEventbusProtoExternalTraffic $externalTraffic
   */
  public function setExternalTraffic(EnterpriseCrmEventbusProtoExternalTraffic $externalTraffic)
  {
    $this->externalTraffic = $externalTraffic;
  }
  /**
   * @return EnterpriseCrmEventbusProtoExternalTraffic
   */
  public function getExternalTraffic()
  {
    return $this->externalTraffic;
  }
  /**
   * Auto-generated.
   *
   * @param string $lastModifiedTimestamp
   */
  public function setLastModifiedTimestamp($lastModifiedTimestamp)
  {
    $this->lastModifiedTimestamp = $lastModifiedTimestamp;
  }
  /**
   * @return string
   */
  public function getLastModifiedTimestamp()
  {
    return $this->lastModifiedTimestamp;
  }
  /**
   * Which Google product the suspension belongs to. If not set, the suspension
   * belongs to Integration Platform by default.
   *
   * Accepted values: UNSPECIFIED_PRODUCT, IP, APIGEE, SECURITY
   *
   * @param self::PRODUCT_* $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return self::PRODUCT_*
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param EnterpriseCrmEventbusProtoSuspensionConfig $suspensionConfig
   */
  public function setSuspensionConfig(EnterpriseCrmEventbusProtoSuspensionConfig $suspensionConfig)
  {
    $this->suspensionConfig = $suspensionConfig;
  }
  /**
   * @return EnterpriseCrmEventbusProtoSuspensionConfig
   */
  public function getSuspensionConfig()
  {
    return $this->suspensionConfig;
  }
  /**
   * Primary key for the SuspensionResolutionInfoTable.
   *
   * @param string $suspensionId
   */
  public function setSuspensionId($suspensionId)
  {
    $this->suspensionId = $suspensionId;
  }
  /**
   * @return string
   */
  public function getSuspensionId()
  {
    return $this->suspensionId;
  }
  /**
   * Required. Task number of the associated SuspensionTask.
   *
   * @param string $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return string
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
  /**
   * Required. The name of the originating workflow.
   *
   * @param string $workflowName
   */
  public function setWorkflowName($workflowName)
  {
    $this->workflowName = $workflowName;
  }
  /**
   * @return string
   */
  public function getWorkflowName()
  {
    return $this->workflowName;
  }
  /**
   * Wrapped dek
   *
   * @param string $wrappedDek
   */
  public function setWrappedDek($wrappedDek)
  {
    $this->wrappedDek = $wrappedDek;
  }
  /**
   * @return string
   */
  public function getWrappedDek()
  {
    return $this->wrappedDek;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoSuspensionResolutionInfo::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoSuspensionResolutionInfo');
