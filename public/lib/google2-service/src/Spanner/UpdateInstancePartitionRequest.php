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

class UpdateInstancePartitionRequest extends \Google\Model
{
  /**
   * Required. A mask specifying which fields in InstancePartition should be
   * updated. The field mask must always be specified; this prevents any future
   * fields in InstancePartition from being erased accidentally by clients that
   * do not know about them.
   *
   * @var string
   */
  public $fieldMask;
  protected $instancePartitionType = InstancePartition::class;
  protected $instancePartitionDataType = '';

  /**
   * Required. A mask specifying which fields in InstancePartition should be
   * updated. The field mask must always be specified; this prevents any future
   * fields in InstancePartition from being erased accidentally by clients that
   * do not know about them.
   *
   * @param string $fieldMask
   */
  public function setFieldMask($fieldMask)
  {
    $this->fieldMask = $fieldMask;
  }
  /**
   * @return string
   */
  public function getFieldMask()
  {
    return $this->fieldMask;
  }
  /**
   * Required. The instance partition to update, which must always include the
   * instance partition name. Otherwise, only fields mentioned in field_mask
   * need be included.
   *
   * @param InstancePartition $instancePartition
   */
  public function setInstancePartition(InstancePartition $instancePartition)
  {
    $this->instancePartition = $instancePartition;
  }
  /**
   * @return InstancePartition
   */
  public function getInstancePartition()
  {
    return $this->instancePartition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateInstancePartitionRequest::class, 'Google_Service_Spanner_UpdateInstancePartitionRequest');
