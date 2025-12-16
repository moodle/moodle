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

namespace Google\Service\ShoppingContent;

class RenderAccountIssuesResponse extends \Google\Collection
{
  protected $collection_key = 'issues';
  protected $alternateDisputeResolutionType = AlternateDisputeResolution::class;
  protected $alternateDisputeResolutionDataType = '';
  protected $issuesType = AccountIssue::class;
  protected $issuesDataType = 'array';

  /**
   * Alternate Dispute Resolution (ADR) is deprecated. Use
   * `prerendered_out_of_court_dispute_settlement` instead.
   *
   * @deprecated
   * @param AlternateDisputeResolution $alternateDisputeResolution
   */
  public function setAlternateDisputeResolution(AlternateDisputeResolution $alternateDisputeResolution)
  {
    $this->alternateDisputeResolution = $alternateDisputeResolution;
  }
  /**
   * @deprecated
   * @return AlternateDisputeResolution
   */
  public function getAlternateDisputeResolution()
  {
    return $this->alternateDisputeResolution;
  }
  /**
   * List of account issues for a given account. This list can be shown with
   * compressed, expandable items. In the compressed form, the title and impact
   * should be shown for each issue. Once the issue is expanded, the detailed
   * content and available actions should be rendered.
   *
   * @param AccountIssue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return AccountIssue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenderAccountIssuesResponse::class, 'Google_Service_ShoppingContent_RenderAccountIssuesResponse');
