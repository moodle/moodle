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

namespace Google\Service\RealTimeBidding;

class PolicyCompliance extends \Google\Collection
{
  /**
   * Default value that should never be used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Creative is pending review.
   */
  public const STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * Creative cannot serve.
   */
  public const STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * Creative is approved.
   */
  public const STATUS_APPROVED = 'APPROVED';
  /**
   * Certificates are required for the creative to be served in some regions.
   * For more information about creative certification, refer to:
   * https://support.google.com/authorizedbuyers/answer/7450776
   */
  public const STATUS_CERTIFICATE_REQUIRED = 'CERTIFICATE_REQUIRED';
  protected $collection_key = 'topics';
  /**
   * Serving status for the given transaction type (for example, open auction,
   * deals) or region (for example, China, Russia). Can be used to filter the
   * response of the creatives.list method.
   *
   * @var string
   */
  public $status;
  protected $topicsType = PolicyTopicEntry::class;
  protected $topicsDataType = 'array';

  /**
   * Serving status for the given transaction type (for example, open auction,
   * deals) or region (for example, China, Russia). Can be used to filter the
   * response of the creatives.list method.
   *
   * Accepted values: STATUS_UNSPECIFIED, PENDING_REVIEW, DISAPPROVED, APPROVED,
   * CERTIFICATE_REQUIRED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Topics related to the policy compliance for this transaction type (for
   * example, open auction, deals) or region (for example, China, Russia).
   * Topics may be present only if status is DISAPPROVED.
   *
   * @param PolicyTopicEntry[] $topics
   */
  public function setTopics($topics)
  {
    $this->topics = $topics;
  }
  /**
   * @return PolicyTopicEntry[]
   */
  public function getTopics()
  {
    return $this->topics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyCompliance::class, 'Google_Service_RealTimeBidding_PolicyCompliance');
