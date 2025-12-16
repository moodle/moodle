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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1alpha1CreateInstructionMetadata extends \Google\Collection
{
  protected $collection_key = 'partialFailures';
  /**
   * Timestamp when create instruction request was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The name of the created Instruction.
   * projects/{project_id}/instructions/{instruction_id}
   *
   * @var string
   */
  public $instruction;
  protected $partialFailuresType = GoogleRpcStatus::class;
  protected $partialFailuresDataType = 'array';

  /**
   * Timestamp when create instruction request was created.
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
   * The name of the created Instruction.
   * projects/{project_id}/instructions/{instruction_id}
   *
   * @param string $instruction
   */
  public function setInstruction($instruction)
  {
    $this->instruction = $instruction;
  }
  /**
   * @return string
   */
  public function getInstruction()
  {
    return $this->instruction;
  }
  /**
   * Partial failures encountered. E.g. single files that couldn't be read.
   * Status details field will contain standard GCP error details.
   *
   * @param GoogleRpcStatus[] $partialFailures
   */
  public function setPartialFailures($partialFailures)
  {
    $this->partialFailures = $partialFailures;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialFailures()
  {
    return $this->partialFailures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1alpha1CreateInstructionMetadata::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1alpha1CreateInstructionMetadata');
