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

namespace Google\Service\DataManager;

class IngestEventsStatus extends \Google\Model
{
  /**
   * The total count of events sent in the upload request. Includes all events
   * in the request, regardless of whether they were successfully ingested or
   * not.
   *
   * @var string
   */
  public $recordCount;

  /**
   * The total count of events sent in the upload request. Includes all events
   * in the request, regardless of whether they were successfully ingested or
   * not.
   *
   * @param string $recordCount
   */
  public function setRecordCount($recordCount)
  {
    $this->recordCount = $recordCount;
  }
  /**
   * @return string
   */
  public function getRecordCount()
  {
    return $this->recordCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestEventsStatus::class, 'Google_Service_DataManager_IngestEventsStatus');
