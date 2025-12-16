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

namespace Google\Service\AuthorizedBuyersMarketplace;

class MediaPlanner extends \Google\Collection
{
  protected $collection_key = 'ancestorNames';
  /**
   * Output only. Account ID of the media planner.
   *
   * @deprecated
   * @var string
   */
  public $accountId;
  /**
   * Output only. The ancestor names of the media planner. Format:
   * `mediaPlanners/{mediaPlannerAccountId}` Can be used to filter the response
   * of the mediaPlanners.list method.
   *
   * @var string[]
   */
  public $ancestorNames;
  /**
   * Output only. The display name of the media planner. Can be used to filter
   * the response of the mediaPlanners.list method.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The unique resource name of the media planner. Format:
   * `mediaPlanners/{mediaPlannerAccountId}` Can be used to filter the response
   * of the mediaPlanners.list method.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Account ID of the media planner.
   *
   * @deprecated
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Output only. The ancestor names of the media planner. Format:
   * `mediaPlanners/{mediaPlannerAccountId}` Can be used to filter the response
   * of the mediaPlanners.list method.
   *
   * @param string[] $ancestorNames
   */
  public function setAncestorNames($ancestorNames)
  {
    $this->ancestorNames = $ancestorNames;
  }
  /**
   * @return string[]
   */
  public function getAncestorNames()
  {
    return $this->ancestorNames;
  }
  /**
   * Output only. The display name of the media planner. Can be used to filter
   * the response of the mediaPlanners.list method.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. The unique resource name of the media planner. Format:
   * `mediaPlanners/{mediaPlannerAccountId}` Can be used to filter the response
   * of the mediaPlanners.list method.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediaPlanner::class, 'Google_Service_AuthorizedBuyersMarketplace_MediaPlanner');
