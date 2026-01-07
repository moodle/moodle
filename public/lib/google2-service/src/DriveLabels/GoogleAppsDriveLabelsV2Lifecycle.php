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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2Lifecycle extends \Google\Model
{
  /**
   * Unknown State.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The initial state of an object. Once published, the object can never return
   * to this state. Once an object is published, certain kinds of changes are no
   * longer permitted.
   */
  public const STATE_UNPUBLISHED_DRAFT = 'UNPUBLISHED_DRAFT';
  /**
   * The object has been published. The object might have unpublished draft
   * changes as indicated by `has_unpublished_changes`.
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  /**
   * The object has been published and has since been disabled. The object might
   * have unpublished draft changes as indicated by `has_unpublished_changes`.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The object has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $disabledPolicyType = GoogleAppsDriveLabelsV2LifecycleDisabledPolicy::class;
  protected $disabledPolicyDataType = '';
  /**
   * Output only. Whether the object associated with this lifecycle has
   * unpublished changes.
   *
   * @var bool
   */
  public $hasUnpublishedChanges;
  /**
   * Output only. The state of the object associated with this lifecycle.
   *
   * @var string
   */
  public $state;

  /**
   * The policy that governs how to show a disabled label, field, or selection
   * choice.
   *
   * @param GoogleAppsDriveLabelsV2LifecycleDisabledPolicy $disabledPolicy
   */
  public function setDisabledPolicy(GoogleAppsDriveLabelsV2LifecycleDisabledPolicy $disabledPolicy)
  {
    $this->disabledPolicy = $disabledPolicy;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LifecycleDisabledPolicy
   */
  public function getDisabledPolicy()
  {
    return $this->disabledPolicy;
  }
  /**
   * Output only. Whether the object associated with this lifecycle has
   * unpublished changes.
   *
   * @param bool $hasUnpublishedChanges
   */
  public function setHasUnpublishedChanges($hasUnpublishedChanges)
  {
    $this->hasUnpublishedChanges = $hasUnpublishedChanges;
  }
  /**
   * @return bool
   */
  public function getHasUnpublishedChanges()
  {
    return $this->hasUnpublishedChanges;
  }
  /**
   * Output only. The state of the object associated with this lifecycle.
   *
   * Accepted values: STATE_UNSPECIFIED, UNPUBLISHED_DRAFT, PUBLISHED, DISABLED,
   * DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2Lifecycle::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2Lifecycle');
