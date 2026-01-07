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

class TriggerBackupRequest extends \Google\Model
{
  /**
   * Optional. The duration for which backup data will be kept, while taking an
   * on-demand backup with custom retention. It is defined in "days". It is
   * mutually exclusive with rule_id. This field is required if rule_id is not
   * provided.
   *
   * @var int
   */
  public $customRetentionDays;
  /**
   * Optional. Labels to be applied on the backup.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. backup rule_id for which a backup needs to be triggered. If not
   * specified, on-demand backup with custom retention will be triggered.
   *
   * @var string
   */
  public $ruleId;

  /**
   * Optional. The duration for which backup data will be kept, while taking an
   * on-demand backup with custom retention. It is defined in "days". It is
   * mutually exclusive with rule_id. This field is required if rule_id is not
   * provided.
   *
   * @param int $customRetentionDays
   */
  public function setCustomRetentionDays($customRetentionDays)
  {
    $this->customRetentionDays = $customRetentionDays;
  }
  /**
   * @return int
   */
  public function getCustomRetentionDays()
  {
    return $this->customRetentionDays;
  }
  /**
   * Optional. Labels to be applied on the backup.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Optional. backup rule_id for which a backup needs to be triggered. If not
   * specified, on-demand backup with custom retention will be triggered.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TriggerBackupRequest::class, 'Google_Service_Backupdr_TriggerBackupRequest');
