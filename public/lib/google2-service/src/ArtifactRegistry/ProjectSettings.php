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

namespace Google\Service\ArtifactRegistry;

class ProjectSettings extends \Google\Model
{
  /**
   * No redirection status has been set.
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_STATE_UNSPECIFIED = 'REDIRECTION_STATE_UNSPECIFIED';
  /**
   * Redirection is disabled.
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_FROM_GCR_IO_DISABLED = 'REDIRECTION_FROM_GCR_IO_DISABLED';
  /**
   * Redirection is enabled.
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_FROM_GCR_IO_ENABLED = 'REDIRECTION_FROM_GCR_IO_ENABLED';
  /**
   * Redirection is enabled, and has been finalized so cannot be reverted.
   *
   * @deprecated
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_FROM_GCR_IO_FINALIZED = 'REDIRECTION_FROM_GCR_IO_FINALIZED';
  /**
   * Redirection is enabled and missing images are copied from GCR
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_FROM_GCR_IO_ENABLED_AND_COPYING = 'REDIRECTION_FROM_GCR_IO_ENABLED_AND_COPYING';
  /**
   * Redirection is partially enabled and missing images are copied from GCR
   */
  public const LEGACY_REDIRECTION_STATE_REDIRECTION_FROM_GCR_IO_PARTIAL_AND_COPYING = 'REDIRECTION_FROM_GCR_IO_PARTIAL_AND_COPYING';
  /**
   * The redirection state of the legacy repositories in this project.
   *
   * @var string
   */
  public $legacyRedirectionState;
  /**
   * The name of the project's settings. Always of the form: projects/{project-
   * id}/projectSettings In update request: never set In response: always set
   *
   * @var string
   */
  public $name;
  /**
   * The percentage of pull traffic to redirect from GCR to AR when using
   * partial redirection.
   *
   * @var int
   */
  public $pullPercent;

  /**
   * The redirection state of the legacy repositories in this project.
   *
   * Accepted values: REDIRECTION_STATE_UNSPECIFIED,
   * REDIRECTION_FROM_GCR_IO_DISABLED, REDIRECTION_FROM_GCR_IO_ENABLED,
   * REDIRECTION_FROM_GCR_IO_FINALIZED,
   * REDIRECTION_FROM_GCR_IO_ENABLED_AND_COPYING,
   * REDIRECTION_FROM_GCR_IO_PARTIAL_AND_COPYING
   *
   * @param self::LEGACY_REDIRECTION_STATE_* $legacyRedirectionState
   */
  public function setLegacyRedirectionState($legacyRedirectionState)
  {
    $this->legacyRedirectionState = $legacyRedirectionState;
  }
  /**
   * @return self::LEGACY_REDIRECTION_STATE_*
   */
  public function getLegacyRedirectionState()
  {
    return $this->legacyRedirectionState;
  }
  /**
   * The name of the project's settings. Always of the form: projects/{project-
   * id}/projectSettings In update request: never set In response: always set
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
   * The percentage of pull traffic to redirect from GCR to AR when using
   * partial redirection.
   *
   * @param int $pullPercent
   */
  public function setPullPercent($pullPercent)
  {
    $this->pullPercent = $pullPercent;
  }
  /**
   * @return int
   */
  public function getPullPercent()
  {
    return $this->pullPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectSettings::class, 'Google_Service_ArtifactRegistry_ProjectSettings');
