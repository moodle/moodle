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

class CleanupPolicyCondition extends \Google\Collection
{
  /**
   * Tag status not specified.
   */
  public const TAG_STATE_TAG_STATE_UNSPECIFIED = 'TAG_STATE_UNSPECIFIED';
  /**
   * Applies to tagged versions only.
   */
  public const TAG_STATE_TAGGED = 'TAGGED';
  /**
   * Applies to untagged versions only.
   */
  public const TAG_STATE_UNTAGGED = 'UNTAGGED';
  /**
   * Applies to all versions.
   */
  public const TAG_STATE_ANY = 'ANY';
  protected $collection_key = 'versionNamePrefixes';
  /**
   * Match versions newer than a duration.
   *
   * @var string
   */
  public $newerThan;
  /**
   * Match versions older than a duration.
   *
   * @var string
   */
  public $olderThan;
  /**
   * Match versions by package prefix. Applied on any prefix match.
   *
   * @var string[]
   */
  public $packageNamePrefixes;
  /**
   * Match versions by tag prefix. Applied on any prefix match.
   *
   * @var string[]
   */
  public $tagPrefixes;
  /**
   * Match versions by tag status.
   *
   * @var string
   */
  public $tagState;
  /**
   * Match versions by version name prefix. Applied on any prefix match.
   *
   * @var string[]
   */
  public $versionNamePrefixes;

  /**
   * Match versions newer than a duration.
   *
   * @param string $newerThan
   */
  public function setNewerThan($newerThan)
  {
    $this->newerThan = $newerThan;
  }
  /**
   * @return string
   */
  public function getNewerThan()
  {
    return $this->newerThan;
  }
  /**
   * Match versions older than a duration.
   *
   * @param string $olderThan
   */
  public function setOlderThan($olderThan)
  {
    $this->olderThan = $olderThan;
  }
  /**
   * @return string
   */
  public function getOlderThan()
  {
    return $this->olderThan;
  }
  /**
   * Match versions by package prefix. Applied on any prefix match.
   *
   * @param string[] $packageNamePrefixes
   */
  public function setPackageNamePrefixes($packageNamePrefixes)
  {
    $this->packageNamePrefixes = $packageNamePrefixes;
  }
  /**
   * @return string[]
   */
  public function getPackageNamePrefixes()
  {
    return $this->packageNamePrefixes;
  }
  /**
   * Match versions by tag prefix. Applied on any prefix match.
   *
   * @param string[] $tagPrefixes
   */
  public function setTagPrefixes($tagPrefixes)
  {
    $this->tagPrefixes = $tagPrefixes;
  }
  /**
   * @return string[]
   */
  public function getTagPrefixes()
  {
    return $this->tagPrefixes;
  }
  /**
   * Match versions by tag status.
   *
   * Accepted values: TAG_STATE_UNSPECIFIED, TAGGED, UNTAGGED, ANY
   *
   * @param self::TAG_STATE_* $tagState
   */
  public function setTagState($tagState)
  {
    $this->tagState = $tagState;
  }
  /**
   * @return self::TAG_STATE_*
   */
  public function getTagState()
  {
    return $this->tagState;
  }
  /**
   * Match versions by version name prefix. Applied on any prefix match.
   *
   * @param string[] $versionNamePrefixes
   */
  public function setVersionNamePrefixes($versionNamePrefixes)
  {
    $this->versionNamePrefixes = $versionNamePrefixes;
  }
  /**
   * @return string[]
   */
  public function getVersionNamePrefixes()
  {
    return $this->versionNamePrefixes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CleanupPolicyCondition::class, 'Google_Service_ArtifactRegistry_CleanupPolicyCondition');
