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

namespace Google\Service\SecurityCommandCenter;

class ProcessSignature extends \Google\Model
{
  /**
   * The default signature type.
   */
  public const SIGNATURE_TYPE_SIGNATURE_TYPE_UNSPECIFIED = 'SIGNATURE_TYPE_UNSPECIFIED';
  /**
   * Used for signatures concerning processes.
   */
  public const SIGNATURE_TYPE_SIGNATURE_TYPE_PROCESS = 'SIGNATURE_TYPE_PROCESS';
  /**
   * Used for signatures concerning disks.
   */
  public const SIGNATURE_TYPE_SIGNATURE_TYPE_FILE = 'SIGNATURE_TYPE_FILE';
  protected $memoryHashSignatureType = MemoryHashSignature::class;
  protected $memoryHashSignatureDataType = '';
  /**
   * Describes the type of resource associated with the signature.
   *
   * @var string
   */
  public $signatureType;
  protected $yaraRuleSignatureType = YaraRuleSignature::class;
  protected $yaraRuleSignatureDataType = '';

  /**
   * Signature indicating that a binary family was matched.
   *
   * @param MemoryHashSignature $memoryHashSignature
   */
  public function setMemoryHashSignature(MemoryHashSignature $memoryHashSignature)
  {
    $this->memoryHashSignature = $memoryHashSignature;
  }
  /**
   * @return MemoryHashSignature
   */
  public function getMemoryHashSignature()
  {
    return $this->memoryHashSignature;
  }
  /**
   * Describes the type of resource associated with the signature.
   *
   * Accepted values: SIGNATURE_TYPE_UNSPECIFIED, SIGNATURE_TYPE_PROCESS,
   * SIGNATURE_TYPE_FILE
   *
   * @param self::SIGNATURE_TYPE_* $signatureType
   */
  public function setSignatureType($signatureType)
  {
    $this->signatureType = $signatureType;
  }
  /**
   * @return self::SIGNATURE_TYPE_*
   */
  public function getSignatureType()
  {
    return $this->signatureType;
  }
  /**
   * Signature indicating that a YARA rule was matched.
   *
   * @param YaraRuleSignature $yaraRuleSignature
   */
  public function setYaraRuleSignature(YaraRuleSignature $yaraRuleSignature)
  {
    $this->yaraRuleSignature = $yaraRuleSignature;
  }
  /**
   * @return YaraRuleSignature
   */
  public function getYaraRuleSignature()
  {
    return $this->yaraRuleSignature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProcessSignature::class, 'Google_Service_SecurityCommandCenter_ProcessSignature');
