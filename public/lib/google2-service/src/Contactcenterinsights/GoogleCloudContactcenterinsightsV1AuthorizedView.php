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

class GoogleCloudContactcenterinsightsV1AuthorizedView extends \Google\Model
{
  /**
   * A filter to reduce conversation results to a specific subset. The
   * AuthorizedView's assigned permission (read/write) could be applied to the
   * subset of conversations. If conversation_filter is empty, there is no
   * restriction on the conversations that the AuthorizedView can access. Having
   * *authorizedViews.get* access to the AuthorizedView means having the same
   * read/write access to the Conversations (as well as metadata/annotations
   * linked to the conversation) that this AuthorizedView has.
   *
   * @var string
   */
  public $conversationFilter;
  /**
   * Output only. The time at which the authorized view was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Display Name. Limit 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name of the AuthorizedView. Format: projects/{proj
   * ect}/locations/{location}/authorizedViewSets/{authorized_view_set}/authoriz
   * edViews/{authorized_view}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The most recent time at which the authorized view was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * A filter to reduce conversation results to a specific subset. The
   * AuthorizedView's assigned permission (read/write) could be applied to the
   * subset of conversations. If conversation_filter is empty, there is no
   * restriction on the conversations that the AuthorizedView can access. Having
   * *authorizedViews.get* access to the AuthorizedView means having the same
   * read/write access to the Conversations (as well as metadata/annotations
   * linked to the conversation) that this AuthorizedView has.
   *
   * @param string $conversationFilter
   */
  public function setConversationFilter($conversationFilter)
  {
    $this->conversationFilter = $conversationFilter;
  }
  /**
   * @return string
   */
  public function getConversationFilter()
  {
    return $this->conversationFilter;
  }
  /**
   * Output only. The time at which the authorized view was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Display Name. Limit 64 characters.
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
   * Identifier. The resource name of the AuthorizedView. Format: projects/{proj
   * ect}/locations/{location}/authorizedViewSets/{authorized_view_set}/authoriz
   * edViews/{authorized_view}
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
  /**
   * Output only. The most recent time at which the authorized view was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1AuthorizedView::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AuthorizedView');
