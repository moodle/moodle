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

class GoogleCloudContactcenterinsightsV1QaScorecardRevision extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The scorecard revision can be edited.
   */
  public const STATE_EDITABLE = 'EDITABLE';
  /**
   * Scorecard model training is in progress.
   */
  public const STATE_TRAINING = 'TRAINING';
  /**
   * Scorecard revision model training failed.
   */
  public const STATE_TRAINING_FAILED = 'TRAINING_FAILED';
  /**
   * The revision can be used in analysis.
   */
  public const STATE_READY = 'READY';
  /**
   * Scorecard is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Scorecard model training was explicitly cancelled by the user.
   */
  public const STATE_TRAINING_CANCELLED = 'TRAINING_CANCELLED';
  protected $collection_key = 'alternateIds';
  /**
   * Output only. Alternative IDs for this revision of the scorecard, e.g.,
   * `latest`.
   *
   * @var string[]
   */
  public $alternateIds;
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The name of the scorecard revision. Format: projects/{project}/
   * locations/{location}/qaScorecards/{qa_scorecard}/revisions/{revision}
   *
   * @var string
   */
  public $name;
  protected $snapshotType = GoogleCloudContactcenterinsightsV1QaScorecard::class;
  protected $snapshotDataType = '';
  /**
   * Output only. State of the scorecard revision, indicating whether it's ready
   * to be used in analysis.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Alternative IDs for this revision of the scorecard, e.g.,
   * `latest`.
   *
   * @param string[] $alternateIds
   */
  public function setAlternateIds($alternateIds)
  {
    $this->alternateIds = $alternateIds;
  }
  /**
   * @return string[]
   */
  public function getAlternateIds()
  {
    return $this->alternateIds;
  }
  /**
   * Output only. The timestamp that the revision was created.
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
   * Identifier. The name of the scorecard revision. Format: projects/{project}/
   * locations/{location}/qaScorecards/{qa_scorecard}/revisions/{revision}
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
   * The snapshot of the scorecard at the time of this revision's creation.
   *
   * @param GoogleCloudContactcenterinsightsV1QaScorecard $snapshot
   */
  public function setSnapshot(GoogleCloudContactcenterinsightsV1QaScorecard $snapshot)
  {
    $this->snapshot = $snapshot;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaScorecard
   */
  public function getSnapshot()
  {
    return $this->snapshot;
  }
  /**
   * Output only. State of the scorecard revision, indicating whether it's ready
   * to be used in analysis.
   *
   * Accepted values: STATE_UNSPECIFIED, EDITABLE, TRAINING, TRAINING_FAILED,
   * READY, DELETING, TRAINING_CANCELLED
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
class_alias(GoogleCloudContactcenterinsightsV1QaScorecardRevision::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaScorecardRevision');
