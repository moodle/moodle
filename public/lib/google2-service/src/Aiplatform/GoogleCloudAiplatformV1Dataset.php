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

class GoogleCloudAiplatformV1Dataset extends \Google\Collection
{
  protected $collection_key = 'savedQueries';
  /**
   * Output only. Timestamp when this Dataset was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The number of DataItems in this Dataset. Only apply for non-
   * structured Dataset.
   *
   * @var string
   */
  public $dataItemCount;
  /**
   * The description of the Dataset.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The user-defined name of the Dataset. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * The labels with user-defined metadata to organize your Datasets. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Dataset (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable. Following system labels exist for each Dataset: *
   * "aiplatform.googleapis.com/dataset_metadata_schema": output only, its value
   * is the metadata_schema's title.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Additional information about the Dataset.
   *
   * @var array
   */
  public $metadata;
  /**
   * Output only. The resource name of the Artifact that was created in
   * MetadataStore when creating the Dataset. The Artifact resource name pattern
   * is `projects/{project}/locations/{location}/metadataStores/{metadata_store}
   * /artifacts/{artifact}`.
   *
   * @var string
   */
  public $metadataArtifact;
  /**
   * Required. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Dataset. The schema is defined as an
   * OpenAPI 3.0.2 Schema Object. The schema files that can be used here are
   * found in gs://google-cloud-aiplatform/schema/dataset/metadata/.
   *
   * @var string
   */
  public $metadataSchemaUri;
  /**
   * Optional. Reference to the public base model last used by the dataset. Only
   * set for prompt datasets.
   *
   * @var string
   */
  public $modelReference;
  /**
   * Output only. Identifier. The resource name of the Dataset. Format:
   * `projects/{project}/locations/{location}/datasets/{dataset}`
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
  protected $savedQueriesType = GoogleCloudAiplatformV1SavedQuery::class;
  protected $savedQueriesDataType = 'array';
  /**
   * Output only. Timestamp when this Dataset was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this Dataset was created.
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
   * Output only. The number of DataItems in this Dataset. Only apply for non-
   * structured Dataset.
   *
   * @param string $dataItemCount
   */
  public function setDataItemCount($dataItemCount)
  {
    $this->dataItemCount = $dataItemCount;
  }
  /**
   * @return string
   */
  public function getDataItemCount()
  {
    return $this->dataItemCount;
  }
  /**
   * The description of the Dataset.
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
   * Required. The user-defined name of the Dataset. The name can be up to 128
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
   * Customer-managed encryption key spec for a Dataset. If set, this Dataset
   * and all sub-resources of this Dataset will be secured by this key.
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
   * The labels with user-defined metadata to organize your Datasets. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Dataset (System labels are excluded). See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable. Following system labels exist for each Dataset: *
   * "aiplatform.googleapis.com/dataset_metadata_schema": output only, its value
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
   * Required. Additional information about the Dataset.
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
   * Output only. The resource name of the Artifact that was created in
   * MetadataStore when creating the Dataset. The Artifact resource name pattern
   * is `projects/{project}/locations/{location}/metadataStores/{metadata_store}
   * /artifacts/{artifact}`.
   *
   * @param string $metadataArtifact
   */
  public function setMetadataArtifact($metadataArtifact)
  {
    $this->metadataArtifact = $metadataArtifact;
  }
  /**
   * @return string
   */
  public function getMetadataArtifact()
  {
    return $this->metadataArtifact;
  }
  /**
   * Required. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Dataset. The schema is defined as an
   * OpenAPI 3.0.2 Schema Object. The schema files that can be used here are
   * found in gs://google-cloud-aiplatform/schema/dataset/metadata/.
   *
   * @param string $metadataSchemaUri
   */
  public function setMetadataSchemaUri($metadataSchemaUri)
  {
    $this->metadataSchemaUri = $metadataSchemaUri;
  }
  /**
   * @return string
   */
  public function getMetadataSchemaUri()
  {
    return $this->metadataSchemaUri;
  }
  /**
   * Optional. Reference to the public base model last used by the dataset. Only
   * set for prompt datasets.
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
   * Output only. Identifier. The resource name of the Dataset. Format:
   * `projects/{project}/locations/{location}/datasets/{dataset}`
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
   * All SavedQueries belong to the Dataset will be returned in List/Get Dataset
   * response. The annotation_specs field will not be populated except for UI
   * cases which will only use annotation_spec_count. In CreateDataset request,
   * a SavedQuery is created together if this field is set, up to one SavedQuery
   * can be set in CreateDatasetRequest. The SavedQuery should not contain any
   * AnnotationSpec.
   *
   * @param GoogleCloudAiplatformV1SavedQuery[] $savedQueries
   */
  public function setSavedQueries($savedQueries)
  {
    $this->savedQueries = $savedQueries;
  }
  /**
   * @return GoogleCloudAiplatformV1SavedQuery[]
   */
  public function getSavedQueries()
  {
    return $this->savedQueries;
  }
  /**
   * Output only. Timestamp when this Dataset was last updated.
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
class_alias(GoogleCloudAiplatformV1Dataset::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Dataset');
