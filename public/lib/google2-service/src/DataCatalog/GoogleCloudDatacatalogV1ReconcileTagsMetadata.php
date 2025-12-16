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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1ReconcileTagsMetadata extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_RECONCILIATION_STATE_UNSPECIFIED = 'RECONCILIATION_STATE_UNSPECIFIED';
  /**
   * The reconciliation has been queued and awaits for execution.
   */
  public const STATE_RECONCILIATION_QUEUED = 'RECONCILIATION_QUEUED';
  /**
   * The reconciliation is in progress.
   */
  public const STATE_RECONCILIATION_IN_PROGRESS = 'RECONCILIATION_IN_PROGRESS';
  /**
   * The reconciliation has been finished.
   */
  public const STATE_RECONCILIATION_DONE = 'RECONCILIATION_DONE';
  protected $errorsType = Status::class;
  protected $errorsDataType = 'map';
  /**
   * State of the reconciliation operation.
   *
   * @var string
   */
  public $state;

  /**
   * Maps the name of each tagged column (or empty string for a sole entry) to
   * tagging operation status.
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
   * State of the reconciliation operation.
   *
   * Accepted values: RECONCILIATION_STATE_UNSPECIFIED, RECONCILIATION_QUEUED,
   * RECONCILIATION_IN_PROGRESS, RECONCILIATION_DONE
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
class_alias(GoogleCloudDatacatalogV1ReconcileTagsMetadata::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ReconcileTagsMetadata');
