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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1BulkDeleteConversationsRequest extends \Google\Model
{
  /**
   * Filter used to select the subset of conversations to delete.
   *
   * @var string
   */
  public $filter;
  /**
   * If set to true, all of this conversation's analyses will also be deleted.
   * Otherwise, the request will only succeed if the conversation has no
   * analyses.
   *
   * @var bool
   */
  public $force;
  /**
   * Maximum number of conversations to delete.
   *
   * @var int
   */
  public $maxDeleteCount;
  /**
   * Required. The parent resource to delete conversations from. Format:
   * projects/{project}/locations/{location}
   *
   * @var string
   */
  public $parent;

  /**
   * Filter used to select the subset of conversations to delete.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * If set to true, all of this conversation's analyses will also be deleted.
   * Otherwise, the request will only succeed if the conversation has no
   * analyses.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Maximum number of conversations to delete.
   *
   * @param int $maxDeleteCount
   */
  public function setMaxDeleteCount($maxDeleteCount)
  {
    $this->maxDeleteCount = $maxDeleteCount;
  }
  /**
   * @return int
   */
  public function getMaxDeleteCount()
  {
    return $this->maxDeleteCount;
  }
  /**
   * Required. The parent resource to delete conversations from. Format:
   * projects/{project}/locations/{location}
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1BulkDeleteConversationsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1BulkDeleteConversationsRequest');
