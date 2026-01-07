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

class GoogleCloudContactcenterinsightsV1PhraseMatcher extends \Google\Collection
{
  /**
   * Participant's role is not set.
   */
  public const ROLE_MATCH_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Participant is a human agent.
   */
  public const ROLE_MATCH_HUMAN_AGENT = 'HUMAN_AGENT';
  /**
   * Participant is an automated agent.
   */
  public const ROLE_MATCH_AUTOMATED_AGENT = 'AUTOMATED_AGENT';
  /**
   * Participant is an end user who conversed with the contact center.
   */
  public const ROLE_MATCH_END_USER = 'END_USER';
  /**
   * Participant is either a human or automated agent.
   */
  public const ROLE_MATCH_ANY_AGENT = 'ANY_AGENT';
  /**
   * Unspecified.
   */
  public const TYPE_PHRASE_MATCHER_TYPE_UNSPECIFIED = 'PHRASE_MATCHER_TYPE_UNSPECIFIED';
  /**
   * Must meet all phrase match rule groups or there is no match.
   */
  public const TYPE_ALL_OF = 'ALL_OF';
  /**
   * If any of the phrase match rule groups are met, there is a match.
   */
  public const TYPE_ANY_OF = 'ANY_OF';
  protected $collection_key = 'phraseMatchRuleGroups';
  /**
   * Output only. The most recent time at which the activation status was
   * updated.
   *
   * @var string
   */
  public $activationUpdateTime;
  /**
   * Applies the phrase matcher only when it is active.
   *
   * @var bool
   */
  public $active;
  /**
   * The human-readable name of the phrase matcher.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name of the phrase matcher. Format:
   * projects/{project}/locations/{location}/phraseMatchers/{phrase_matcher}
   *
   * @var string
   */
  public $name;
  protected $phraseMatchRuleGroupsType = GoogleCloudContactcenterinsightsV1PhraseMatchRuleGroup::class;
  protected $phraseMatchRuleGroupsDataType = 'array';
  /**
   * Output only. The timestamp of when the revision was created. It is also the
   * create time when a new matcher is added.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. Immutable. The revision ID of the phrase matcher. A new
   * revision is committed whenever the matcher is changed, except when it is
   * activated or deactivated. A server generated random ID will be used.
   * Example: locations/global/phraseMatchers/my-first-matcher@1234567
   *
   * @var string
   */
  public $revisionId;
  /**
   * The role whose utterances the phrase matcher should be matched against. If
   * the role is ROLE_UNSPECIFIED it will be matched against any utterances in
   * the transcript.
   *
   * @var string
   */
  public $roleMatch;
  /**
   * Required. The type of this phrase matcher.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The most recent time at which the phrase matcher was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The customized version tag to use for the phrase matcher. If not specified,
   * it will default to `revision_id`.
   *
   * @var string
   */
  public $versionTag;

  /**
   * Output only. The most recent time at which the activation status was
   * updated.
   *
   * @param string $activationUpdateTime
   */
  public function setActivationUpdateTime($activationUpdateTime)
  {
    $this->activationUpdateTime = $activationUpdateTime;
  }
  /**
   * @return string
   */
  public function getActivationUpdateTime()
  {
    return $this->activationUpdateTime;
  }
  /**
   * Applies the phrase matcher only when it is active.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * The human-readable name of the phrase matcher.
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
   * The resource name of the phrase matcher. Format:
   * projects/{project}/locations/{location}/phraseMatchers/{phrase_matcher}
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
   * A list of phase match rule groups that are included in this matcher.
   *
   * @param GoogleCloudContactcenterinsightsV1PhraseMatchRuleGroup[] $phraseMatchRuleGroups
   */
  public function setPhraseMatchRuleGroups($phraseMatchRuleGroups)
  {
    $this->phraseMatchRuleGroups = $phraseMatchRuleGroups;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1PhraseMatchRuleGroup[]
   */
  public function getPhraseMatchRuleGroups()
  {
    return $this->phraseMatchRuleGroups;
  }
  /**
   * Output only. The timestamp of when the revision was created. It is also the
   * create time when a new matcher is added.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. Immutable. The revision ID of the phrase matcher. A new
   * revision is committed whenever the matcher is changed, except when it is
   * activated or deactivated. A server generated random ID will be used.
   * Example: locations/global/phraseMatchers/my-first-matcher@1234567
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * The role whose utterances the phrase matcher should be matched against. If
   * the role is ROLE_UNSPECIFIED it will be matched against any utterances in
   * the transcript.
   *
   * Accepted values: ROLE_UNSPECIFIED, HUMAN_AGENT, AUTOMATED_AGENT, END_USER,
   * ANY_AGENT
   *
   * @param self::ROLE_MATCH_* $roleMatch
   */
  public function setRoleMatch($roleMatch)
  {
    $this->roleMatch = $roleMatch;
  }
  /**
   * @return self::ROLE_MATCH_*
   */
  public function getRoleMatch()
  {
    return $this->roleMatch;
  }
  /**
   * Required. The type of this phrase matcher.
   *
   * Accepted values: PHRASE_MATCHER_TYPE_UNSPECIFIED, ALL_OF, ANY_OF
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
  /**
   * Output only. The most recent time at which the phrase matcher was updated.
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
  /**
   * The customized version tag to use for the phrase matcher. If not specified,
   * it will default to `revision_id`.
   *
   * @param string $versionTag
   */
  public function setVersionTag($versionTag)
  {
    $this->versionTag = $versionTag;
  }
  /**
   * @return string
   */
  public function getVersionTag()
  {
    return $this->versionTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1PhraseMatcher::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1PhraseMatcher');
