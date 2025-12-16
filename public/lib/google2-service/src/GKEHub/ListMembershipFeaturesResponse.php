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

namespace Google\Service\GKEHub;

class ListMembershipFeaturesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $membershipFeaturesType = MembershipFeature::class;
  protected $membershipFeaturesDataType = 'array';
  /**
   * A token to request the next page of resources from the
   * `ListMembershipFeatures` method. The value of an empty string means that
   * there are no more resources to return.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * List of locations that could not be reached while fetching this list.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of matching MembershipFeatures.
   *
   * @param MembershipFeature[] $membershipFeatures
   */
  public function setMembershipFeatures($membershipFeatures)
  {
    $this->membershipFeatures = $membershipFeatures;
  }
  /**
   * @return MembershipFeature[]
   */
  public function getMembershipFeatures()
  {
    return $this->membershipFeatures;
  }
  /**
   * A token to request the next page of resources from the
   * `ListMembershipFeatures` method. The value of an empty string means that
   * there are no more resources to return.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * List of locations that could not be reached while fetching this list.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListMembershipFeaturesResponse::class, 'Google_Service_GKEHub_ListMembershipFeaturesResponse');
