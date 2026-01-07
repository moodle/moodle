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

namespace Google\Service\AndroidManagement;

class EsimCommandStatus extends \Google\Model
{
  /**
   * Unspecified. This value is not used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The eSIM operation was successfully performed on the device.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * The eSIM operation is in progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The user needs to take an action for the eSIM operation to proceed.
   */
  public const STATUS_PENDING_USER_ACTION = 'PENDING_USER_ACTION';
  /**
   * The eSIM operation cannot be executed when setup is in progress.
   */
  public const STATUS_ERROR_SETUP_IN_PROGRESS = 'ERROR_SETUP_IN_PROGRESS';
  /**
   * The user has denied the eSIM operation.
   */
  public const STATUS_ERROR_USER_DENIED = 'ERROR_USER_DENIED';
  /**
   * An error has occurred while trying to add or remove the eSIM on the device,
   * see internal_error_details.
   */
  public const STATUS_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * For a REMOVE_ESIM command, the iccId of the eSIM to be removed was not
   * found on the device. This could either mean the eSIM does not belong to the
   * enterprise or the eSIM corresponding to the iccId is not present on the
   * device.
   */
  public const STATUS_ERROR_ICC_ID_NOT_FOUND = 'ERROR_ICC_ID_NOT_FOUND';
  /**
   * The ADD_ESIM command failed when attempting to add a new eSIM with its
   * activation state set to ACTIVATED since multiple eSIM slots on the device
   * contain active eSIM profiles and there is no free eSIM slot available. To
   * resolve this, the new eSIM can be added with its activation state as
   * NOT_ACTIVATED for later manual activation, or the user must first
   * deactivate an existing active eSIM for the operation to proceed.
   */
  public const STATUS_ERROR_MULTIPLE_ACTIVE_ESIMS_NO_AVAILABLE_SLOT = 'ERROR_MULTIPLE_ACTIVE_ESIMS_NO_AVAILABLE_SLOT';
  protected $esimInfoType = EsimInfo::class;
  protected $esimInfoDataType = '';
  protected $internalErrorDetailsType = InternalErrorDetails::class;
  protected $internalErrorDetailsDataType = '';
  /**
   * Output only. Status of an ADD_ESIM or REMOVE_ESIM command.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Information about the eSIM added or removed. This is populated
   * only when the eSIM operation status is SUCCESS.
   *
   * @param EsimInfo $esimInfo
   */
  public function setEsimInfo(EsimInfo $esimInfo)
  {
    $this->esimInfo = $esimInfo;
  }
  /**
   * @return EsimInfo
   */
  public function getEsimInfo()
  {
    return $this->esimInfo;
  }
  /**
   * Output only. Details of the error if the status is set to INTERNAL_ERROR.
   *
   * @param InternalErrorDetails $internalErrorDetails
   */
  public function setInternalErrorDetails(InternalErrorDetails $internalErrorDetails)
  {
    $this->internalErrorDetails = $internalErrorDetails;
  }
  /**
   * @return InternalErrorDetails
   */
  public function getInternalErrorDetails()
  {
    return $this->internalErrorDetails;
  }
  /**
   * Output only. Status of an ADD_ESIM or REMOVE_ESIM command.
   *
   * Accepted values: STATUS_UNSPECIFIED, SUCCESS, IN_PROGRESS,
   * PENDING_USER_ACTION, ERROR_SETUP_IN_PROGRESS, ERROR_USER_DENIED,
   * INTERNAL_ERROR, ERROR_ICC_ID_NOT_FOUND,
   * ERROR_MULTIPLE_ACTIVE_ESIMS_NO_AVAILABLE_SLOT
   *
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EsimCommandStatus::class, 'Google_Service_AndroidManagement_EsimCommandStatus');
