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

namespace Google\Service\Bigquery;

class ExternalServiceCost extends \Google\Model
{
  /**
   * The billing method used for the external job. This field, set to
   * `SERVICES_SKU`, is only used when billing under the services SKU.
   * Otherwise, it is unspecified for backward compatibility.
   *
   * @var string
   */
  public $billingMethod;
  /**
   * External service cost in terms of bigquery bytes billed.
   *
   * @var string
   */
  public $bytesBilled;
  /**
   * External service cost in terms of bigquery bytes processed.
   *
   * @var string
   */
  public $bytesProcessed;
  /**
   * External service name.
   *
   * @var string
   */
  public $externalService;
  /**
   * Non-preemptable reserved slots used for external job. For example, reserved
   * slots for Cloua AI Platform job are the VM usages converted to BigQuery
   * slot with equivalent mount of price.
   *
   * @var string
   */
  public $reservedSlotCount;
  /**
   * External service cost in terms of bigquery slot milliseconds.
   *
   * @var string
   */
  public $slotMs;

  /**
   * The billing method used for the external job. This field, set to
   * `SERVICES_SKU`, is only used when billing under the services SKU.
   * Otherwise, it is unspecified for backward compatibility.
   *
   * @param string $billingMethod
   */
  public function setBillingMethod($billingMethod)
  {
    $this->billingMethod = $billingMethod;
  }
  /**
   * @return string
   */
  public function getBillingMethod()
  {
    return $this->billingMethod;
  }
  /**
   * External service cost in terms of bigquery bytes billed.
   *
   * @param string $bytesBilled
   */
  public function setBytesBilled($bytesBilled)
  {
    $this->bytesBilled = $bytesBilled;
  }
  /**
   * @return string
   */
  public function getBytesBilled()
  {
    return $this->bytesBilled;
  }
  /**
   * External service cost in terms of bigquery bytes processed.
   *
   * @param string $bytesProcessed
   */
  public function setBytesProcessed($bytesProcessed)
  {
    $this->bytesProcessed = $bytesProcessed;
  }
  /**
   * @return string
   */
  public function getBytesProcessed()
  {
    return $this->bytesProcessed;
  }
  /**
   * External service name.
   *
   * @param string $externalService
   */
  public function setExternalService($externalService)
  {
    $this->externalService = $externalService;
  }
  /**
   * @return string
   */
  public function getExternalService()
  {
    return $this->externalService;
  }
  /**
   * Non-preemptable reserved slots used for external job. For example, reserved
   * slots for Cloua AI Platform job are the VM usages converted to BigQuery
   * slot with equivalent mount of price.
   *
   * @param string $reservedSlotCount
   */
  public function setReservedSlotCount($reservedSlotCount)
  {
    $this->reservedSlotCount = $reservedSlotCount;
  }
  /**
   * @return string
   */
  public function getReservedSlotCount()
  {
    return $this->reservedSlotCount;
  }
  /**
   * External service cost in terms of bigquery slot milliseconds.
   *
   * @param string $slotMs
   */
  public function setSlotMs($slotMs)
  {
    $this->slotMs = $slotMs;
  }
  /**
   * @return string
   */
  public function getSlotMs()
  {
    return $this->slotMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalServiceCost::class, 'Google_Service_Bigquery_ExternalServiceCost');
