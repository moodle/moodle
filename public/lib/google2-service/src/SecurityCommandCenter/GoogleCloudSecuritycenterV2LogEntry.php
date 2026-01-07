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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2LogEntry extends \Google\Model
{
  protected $cloudLoggingEntryType = GoogleCloudSecuritycenterV2CloudLoggingEntry::class;
  protected $cloudLoggingEntryDataType = '';

  /**
   * An individual entry in a log stored in Cloud Logging.
   *
   * @param GoogleCloudSecuritycenterV2CloudLoggingEntry $cloudLoggingEntry
   */
  public function setCloudLoggingEntry(GoogleCloudSecuritycenterV2CloudLoggingEntry $cloudLoggingEntry)
  {
    $this->cloudLoggingEntry = $cloudLoggingEntry;
  }
  /**
   * @return GoogleCloudSecuritycenterV2CloudLoggingEntry
   */
  public function getCloudLoggingEntry()
  {
    return $this->cloudLoggingEntry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2LogEntry::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2LogEntry');
