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

namespace Google\Service\TPU;

class Symptom extends \Google\Model
{
  /**
   * Unspecified symptom.
   */
  public const SYMPTOM_TYPE_SYMPTOM_TYPE_UNSPECIFIED = 'SYMPTOM_TYPE_UNSPECIFIED';
  /**
   * TPU VM memory is low.
   */
  public const SYMPTOM_TYPE_LOW_MEMORY = 'LOW_MEMORY';
  /**
   * TPU runtime is out of memory.
   */
  public const SYMPTOM_TYPE_OUT_OF_MEMORY = 'OUT_OF_MEMORY';
  /**
   * TPU runtime execution has timed out.
   */
  public const SYMPTOM_TYPE_EXECUTE_TIMED_OUT = 'EXECUTE_TIMED_OUT';
  /**
   * TPU runtime fails to construct a mesh that recognizes each TPU device's
   * neighbors.
   */
  public const SYMPTOM_TYPE_MESH_BUILD_FAIL = 'MESH_BUILD_FAIL';
  /**
   * TPU HBM is out of memory.
   */
  public const SYMPTOM_TYPE_HBM_OUT_OF_MEMORY = 'HBM_OUT_OF_MEMORY';
  /**
   * Abusive behaviors have been identified on the current project.
   */
  public const SYMPTOM_TYPE_PROJECT_ABUSE = 'PROJECT_ABUSE';
  /**
   * Timestamp when the Symptom is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Detailed information of the current Symptom.
   *
   * @var string
   */
  public $details;
  /**
   * Type of the Symptom.
   *
   * @var string
   */
  public $symptomType;
  /**
   * A string used to uniquely distinguish a worker within a TPU node.
   *
   * @var string
   */
  public $workerId;

  /**
   * Timestamp when the Symptom is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Detailed information of the current Symptom.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Type of the Symptom.
   *
   * Accepted values: SYMPTOM_TYPE_UNSPECIFIED, LOW_MEMORY, OUT_OF_MEMORY,
   * EXECUTE_TIMED_OUT, MESH_BUILD_FAIL, HBM_OUT_OF_MEMORY, PROJECT_ABUSE
   *
   * @param self::SYMPTOM_TYPE_* $symptomType
   */
  public function setSymptomType($symptomType)
  {
    $this->symptomType = $symptomType;
  }
  /**
   * @return self::SYMPTOM_TYPE_*
   */
  public function getSymptomType()
  {
    return $this->symptomType;
  }
  /**
   * A string used to uniquely distinguish a worker within a TPU node.
   *
   * @param string $workerId
   */
  public function setWorkerId($workerId)
  {
    $this->workerId = $workerId;
  }
  /**
   * @return string
   */
  public function getWorkerId()
  {
    return $this->workerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Symptom::class, 'Google_Service_TPU_Symptom');
