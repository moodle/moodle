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

namespace Google\Service\HangoutsChat;

class WorkflowDataSourceMarkup extends \Google\Model
{
  /**
   * Default value. Don't use.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Google Workspace users. The user can only view and select users from their
   * Google Workspace organization.
   */
  public const TYPE_USER = 'USER';
  /**
   * Google Chat spaces that the user is a member of.
   */
  public const TYPE_SPACE = 'SPACE';
  /**
   * Users can choose to view and select existing members from their Google
   * Workspace organization or manually enter an email address or a valid
   * domain.
   */
  public const TYPE_USER_WITH_FREE_FORM = 'USER_WITH_FREE_FORM';
  /**
   * Whether to include variables from the previous step in the data source.
   *
   * @var bool
   */
  public $includeVariables;
  /**
   * The type of data source.
   *
   * @var string
   */
  public $type;

  /**
   * Whether to include variables from the previous step in the data source.
   *
   * @param bool $includeVariables
   */
  public function setIncludeVariables($includeVariables)
  {
    $this->includeVariables = $includeVariables;
  }
  /**
   * @return bool
   */
  public function getIncludeVariables()
  {
    return $this->includeVariables;
  }
  /**
   * The type of data source.
   *
   * Accepted values: UNKNOWN, USER, SPACE, USER_WITH_FREE_FORM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowDataSourceMarkup::class, 'Google_Service_HangoutsChat_WorkflowDataSourceMarkup');
