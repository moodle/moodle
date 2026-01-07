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

namespace Google\Service\Bigquery;

class InputDataChange extends \Google\Model
{
  /**
   * Output only. Records read difference percentage compared to a previous run.
   *
   * @var float
   */
  public $recordsReadDiffPercentage;

  /**
   * Output only. Records read difference percentage compared to a previous run.
   *
   * @param float $recordsReadDiffPercentage
   */
  public function setRecordsReadDiffPercentage($recordsReadDiffPercentage)
  {
    $this->recordsReadDiffPercentage = $recordsReadDiffPercentage;
  }
  /**
   * @return float
   */
  public function getRecordsReadDiffPercentage()
  {
    return $this->recordsReadDiffPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputDataChange::class, 'Google_Service_Bigquery_InputDataChange');
