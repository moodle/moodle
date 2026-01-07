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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2HybridInspectStatistics extends \Google\Model
{
  /**
   * The number of hybrid inspection requests aborted because the job ran out of
   * quota or was ended before they could be processed.
   *
   * @var string
   */
  public $abortedCount;
  /**
   * The number of hybrid requests currently being processed. Only populated
   * when called via method `getDlpJob`. A burst of traffic may cause hybrid
   * inspect requests to be enqueued. Processing will take place as quickly as
   * possible, but resource limitations may impact how long a request is
   * enqueued for.
   *
   * @var string
   */
  public $pendingCount;
  /**
   * The number of hybrid inspection requests processed within this job.
   *
   * @var string
   */
  public $processedCount;

  /**
   * The number of hybrid inspection requests aborted because the job ran out of
   * quota or was ended before they could be processed.
   *
   * @param string $abortedCount
   */
  public function setAbortedCount($abortedCount)
  {
    $this->abortedCount = $abortedCount;
  }
  /**
   * @return string
   */
  public function getAbortedCount()
  {
    return $this->abortedCount;
  }
  /**
   * The number of hybrid requests currently being processed. Only populated
   * when called via method `getDlpJob`. A burst of traffic may cause hybrid
   * inspect requests to be enqueued. Processing will take place as quickly as
   * possible, but resource limitations may impact how long a request is
   * enqueued for.
   *
   * @param string $pendingCount
   */
  public function setPendingCount($pendingCount)
  {
    $this->pendingCount = $pendingCount;
  }
  /**
   * @return string
   */
  public function getPendingCount()
  {
    return $this->pendingCount;
  }
  /**
   * The number of hybrid inspection requests processed within this job.
   *
   * @param string $processedCount
   */
  public function setProcessedCount($processedCount)
  {
    $this->processedCount = $processedCount;
  }
  /**
   * @return string
   */
  public function getProcessedCount()
  {
    return $this->processedCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2HybridInspectStatistics::class, 'Google_Service_DLP_GooglePrivacyDlpV2HybridInspectStatistics');
