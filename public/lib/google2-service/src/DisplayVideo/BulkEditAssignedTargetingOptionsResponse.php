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

namespace Google\Service\DisplayVideo;

class BulkEditAssignedTargetingOptionsResponse extends \Google\Collection
{
  protected $collection_key = 'updatedLineItemIds';
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. The IDs of the line items which failed.
   *
   * @var string[]
   */
  public $failedLineItemIds;
  /**
   * Output only. The IDs of the line items which successfully updated.
   *
   * @var string[]
   */
  public $updatedLineItemIds;

  /**
   * The error information for each line item that failed to update.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. The IDs of the line items which failed.
   *
   * @param string[] $failedLineItemIds
   */
  public function setFailedLineItemIds($failedLineItemIds)
  {
    $this->failedLineItemIds = $failedLineItemIds;
  }
  /**
   * @return string[]
   */
  public function getFailedLineItemIds()
  {
    return $this->failedLineItemIds;
  }
  /**
   * Output only. The IDs of the line items which successfully updated.
   *
   * @param string[] $updatedLineItemIds
   */
  public function setUpdatedLineItemIds($updatedLineItemIds)
  {
    $this->updatedLineItemIds = $updatedLineItemIds;
  }
  /**
   * @return string[]
   */
  public function getUpdatedLineItemIds()
  {
    return $this->updatedLineItemIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkEditAssignedTargetingOptionsResponse::class, 'Google_Service_DisplayVideo_BulkEditAssignedTargetingOptionsResponse');
