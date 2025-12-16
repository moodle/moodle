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

class GoogleCloudSecuritycenterV2IssueSecurityContext extends \Google\Model
{
  protected $aggregatedCountType = GoogleCloudSecuritycenterV2IssueSecurityContextAggregatedCount::class;
  protected $aggregatedCountDataType = '';
  protected $contextType = GoogleCloudSecuritycenterV2IssueSecurityContextContext::class;
  protected $contextDataType = '';

  /**
   * The aggregated count of the security context.
   *
   * @param GoogleCloudSecuritycenterV2IssueSecurityContextAggregatedCount $aggregatedCount
   */
  public function setAggregatedCount(GoogleCloudSecuritycenterV2IssueSecurityContextAggregatedCount $aggregatedCount)
  {
    $this->aggregatedCount = $aggregatedCount;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueSecurityContextAggregatedCount
   */
  public function getAggregatedCount()
  {
    return $this->aggregatedCount;
  }
  /**
   * The context of the security context.
   *
   * @param GoogleCloudSecuritycenterV2IssueSecurityContextContext $context
   */
  public function setContext(GoogleCloudSecuritycenterV2IssueSecurityContextContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueSecurityContextContext
   */
  public function getContext()
  {
    return $this->context;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IssueSecurityContext::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IssueSecurityContext');
