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

class GoogleCloudAiplatformV1TensorboardExperiment extends \Google\Model
{
  /**
   * Output only. Timestamp when this TensorboardExperiment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of this TensorboardExperiment.
   *
   * @var string
   */
  public $description;
  /**
   * User provided name of this TensorboardExperiment.
   *
   * @var string
   */
  public $displayName;
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * The labels with user-defined metadata to organize your
   * TensorboardExperiment. Label keys and values cannot be longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. No more than 64 user labels can be associated with one Dataset
   * (System labels are excluded). See https://goo.gl/xmQnxf for more
   * information and examples of labels. System reserved label keys are prefixed
   * with `aiplatform.googleapis.com/` and are immutable. The following system
   * labels exist for each Dataset: *
   * `aiplatform.googleapis.com/dataset_metadata_schema`: output only. Its value
   * is the metadata_schema's title.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Name of the TensorboardExperiment. Format: `projects/{project}
   * /locations/{location}/tensorboards/{tensorboard}/experiments/{experiment}`
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Source of the TensorboardExperiment. Example: a custom training
   * job.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. Timestamp when this TensorboardExperiment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this TensorboardExperiment was created.
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
   * Description of this TensorboardExperiment.
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
   * User provided name of this TensorboardExperiment.
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
   * Used to perform consistent read-modify-write updates. If not set, a blind
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
   * The labels with user-defined metadata to organize your
   * TensorboardExperiment. Label keys and values cannot be longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. No more than 64 user labels can be associated with one Dataset
   * (System labels are excluded). See https://goo.gl/xmQnxf for more
   * information and examples of labels. System reserved label keys are prefixed
   * with `aiplatform.googleapis.com/` and are immutable. The following system
   * labels exist for each Dataset: *
   * `aiplatform.googleapis.com/dataset_metadata_schema`: output only. Its value
   * is the metadata_schema's title.
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
   * Output only. Name of the TensorboardExperiment. Format: `projects/{project}
   * /locations/{location}/tensorboards/{tensorboard}/experiments/{experiment}`
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
   * Immutable. Source of the TensorboardExperiment. Example: a custom training
   * job.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. Timestamp when this TensorboardExperiment was last updated.
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
class_alias(GoogleCloudAiplatformV1TensorboardExperiment::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TensorboardExperiment');
