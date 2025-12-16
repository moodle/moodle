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

class Command extends \Google\Collection
{
  /**
   * There was no error.
   */
  public const ERROR_CODE_COMMAND_ERROR_CODE_UNSPECIFIED = 'COMMAND_ERROR_CODE_UNSPECIFIED';
  /**
   * An unknown error occurred.
   */
  public const ERROR_CODE_UNKNOWN = 'UNKNOWN';
  /**
   * The API level of the device does not support this command.
   */
  public const ERROR_CODE_API_LEVEL = 'API_LEVEL';
  /**
   * The management mode (profile owner, device owner, etc.) does not support
   * the command.
   */
  public const ERROR_CODE_MANAGEMENT_MODE = 'MANAGEMENT_MODE';
  /**
   * The command has an invalid parameter value.
   */
  public const ERROR_CODE_INVALID_VALUE = 'INVALID_VALUE';
  /**
   * The device doesn't support the command. Updating Android Device Policy to
   * the latest version may resolve the issue.
   */
  public const ERROR_CODE_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * This value is disallowed.
   */
  public const TYPE_COMMAND_TYPE_UNSPECIFIED = 'COMMAND_TYPE_UNSPECIFIED';
  /**
   * Lock the device, as if the lock screen timeout had expired. For a work
   * profile, if there is a separate work profile lock, this only locks the work
   * profile, with one exception: on work profiles on an organization-owned
   * device running Android 8, 9, or 10, this locks the entire device.
   */
  public const TYPE_LOCK = 'LOCK';
  /**
   * Reset the user's password.
   */
  public const TYPE_RESET_PASSWORD = 'RESET_PASSWORD';
  /**
   * Reboot the device. Only supported on fully managed devices running Android
   * 7.0 (API level 24) or higher.
   */
  public const TYPE_REBOOT = 'REBOOT';
  /**
   * Removes the work profile and all policies from a company-owned Android 8.0+
   * device, relinquishing the device for personal use. Apps and data associated
   * with the personal profile(s) are preserved. The device will be deleted from
   * the server after it acknowledges the command.
   */
  public const TYPE_RELINQUISH_OWNERSHIP = 'RELINQUISH_OWNERSHIP';
  /**
   * Clears the application data of specified apps. This is supported on Android
   * 9 and above. Note that an application can store data outside of its
   * application data, for example in external storage or in a user dictionary.
   * See also clear_apps_data_params.
   */
  public const TYPE_CLEAR_APP_DATA = 'CLEAR_APP_DATA';
  /**
   * Puts the device into lost mode. Only supported on fully managed devices or
   * organization-owned devices with a managed profile. See also
   * start_lost_mode_params.
   */
  public const TYPE_START_LOST_MODE = 'START_LOST_MODE';
  /**
   * Takes the device out of lost mode. Only supported on fully managed devices
   * or organization-owned devices with a managed profile. See also
   * stop_lost_mode_params.
   */
  public const TYPE_STOP_LOST_MODE = 'STOP_LOST_MODE';
  /**
   * Adds an eSIM profile to the device. This is supported on Android 15 and
   * above. See also addEsimParams. To remove an eSIM profile, use the
   * REMOVE_ESIM command. To determine what happens to the eSIM profile when a
   * device is wiped, set wipeDataFlags in the policy. Note: To provision
   * multiple eSIMs on a single device, it is recommended to introduce a delay
   * of a few minutes between successive executions of the command.
   */
  public const TYPE_ADD_ESIM = 'ADD_ESIM';
  /**
   * Removes an eSIM profile from the device. This is supported on Android 15
   * and above. See also removeEsimParams.
   */
  public const TYPE_REMOVE_ESIM = 'REMOVE_ESIM';
  /**
   * Request information related to the device.
   */
  public const TYPE_REQUEST_DEVICE_INFO = 'REQUEST_DEVICE_INFO';
  /**
   * Wipes the device, via a factory reset for a company owned device, or by
   * deleting the work profile for a personally owned device with work profile.
   * The wipe only occurs once the device acknowledges the command. The command
   * can be cancelled before then.
   */
  public const TYPE_WIPE = 'WIPE';
  protected $collection_key = 'resetPasswordFlags';
  protected $addEsimParamsType = AddEsimParams::class;
  protected $addEsimParamsDataType = '';
  protected $clearAppsDataParamsType = ClearAppsDataParams::class;
  protected $clearAppsDataParamsDataType = '';
  protected $clearAppsDataStatusType = ClearAppsDataStatus::class;
  protected $clearAppsDataStatusDataType = '';
  /**
   * The timestamp at which the command was created. The timestamp is
   * automatically generated by the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * The duration for which the command is valid. The command will expire if not
   * executed by the device during this time. The default duration if
   * unspecified is ten minutes. There is no maximum duration.
   *
   * @var string
   */
  public $duration;
  /**
   * If the command failed, an error code explaining the failure. This is not
   * set when the command is cancelled by the caller. For reasoning about
   * command errors, prefer fields in the following order (most preferred
   * first): 1. Command-specific fields like clearAppsDataStatus,
   * startLostModeStatus, or similar, if they exist. 2. This field, if set. 3.
   * The generic error field in the Operation that wraps the command.
   *
   * @var string
   */
  public $errorCode;
  protected $esimStatusType = EsimCommandStatus::class;
  protected $esimStatusDataType = '';
  /**
   * For commands of type RESET_PASSWORD, optionally specifies the new password.
   * Note: The new password must be at least 6 characters long if it is numeric
   * in case of Android 14 devices. Else the command will fail with
   * INVALID_VALUE.
   *
   * @var string
   */
  public $newPassword;
  protected $removeEsimParamsType = RemoveEsimParams::class;
  protected $removeEsimParamsDataType = '';
  protected $requestDeviceInfoParamsType = RequestDeviceInfoParams::class;
  protected $requestDeviceInfoParamsDataType = '';
  protected $requestDeviceInfoStatusType = RequestDeviceInfoStatus::class;
  protected $requestDeviceInfoStatusDataType = '';
  /**
   * For commands of type RESET_PASSWORD, optionally specifies flags.
   *
   * @var string[]
   */
  public $resetPasswordFlags;
  protected $startLostModeParamsType = StartLostModeParams::class;
  protected $startLostModeParamsDataType = '';
  protected $startLostModeStatusType = StartLostModeStatus::class;
  protected $startLostModeStatusDataType = '';
  protected $stopLostModeParamsType = StopLostModeParams::class;
  protected $stopLostModeParamsDataType = '';
  protected $stopLostModeStatusType = StopLostModeStatus::class;
  protected $stopLostModeStatusDataType = '';
  /**
   * The type of the command.
   *
   * @var string
   */
  public $type;
  /**
   * The resource name of the user that owns the device in the form
   * enterprises/{enterpriseId}/users/{userId}. This is automatically generated
   * by the server based on the device the command is sent to.
   *
   * @var string
   */
  public $userName;
  protected $wipeParamsType = WipeParams::class;
  protected $wipeParamsDataType = '';

  /**
   * Optional. Parameters for the ADD_ESIM command to add an eSIM profile to the
   * device. If this is set, then it is suggested that type should not be set.
   * In this case, the server automatically sets it to ADD_ESIM. It is also
   * acceptable to explicitly set type to ADD_ESIM.
   *
   * @param AddEsimParams $addEsimParams
   */
  public function setAddEsimParams(AddEsimParams $addEsimParams)
  {
    $this->addEsimParams = $addEsimParams;
  }
  /**
   * @return AddEsimParams
   */
  public function getAddEsimParams()
  {
    return $this->addEsimParams;
  }
  /**
   * Parameters for the CLEAR_APP_DATA command to clear the data of specified
   * apps from the device. See ClearAppsDataParams. If this is set, then it is
   * suggested that type should not be set. In this case, the server
   * automatically sets it to CLEAR_APP_DATA. It is also acceptable to
   * explicitly set type to CLEAR_APP_DATA.
   *
   * @param ClearAppsDataParams $clearAppsDataParams
   */
  public function setClearAppsDataParams(ClearAppsDataParams $clearAppsDataParams)
  {
    $this->clearAppsDataParams = $clearAppsDataParams;
  }
  /**
   * @return ClearAppsDataParams
   */
  public function getClearAppsDataParams()
  {
    return $this->clearAppsDataParams;
  }
  /**
   * Output only. Status of the CLEAR_APP_DATA command to clear the data of
   * specified apps from the device. See ClearAppsDataStatus.
   *
   * @param ClearAppsDataStatus $clearAppsDataStatus
   */
  public function setClearAppsDataStatus(ClearAppsDataStatus $clearAppsDataStatus)
  {
    $this->clearAppsDataStatus = $clearAppsDataStatus;
  }
  /**
   * @return ClearAppsDataStatus
   */
  public function getClearAppsDataStatus()
  {
    return $this->clearAppsDataStatus;
  }
  /**
   * The timestamp at which the command was created. The timestamp is
   * automatically generated by the server.
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
   * The duration for which the command is valid. The command will expire if not
   * executed by the device during this time. The default duration if
   * unspecified is ten minutes. There is no maximum duration.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * If the command failed, an error code explaining the failure. This is not
   * set when the command is cancelled by the caller. For reasoning about
   * command errors, prefer fields in the following order (most preferred
   * first): 1. Command-specific fields like clearAppsDataStatus,
   * startLostModeStatus, or similar, if they exist. 2. This field, if set. 3.
   * The generic error field in the Operation that wraps the command.
   *
   * Accepted values: COMMAND_ERROR_CODE_UNSPECIFIED, UNKNOWN, API_LEVEL,
   * MANAGEMENT_MODE, INVALID_VALUE, UNSUPPORTED
   *
   * @param self::ERROR_CODE_* $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return self::ERROR_CODE_*
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Output only. Status of an ADD_ESIM or REMOVE_ESIM command.
   *
   * @param EsimCommandStatus $esimStatus
   */
  public function setEsimStatus(EsimCommandStatus $esimStatus)
  {
    $this->esimStatus = $esimStatus;
  }
  /**
   * @return EsimCommandStatus
   */
  public function getEsimStatus()
  {
    return $this->esimStatus;
  }
  /**
   * For commands of type RESET_PASSWORD, optionally specifies the new password.
   * Note: The new password must be at least 6 characters long if it is numeric
   * in case of Android 14 devices. Else the command will fail with
   * INVALID_VALUE.
   *
   * @param string $newPassword
   */
  public function setNewPassword($newPassword)
  {
    $this->newPassword = $newPassword;
  }
  /**
   * @return string
   */
  public function getNewPassword()
  {
    return $this->newPassword;
  }
  /**
   * Optional. Parameters for the REMOVE_ESIM command to remove an eSIM profile
   * from the device. If this is set, then it is suggested that type should not
   * be set. In this case, the server automatically sets it to REMOVE_ESIM. It
   * is also acceptable to explicitly set type to REMOVE_ESIM.
   *
   * @param RemoveEsimParams $removeEsimParams
   */
  public function setRemoveEsimParams(RemoveEsimParams $removeEsimParams)
  {
    $this->removeEsimParams = $removeEsimParams;
  }
  /**
   * @return RemoveEsimParams
   */
  public function getRemoveEsimParams()
  {
    return $this->removeEsimParams;
  }
  /**
   * Optional. Parameters for the REQUEST_DEVICE_INFO command to get device
   * related information. If this is set, then it is suggested that type should
   * not be set. In this case, the server automatically sets it to
   * REQUEST_DEVICE_INFO . It is also acceptable to explicitly set type to
   * REQUEST_DEVICE_INFO.
   *
   * @param RequestDeviceInfoParams $requestDeviceInfoParams
   */
  public function setRequestDeviceInfoParams(RequestDeviceInfoParams $requestDeviceInfoParams)
  {
    $this->requestDeviceInfoParams = $requestDeviceInfoParams;
  }
  /**
   * @return RequestDeviceInfoParams
   */
  public function getRequestDeviceInfoParams()
  {
    return $this->requestDeviceInfoParams;
  }
  /**
   * Output only. Status of the REQUEST_DEVICE_INFO command.
   *
   * @param RequestDeviceInfoStatus $requestDeviceInfoStatus
   */
  public function setRequestDeviceInfoStatus(RequestDeviceInfoStatus $requestDeviceInfoStatus)
  {
    $this->requestDeviceInfoStatus = $requestDeviceInfoStatus;
  }
  /**
   * @return RequestDeviceInfoStatus
   */
  public function getRequestDeviceInfoStatus()
  {
    return $this->requestDeviceInfoStatus;
  }
  /**
   * For commands of type RESET_PASSWORD, optionally specifies flags.
   *
   * @param string[] $resetPasswordFlags
   */
  public function setResetPasswordFlags($resetPasswordFlags)
  {
    $this->resetPasswordFlags = $resetPasswordFlags;
  }
  /**
   * @return string[]
   */
  public function getResetPasswordFlags()
  {
    return $this->resetPasswordFlags;
  }
  /**
   * Parameters for the START_LOST_MODE command to put the device into lost
   * mode. See StartLostModeParams. If this is set, then it is suggested that
   * type should not be set. In this case, the server automatically sets it to
   * START_LOST_MODE. It is also acceptable to explicitly set type to
   * START_LOST_MODE.
   *
   * @param StartLostModeParams $startLostModeParams
   */
  public function setStartLostModeParams(StartLostModeParams $startLostModeParams)
  {
    $this->startLostModeParams = $startLostModeParams;
  }
  /**
   * @return StartLostModeParams
   */
  public function getStartLostModeParams()
  {
    return $this->startLostModeParams;
  }
  /**
   * Output only. Status of the START_LOST_MODE command to put the device into
   * lost mode. See StartLostModeStatus.
   *
   * @param StartLostModeStatus $startLostModeStatus
   */
  public function setStartLostModeStatus(StartLostModeStatus $startLostModeStatus)
  {
    $this->startLostModeStatus = $startLostModeStatus;
  }
  /**
   * @return StartLostModeStatus
   */
  public function getStartLostModeStatus()
  {
    return $this->startLostModeStatus;
  }
  /**
   * Parameters for the STOP_LOST_MODE command to take the device out of lost
   * mode. See StopLostModeParams. If this is set, then it is suggested that
   * type should not be set. In this case, the server automatically sets it to
   * STOP_LOST_MODE. It is also acceptable to explicitly set type to
   * STOP_LOST_MODE.
   *
   * @param StopLostModeParams $stopLostModeParams
   */
  public function setStopLostModeParams(StopLostModeParams $stopLostModeParams)
  {
    $this->stopLostModeParams = $stopLostModeParams;
  }
  /**
   * @return StopLostModeParams
   */
  public function getStopLostModeParams()
  {
    return $this->stopLostModeParams;
  }
  /**
   * Output only. Status of the STOP_LOST_MODE command to take the device out of
   * lost mode. See StopLostModeStatus.
   *
   * @param StopLostModeStatus $stopLostModeStatus
   */
  public function setStopLostModeStatus(StopLostModeStatus $stopLostModeStatus)
  {
    $this->stopLostModeStatus = $stopLostModeStatus;
  }
  /**
   * @return StopLostModeStatus
   */
  public function getStopLostModeStatus()
  {
    return $this->stopLostModeStatus;
  }
  /**
   * The type of the command.
   *
   * Accepted values: COMMAND_TYPE_UNSPECIFIED, LOCK, RESET_PASSWORD, REBOOT,
   * RELINQUISH_OWNERSHIP, CLEAR_APP_DATA, START_LOST_MODE, STOP_LOST_MODE,
   * ADD_ESIM, REMOVE_ESIM, REQUEST_DEVICE_INFO, WIPE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The resource name of the user that owns the device in the form
   * enterprises/{enterpriseId}/users/{userId}. This is automatically generated
   * by the server based on the device the command is sent to.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
  /**
   * Optional. Parameters for the WIPE command to wipe the device. If this is
   * set, then it is suggested that type should not be set. In this case, the
   * server automatically sets it to WIPE. It is also acceptable to explicitly
   * set type to WIPE.
   *
   * @param WipeParams $wipeParams
   */
  public function setWipeParams(WipeParams $wipeParams)
  {
    $this->wipeParams = $wipeParams;
  }
  /**
   * @return WipeParams
   */
  public function getWipeParams()
  {
    return $this->wipeParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Command::class, 'Google_Service_AndroidManagement_Command');
