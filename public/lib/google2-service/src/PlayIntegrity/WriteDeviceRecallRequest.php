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

namespace Google\Service\PlayIntegrity;

class WriteDeviceRecallRequest extends \Google\Model
{
  /**
   * Required. Integrity token obtained from calling Play Integrity API.
   *
   * @var string
   */
  public $integrityToken;
  protected $newValuesType = Values::class;
  protected $newValuesDataType = '';

  /**
   * Required. Integrity token obtained from calling Play Integrity API.
   *
   * @param string $integrityToken
   */
  public function setIntegrityToken($integrityToken)
  {
    $this->integrityToken = $integrityToken;
  }
  /**
   * @return string
   */
  public function getIntegrityToken()
  {
    return $this->integrityToken;
  }
  /**
   * Required. The new values for the device recall bits to be written.
   *
   * @param Values $newValues
   */
  public function setNewValues(Values $newValues)
  {
    $this->newValues = $newValues;
  }
  /**
   * @return Values
   */
  public function getNewValues()
  {
    return $this->newValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteDeviceRecallRequest::class, 'Google_Service_PlayIntegrity_WriteDeviceRecallRequest');
