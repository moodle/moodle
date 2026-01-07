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

namespace Google\Service\Backupdr;

class FetchMsComplianceMetadataResponse extends \Google\Model
{
  /**
   * The ms compliance metadata of the target project, if the project is an
   * assured workloads project, values will be true, otherwise false.
   *
   * @var bool
   */
  public $isAssuredWorkload;

  /**
   * The ms compliance metadata of the target project, if the project is an
   * assured workloads project, values will be true, otherwise false.
   *
   * @param bool $isAssuredWorkload
   */
  public function setIsAssuredWorkload($isAssuredWorkload)
  {
    $this->isAssuredWorkload = $isAssuredWorkload;
  }
  /**
   * @return bool
   */
  public function getIsAssuredWorkload()
  {
    return $this->isAssuredWorkload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchMsComplianceMetadataResponse::class, 'Google_Service_Backupdr_FetchMsComplianceMetadataResponse');
