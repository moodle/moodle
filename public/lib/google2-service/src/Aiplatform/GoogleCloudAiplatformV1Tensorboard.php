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

class GoogleCloudAiplatformV1Tensorboard extends \Google\Model
{
  /**
   * Output only. Consumer project Cloud Storage path prefix used to store blob
   * data, which can either be a bucket or directory. Does not end with a '/'.
   *
   * @var string
   */
  public $blobStoragePathPrefix;
  /**
   * Output only. Timestamp when this Tensorboard was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of this Tensorboard.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User provided name of this Tensorboard.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Used to indicate if the TensorBoard instance is the default one. Each
   * project & region can have at most one default TensorBoard instance.
   * Creation of a default TensorBoard instance and updating an existing
   * TensorBoard instance to be default will mark all other TensorBoard
   * instances (if any) as non default.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * The labels with user-defined metadata to organize your Tensorboards. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. No more than 64 user labels
   * can be associated with one Tensorboard (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Name of the Tensorboard. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of Runs stored in this Tensorboard.
   *
   * @var int
   */
  public $runCount;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Timestamp when this Tensorboard was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Consumer project Cloud Storage path prefix used to store blob
   * data, which can either be a bucket or directory. Does not end with a '/'.
   *
   * @param string $blobStoragePathPrefix
   */
  public function setBlobStoragePathPrefix($blobStoragePathPrefix)
  {
    $this->blobStoragePathPrefix = $blobStoragePathPrefix;
  }
  /**
   * @return string
   */
  public function getBlobStoragePathPrefix()
  {
    return $this->blobStoragePathPrefix;
  }
  /**
   * Output only. Timestamp when this Tensorboard was created.
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
   * Description of this Tensorboard.
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
   * Required. User provided name of this Tensorboard.
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
   * Customer-managed encryption key spec for a Tensorboard. If set, this
   * Tensorboard and all sub-resources of this Tensorboard will be secured by
   * this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
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
   * Used to indicate if the TensorBoard instance is the default one. Each
   * project & region can have at most one default TensorBoard instance.
   * Creation of a default TensorBoard instance and updating an existing
   * TensorBoard instance to be default will mark all other TensorBoard
   * instances (if any) as non default.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * The labels with user-defined metadata to organize your Tensorboards. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. No more than 64 user labels
   * can be associated with one Tensorboard (System labels are excluded). See
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
   * Output only. Name of the Tensorboard. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
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
   * Output only. The number of Runs stored in this Tensorboard.
   *
   * @param int $runCount
   */
  public function setRunCount($runCount)
  {
    $this->runCount = $runCount;
  }
  /**
   * @return int
   */
  public function getRunCount()
  {
    return $this->runCount;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Timestamp when this Tensorboard was last updated.
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
class_alias(GoogleCloudAiplatformV1Tensorboard::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Tensorboard');
