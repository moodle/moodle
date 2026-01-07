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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicyModificationErrorDetails extends \Google\Collection
{
  protected $collection_key = 'modificationErrors';
  protected $modificationErrorsType = GoogleChromePolicyVersionsV1PolicyModificationError::class;
  protected $modificationErrorsDataType = 'array';

  /**
   * Output only. List of specific policy modifications errors that may have
   * occurred during a modifying request.
   *
   * @param GoogleChromePolicyVersionsV1PolicyModificationError[] $modificationErrors
   */
  public function setModificationErrors($modificationErrors)
  {
    $this->modificationErrors = $modificationErrors;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyModificationError[]
   */
  public function getModificationErrors()
  {
    return $this->modificationErrors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicyModificationErrorDetails::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicyModificationErrorDetails');
