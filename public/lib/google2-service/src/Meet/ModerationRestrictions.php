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

namespace Google\Service\Meet;

class ModerationRestrictions extends \Google\Model
{
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const CHAT_RESTRICTION_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * Meeting owner and co-host have the permission.
   */
  public const CHAT_RESTRICTION_HOSTS_ONLY = 'HOSTS_ONLY';
  /**
   * All Participants have permissions.
   */
  public const CHAT_RESTRICTION_NO_RESTRICTION = 'NO_RESTRICTION';
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const DEFAULT_JOIN_AS_VIEWER_TYPE_DEFAULT_JOIN_AS_VIEWER_TYPE_UNSPECIFIED = 'DEFAULT_JOIN_AS_VIEWER_TYPE_UNSPECIFIED';
  /**
   * Users will by default join as viewers.
   */
  public const DEFAULT_JOIN_AS_VIEWER_TYPE_ON = 'ON';
  /**
   * Users will by default join as contributors.
   */
  public const DEFAULT_JOIN_AS_VIEWER_TYPE_OFF = 'OFF';
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const PRESENT_RESTRICTION_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * Meeting owner and co-host have the permission.
   */
  public const PRESENT_RESTRICTION_HOSTS_ONLY = 'HOSTS_ONLY';
  /**
   * All Participants have permissions.
   */
  public const PRESENT_RESTRICTION_NO_RESTRICTION = 'NO_RESTRICTION';
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const REACTION_RESTRICTION_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * Meeting owner and co-host have the permission.
   */
  public const REACTION_RESTRICTION_HOSTS_ONLY = 'HOSTS_ONLY';
  /**
   * All Participants have permissions.
   */
  public const REACTION_RESTRICTION_NO_RESTRICTION = 'NO_RESTRICTION';
  /**
   * Defines who has permission to send chat messages in the meeting space.
   *
   * @var string
   */
  public $chatRestriction;
  /**
   * Defines whether to restrict the default role assigned to users as viewer.
   *
   * @var string
   */
  public $defaultJoinAsViewerType;
  /**
   * Defines who has permission to share their screen in the meeting space.
   *
   * @var string
   */
  public $presentRestriction;
  /**
   * Defines who has permission to send reactions in the meeting space.
   *
   * @var string
   */
  public $reactionRestriction;

  /**
   * Defines who has permission to send chat messages in the meeting space.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, HOSTS_ONLY, NO_RESTRICTION
   *
   * @param self::CHAT_RESTRICTION_* $chatRestriction
   */
  public function setChatRestriction($chatRestriction)
  {
    $this->chatRestriction = $chatRestriction;
  }
  /**
   * @return self::CHAT_RESTRICTION_*
   */
  public function getChatRestriction()
  {
    return $this->chatRestriction;
  }
  /**
   * Defines whether to restrict the default role assigned to users as viewer.
   *
   * Accepted values: DEFAULT_JOIN_AS_VIEWER_TYPE_UNSPECIFIED, ON, OFF
   *
   * @param self::DEFAULT_JOIN_AS_VIEWER_TYPE_* $defaultJoinAsViewerType
   */
  public function setDefaultJoinAsViewerType($defaultJoinAsViewerType)
  {
    $this->defaultJoinAsViewerType = $defaultJoinAsViewerType;
  }
  /**
   * @return self::DEFAULT_JOIN_AS_VIEWER_TYPE_*
   */
  public function getDefaultJoinAsViewerType()
  {
    return $this->defaultJoinAsViewerType;
  }
  /**
   * Defines who has permission to share their screen in the meeting space.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, HOSTS_ONLY, NO_RESTRICTION
   *
   * @param self::PRESENT_RESTRICTION_* $presentRestriction
   */
  public function setPresentRestriction($presentRestriction)
  {
    $this->presentRestriction = $presentRestriction;
  }
  /**
   * @return self::PRESENT_RESTRICTION_*
   */
  public function getPresentRestriction()
  {
    return $this->presentRestriction;
  }
  /**
   * Defines who has permission to send reactions in the meeting space.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, HOSTS_ONLY, NO_RESTRICTION
   *
   * @param self::REACTION_RESTRICTION_* $reactionRestriction
   */
  public function setReactionRestriction($reactionRestriction)
  {
    $this->reactionRestriction = $reactionRestriction;
  }
  /**
   * @return self::REACTION_RESTRICTION_*
   */
  public function getReactionRestriction()
  {
    return $this->reactionRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModerationRestrictions::class, 'Google_Service_Meet_ModerationRestrictions');
