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

namespace Google\Service\Sheets;

class CancelDataSourceRefreshStatus extends \Google\Model
{
  protected $referenceType = DataSourceObjectReference::class;
  protected $referenceDataType = '';
  protected $refreshCancellationStatusType = RefreshCancellationStatus::class;
  protected $refreshCancellationStatusDataType = '';

  /**
   * Reference to the data source object whose refresh is being cancelled.
   *
   * @param DataSourceObjectReference $reference
   */
  public function setReference(DataSourceObjectReference $reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return DataSourceObjectReference
   */
  public function getReference()
  {
    return $this->reference;
  }
  /**
   * The cancellation status.
   *
   * @param RefreshCancellationStatus $refreshCancellationStatus
   */
  public function setRefreshCancellationStatus(RefreshCancellationStatus $refreshCancellationStatus)
  {
    $this->refreshCancellationStatus = $refreshCancellationStatus;
  }
  /**
   * @return RefreshCancellationStatus
   */
  public function getRefreshCancellationStatus()
  {
    return $this->refreshCancellationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CancelDataSourceRefreshStatus::class, 'Google_Service_Sheets_CancelDataSourceRefreshStatus');
