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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1TensorboardRun extends \Google\Model
{
  /**
   * Output only. Timestamp when this TensorboardRun was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of this TensorboardRun.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User provided name of this TensorboardRun. This value must be
   * unique among all TensorboardRuns belonging to the same parent
   * TensorboardExperiment.
   *
   * @var string
   */
  public $displayName;
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * The labels with user-defined metadata to organize your TensorboardRuns.
   * This field will be used to filter and visualize Runs in the Tensorboard UI.
   * For example, a Vertex AI training job can set a label
   * aiplatform.googleapis.com/training_job_id=xxxxx to all the runs created
   * within that job. An end user can set a label experiment_id=xxxxx for all
   * the runs produced in a Jupyter notebook. These runs can be grouped by a
   * label value and visualized together in the Tensorboard UI. Label keys and
   * values can be no longer than 64 characters (Unicode codepoints), can only
   * contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one TensorboardRun (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Name of the TensorboardRun. Format: `projects/{project}/locati
   * ons/{location}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{ru
   * n}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Timestamp when this TensorboardRun was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this TensorboardRun was created.
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
   * Description of this TensorboardRun.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. User provided name of this TensorboardRun. This value must be
   * unique among all TensorboardRuns belonging to the same parent
   * TensorboardExperiment.
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
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The labels with user-defined metadata to organize your TensorboardRuns.
   * This field will be used to filter and visualize Runs in the Tensorboard UI.
   * For example, a Vertex AI training job can set a label
   * aiplatform.googleapis.com/training_job_id=xxxxx to all the runs created
   * within that job. An end user can set a label experiment_id=xxxxx for all
   * the runs produced in a Jupyter notebook. These runs can be grouped by a
   * label value and visualized together in the Tensorboard UI. Label keys and
   * values can be no longer than 64 characters (Unicode codepoints), can only
   * contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one TensorboardRun (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Name of the TensorboardRun. Format: `projects/{project}/locati
   * ons/{location}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{ru
   * n}`
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
   * Output only. Timestamp when this TensorboardRun was last updated.
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
class_alias(GoogleCloudAiplatformV1TensorboardRun::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TensorboardRun');
