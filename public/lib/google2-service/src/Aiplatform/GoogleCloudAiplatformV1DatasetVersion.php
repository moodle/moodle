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

class GoogleCloudAiplatformV1DatasetVersion extends \Google\Model
{
  /**
   * Output only. Name of the associated BigQuery dataset.
   *
   * @var string
   */
  public $bigQueryDatasetName;
  /**
   * Output only. Timestamp when this DatasetVersion was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The user-defined name of the DatasetVersion. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Required. Output only. Additional information about the DatasetVersion.
   *
   * @var array
   */
  public $metadata;
  /**
   * Output only. Reference to the public base model last used by the dataset
   * version. Only set for prompt dataset versions.
   *
   * @var string
   */
  public $modelReference;
  /**
   * Output only. Identifier. The resource name of the DatasetVersion. Format: `
   * projects/{project}/locations/{location}/datasets/{dataset}/datasetVersions/
   * {dataset_version}`
   *
   * @var string
   */
  public $name;
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
   * Output only. Timestamp when this DatasetVersion was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Name of the associated BigQuery dataset.
   *
   * @param string $bigQueryDatasetName
   */
  public function setBigQueryDatasetName($bigQueryDatasetName)
  {
    $this->bigQueryDatasetName = $bigQueryDatasetName;
  }
  /**
   * @return string
   */
  public function getBigQueryDatasetName()
  {
    return $this->bigQueryDatasetName;
  }
  /**
   * Output only. Timestamp when this DatasetVersion was created.
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
   * The user-defined name of the DatasetVersion. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Required. Output only. Additional information about the DatasetVersion.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. Reference to the public base model last used by the dataset
   * version. Only set for prompt dataset versions.
   *
   * @param string $modelReference
   */
  public function setModelReference($modelReference)
  {
    $this->modelReference = $modelReference;
  }
  /**
   * @return string
   */
  public function getModelReference()
  {
    return $this->modelReference;
  }
  /**
   * Output only. Identifier. The resource name of the DatasetVersion. Format: `
   * projects/{project}/locations/{location}/datasets/{dataset}/datasetVersions/
   * {dataset_version}`
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
   * Output only. Timestamp when this DatasetVersion was last updated.
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
class_alias(GoogleCloudAiplatformV1DatasetVersion::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DatasetVersion');
