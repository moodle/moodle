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

class PerAppResult extends \Google\Model
{
  /**
   * Unspecified result.
   */
  public const CLEARING_RESULT_CLEARING_RESULT_UNSPECIFIED = 'CLEARING_RESULT_UNSPECIFIED';
  /**
   * This app’s data was successfully cleared.
   */
  public const CLEARING_RESULT_SUCCESS = 'SUCCESS';
  /**
   * This app’s data could not be cleared because the app was not found.
   */
  public const CLEARING_RESULT_APP_NOT_FOUND = 'APP_NOT_FOUND';
  /**
   * This app’s data could not be cleared because the app is protected. For
   * example, this may apply to apps critical to the functioning of the device,
   * such as Google Play Store.
   */
  public const CLEARING_RESULT_APP_PROTECTED = 'APP_PROTECTED';
  /**
   * This app’s data could not be cleared because the device API level does not
   * support this command.
   */
  public const CLEARING_RESULT_API_LEVEL = 'API_LEVEL';
  /**
   * The result of an attempt to clear the data of a single app.
   *
   * @var string
   */
  public $clearingResult;

  /**
   * The result of an attempt to clear the data of a single app.
   *
   * Accepted values: CLEARING_RESULT_UNSPECIFIED, SUCCESS, APP_NOT_FOUND,
   * APP_PROTECTED, API_LEVEL
   *
   * @param self::CLEARING_RESULT_* $clearingResult
   */
  public function setClearingResult($clearingResult)
  {
    $this->clearingResult = $clearingResult;
  }
  /**
   * @return self::CLEARING_RESULT_*
   */
  public function getClearingResult()
  {
    return $this->clearingResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerAppResult::class, 'Google_Service_AndroidManagement_PerAppResult');
