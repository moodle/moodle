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

class InternalErrorDetails extends \Google\Model
{
  /**
   * Error code detail is unspecified. The error_code is not recognized by
   * Android Management API. However, see error_code
   */
  public const ERROR_CODE_DETAIL_ERROR_CODE_DETAIL_UNSPECIFIED = 'ERROR_CODE_DETAIL_UNSPECIFIED';
  /**
   * See EuiccManager.ERROR_TIME_OUT (https://developer.android.com/reference/an
   * droid/telephony/euicc/EuiccManager#ERROR_TIME_OUT) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_TIME_OUT = 'ERROR_TIME_OUT';
  /**
   * See EuiccManager.ERROR_EUICC_MISSING (https://developer.android.com/referen
   * ce/android/telephony/euicc/EuiccManager#ERROR_EUICC_MISSING) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_EUICC_MISSING = 'ERROR_EUICC_MISSING';
  /**
   * See EuiccManager.ERROR_UNSUPPORTED_VERSION (https://developer.android.com/r
   * eference/android/telephony/euicc/EuiccManager#ERROR_UNSUPPORTED_VERSION)
   * for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_UNSUPPORTED_VERSION = 'ERROR_UNSUPPORTED_VERSION';
  /**
   * See EuiccManager.ERROR_ADDRESS_MISSING (https://developer.android.com/refer
   * ence/android/telephony/euicc/EuiccManager#ERROR_ADDRESS_MISSING) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_ADDRESS_MISSING = 'ERROR_ADDRESS_MISSING';
  /**
   * See EuiccManager.ERROR_INVALID_CONFIRMATION_CODE (https://developer.android
   * .com/reference/android/telephony/euicc/EuiccManager#ERROR_INVALID_CONFIRMAT
   * ION_CODE) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INVALID_CONFIRMATION_CODE = 'ERROR_INVALID_CONFIRMATION_CODE';
  /**
   * See EuiccManager.ERROR_CERTIFICATE_ERROR (https://developer.android.com/ref
   * erence/android/telephony/euicc/EuiccManager#ERROR_CERTIFICATE_ERROR) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_CERTIFICATE_ERROR = 'ERROR_CERTIFICATE_ERROR';
  /**
   * See EuiccManager.ERROR_NO_PROFILES_AVAILABLE (https://developer.android.com
   * /reference/android/telephony/euicc/EuiccManager#ERROR_NO_PROFILES_AVAILABLE
   * ) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_NO_PROFILES_AVAILABLE = 'ERROR_NO_PROFILES_AVAILABLE';
  /**
   * See EuiccManager.ERROR_CONNECTION_ERROR (https://developer.android.com/refe
   * rence/android/telephony/euicc/EuiccManager#ERROR_CONNECTION_ERROR) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_CONNECTION_ERROR = 'ERROR_CONNECTION_ERROR';
  /**
   * See EuiccManager.ERROR_INVALID_RESPONSE (https://developer.android.com/refe
   * rence/android/telephony/euicc/EuiccManager#ERROR_INVALID_RESPONSE) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INVALID_RESPONSE = 'ERROR_INVALID_RESPONSE';
  /**
   * See EuiccManager.ERROR_CARRIER_LOCKED (https://developer.android.com/refere
   * nce/android/telephony/euicc/EuiccManager#ERROR_CARRIER_LOCKED) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_CARRIER_LOCKED = 'ERROR_CARRIER_LOCKED';
  /**
   * See EuiccManager.ERROR_DISALLOWED_BY_PPR (https://developer.android.com/ref
   * erence/android/telephony/euicc/EuiccManager#ERROR_DISALLOWED_BY_PPR) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_DISALLOWED_BY_PPR = 'ERROR_DISALLOWED_BY_PPR';
  /**
   * See EuiccManager.ERROR_INVALID_ACTIVATION_CODE (https://developer.android.c
   * om/reference/android/telephony/euicc/EuiccManager#ERROR_INVALID_ACTIVATION_
   * CODE) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INVALID_ACTIVATION_CODE = 'ERROR_INVALID_ACTIVATION_CODE';
  /**
   * See EuiccManager.ERROR_INCOMPATIBLE_CARRIER (https://developer.android.com/
   * reference/android/telephony/euicc/EuiccManager#ERROR_INCOMPATIBLE_CARRIER)
   * for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INCOMPATIBLE_CARRIER = 'ERROR_INCOMPATIBLE_CARRIER';
  /**
   * See EuiccManager.ERROR_OPERATION_BUSY (https://developer.android.com/refere
   * nce/android/telephony/euicc/EuiccManager#ERROR_OPERATION_BUSY) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_OPERATION_BUSY = 'ERROR_OPERATION_BUSY';
  /**
   * See EuiccManager.ERROR_INSTALL_PROFILE (https://developer.android.com/refer
   * ence/android/telephony/euicc/EuiccManager#ERROR_INSTALL_PROFILE) for
   * details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INSTALL_PROFILE = 'ERROR_INSTALL_PROFILE';
  /**
   * See EuiccManager.ERROR_EUICC_INSUFFICIENT_MEMORY (https://developer.android
   * .com/reference/android/telephony/euicc/EuiccManager#ERROR_EUICC_INSUFFICIEN
   * T_MEMORY) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_EUICC_INSUFFICIENT_MEMORY = 'ERROR_EUICC_INSUFFICIENT_MEMORY';
  /**
   * See EuiccManager.ERROR_INVALID_PORT (https://developer.android.com/referenc
   * e/android/telephony/euicc/EuiccManager#ERROR_INVALID_PORT) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_INVALID_PORT = 'ERROR_INVALID_PORT';
  /**
   * See EuiccManager.ERROR_SIM_MISSING (https://developer.android.com/reference
   * /android/telephony/euicc/EuiccManager#ERROR_SIM_MISSING) for details.
   */
  public const ERROR_CODE_DETAIL_ERROR_SIM_MISSING = 'ERROR_SIM_MISSING';
  /**
   * Operation code detail is unspecified. The operation_code is not recognized
   * by Android Management API. However, see operation_code.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_CODE_DETAIL_UNSPECIFIED = 'OPERATION_CODE_DETAIL_UNSPECIFIED';
  /**
   * See EuiccManager.OPERATION_SYSTEM (https://developer.android.com/reference/
   * android/telephony/euicc/EuiccManager#OPERATION_SYSTEM) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_SYSTEM = 'OPERATION_SYSTEM';
  /**
   * See EuiccManager.OPERATION_SIM_SLOT (https://developer.android.com/referenc
   * e/android/telephony/euicc/EuiccManager#OPERATION_SIM_SLOT) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_SIM_SLOT = 'OPERATION_SIM_SLOT';
  /**
   * See EuiccManager.OPERATION_EUICC_CARD (https://developer.android.com/refere
   * nce/android/telephony/euicc/EuiccManager#OPERATION_EUICC_CARD) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_EUICC_CARD = 'OPERATION_EUICC_CARD';
  /**
   * See EuiccManager.OPERATION_SMDX (https://developer.android.com/reference/an
   * droid/telephony/euicc/EuiccManager#OPERATION_SMDX) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_SMDX = 'OPERATION_SMDX';
  /**
   * See EuiccManager.OPERATION_SWITCH (https://developer.android.com/reference/
   * android/telephony/euicc/EuiccManager#OPERATION_SWITCH) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_SWITCH = 'OPERATION_SWITCH';
  /**
   * See EuiccManager.OPERATION_DOWNLOAD (https://developer.android.com/referenc
   * e/android/telephony/euicc/EuiccManager#OPERATION_DOWNLOAD) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_DOWNLOAD = 'OPERATION_DOWNLOAD';
  /**
   * See EuiccManager.OPERATION_METADATA (https://developer.android.com/referenc
   * e/android/telephony/euicc/EuiccManager#OPERATION_METADATA) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_METADATA = 'OPERATION_METADATA';
  /**
   * See EuiccManager.OPERATION_EUICC_GSMA (https://developer.android.com/refere
   * nce/android/telephony/euicc/EuiccManager#OPERATION_EUICC_GSMA) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_EUICC_GSMA = 'OPERATION_EUICC_GSMA';
  /**
   * See EuiccManager.OPERATION_APDU (https://developer.android.com/reference/an
   * droid/telephony/euicc/EuiccManager#OPERATION_APDU) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_APDU = 'OPERATION_APDU';
  /**
   * See EuiccManager.OPERATION_SMDX_SUBJECT_REASON_CODE (https://developer.andr
   * oid.com/reference/android/telephony/euicc/EuiccManager#OPERATION_SMDX_SUBJE
   * CT_REASON_CODE) for details. Note that, in this case, error_code is the
   * least significant 3 bytes of the EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE
   * (https://developer.android.com/reference/android/telephony/euicc/EuiccManag
   * er#EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE) specifying the subject code
   * and the reason code as indicated here (https://developer.android.com/refere
   * nce/android/telephony/euicc/EuiccManager#OPERATION_SMDX_SUBJECT_REASON_CODE
   * ). The most significant byte of the integer is zeroed out. For example, a
   * Subject Code of 8.11.1 and a Reason Code of 5.1 is represented in
   * error_code as 0000 0000 1000 1011 0001 0000 0101 0001 in binary, which is
   * 9113681 in decimal.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_SMDX_SUBJECT_REASON_CODE = 'OPERATION_SMDX_SUBJECT_REASON_CODE';
  /**
   * See EuiccManager.OPERATION_HTTP (https://developer.android.com/reference/an
   * droid/telephony/euicc/EuiccManager#OPERATION_HTTP) for details.
   */
  public const OPERATION_CODE_DETAIL_OPERATION_HTTP = 'OPERATION_HTTP';
  /**
   * Output only. Integer representation of the error code as specified here (ht
   * tps://developer.android.com/reference/android/telephony/euicc/EuiccManager#
   * EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE). See also,
   * OPERATION_SMDX_SUBJECT_REASON_CODE. See error_code_detail for more details.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Output only. The error code detail corresponding to the error_code.
   *
   * @var string
   */
  public $errorCodeDetail;
  /**
   * Output only. Integer representation of the operation code as specified here
   * (https://developer.android.com/reference/android/telephony/euicc/EuiccManag
   * er#EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE). See operation_code_detail
   * for more details.
   *
   * @var string
   */
  public $operationCode;
  /**
   * Output only. The operation code detail corresponding to the operation_code.
   *
   * @var string
   */
  public $operationCodeDetail;

  /**
   * Output only. Integer representation of the error code as specified here (ht
   * tps://developer.android.com/reference/android/telephony/euicc/EuiccManager#
   * EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE). See also,
   * OPERATION_SMDX_SUBJECT_REASON_CODE. See error_code_detail for more details.
   *
   * @param string $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return string
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Output only. The error code detail corresponding to the error_code.
   *
   * Accepted values: ERROR_CODE_DETAIL_UNSPECIFIED, ERROR_TIME_OUT,
   * ERROR_EUICC_MISSING, ERROR_UNSUPPORTED_VERSION, ERROR_ADDRESS_MISSING,
   * ERROR_INVALID_CONFIRMATION_CODE, ERROR_CERTIFICATE_ERROR,
   * ERROR_NO_PROFILES_AVAILABLE, ERROR_CONNECTION_ERROR,
   * ERROR_INVALID_RESPONSE, ERROR_CARRIER_LOCKED, ERROR_DISALLOWED_BY_PPR,
   * ERROR_INVALID_ACTIVATION_CODE, ERROR_INCOMPATIBLE_CARRIER,
   * ERROR_OPERATION_BUSY, ERROR_INSTALL_PROFILE,
   * ERROR_EUICC_INSUFFICIENT_MEMORY, ERROR_INVALID_PORT, ERROR_SIM_MISSING
   *
   * @param self::ERROR_CODE_DETAIL_* $errorCodeDetail
   */
  public function setErrorCodeDetail($errorCodeDetail)
  {
    $this->errorCodeDetail = $errorCodeDetail;
  }
  /**
   * @return self::ERROR_CODE_DETAIL_*
   */
  public function getErrorCodeDetail()
  {
    return $this->errorCodeDetail;
  }
  /**
   * Output only. Integer representation of the operation code as specified here
   * (https://developer.android.com/reference/android/telephony/euicc/EuiccManag
   * er#EXTRA_EMBEDDED_SUBSCRIPTION_DETAILED_CODE). See operation_code_detail
   * for more details.
   *
   * @param string $operationCode
   */
  public function setOperationCode($operationCode)
  {
    $this->operationCode = $operationCode;
  }
  /**
   * @return string
   */
  public function getOperationCode()
  {
    return $this->operationCode;
  }
  /**
   * Output only. The operation code detail corresponding to the operation_code.
   *
   * Accepted values: OPERATION_CODE_DETAIL_UNSPECIFIED, OPERATION_SYSTEM,
   * OPERATION_SIM_SLOT, OPERATION_EUICC_CARD, OPERATION_SMDX, OPERATION_SWITCH,
   * OPERATION_DOWNLOAD, OPERATION_METADATA, OPERATION_EUICC_GSMA,
   * OPERATION_APDU, OPERATION_SMDX_SUBJECT_REASON_CODE, OPERATION_HTTP
   *
   * @param self::OPERATION_CODE_DETAIL_* $operationCodeDetail
   */
  public function setOperationCodeDetail($operationCodeDetail)
  {
    $this->operationCodeDetail = $operationCodeDetail;
  }
  /**
   * @return self::OPERATION_CODE_DETAIL_*
   */
  public function getOperationCodeDetail()
  {
    return $this->operationCodeDetail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InternalErrorDetails::class, 'Google_Service_AndroidManagement_InternalErrorDetails');
