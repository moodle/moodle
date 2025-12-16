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

class GoogleCloudAiplatformV1Artifact extends \Google\Model
{
  /**
   * Unspecified state for the Artifact.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A state used by systems like Vertex AI Pipelines to indicate that the
   * underlying data item represented by this Artifact is being created.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * A state indicating that the Artifact should exist, unless something
   * external to the system deletes it.
   */
  public const STATE_LIVE = 'LIVE';
  /**
   * Output only. Timestamp when this Artifact was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the Artifact
   *
   * @var string
   */
  public $description;
  /**
   * User provided display name of the Artifact. May be up to 128 Unicode
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * An eTag used to perform consistent read-modify-write updates. If not set, a
   * blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * The labels with user-defined metadata to organize your Artifacts. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. No more than 64 user labels
   * can be associated with one Artifact (System labels are excluded).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Properties of the Artifact. Top level metadata keys' heading and trailing
   * spaces will be trimmed. The size of this field should not exceed 200KB.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Output only. The resource name of the Artifact.
   *
   * @var string
   */
  public $name;
  /**
   * The title of the schema describing the metadata. Schema title and version
   * is expected to be registered in earlier Create Schema calls. And both are
   * used together as unique identifiers to identify schemas within the local
   * metadata store.
   *
   * @var string
   */
  public $schemaTitle;
  /**
   * The version of the schema in schema_name to use. Schema title and version
   * is expected to be registered in earlier Create Schema calls. And both are
   * used together as unique identifiers to identify schemas within the local
   * metadata store.
   *
   * @var string
   */
  public $schemaVersion;
  /**
   * The state of this Artifact. This is a property of the Artifact, and does
   * not imply or capture any ongoing process. This property is managed by
   * clients (such as Vertex AI Pipelines), and the system does not prescribe or
   * check the validity of state transitions.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when this Artifact was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The uniform resource identifier of the artifact file. May be empty if there
   * is no actual artifact file.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. Timestamp when this Artifact was created.
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
   * Description of the Artifact
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
   * User provided display name of the Artifact. May be up to 128 Unicode
   * characters.
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
   * An eTag used to perform consistent read-modify-write updates. If not set, a
   * blind "overwrite" update happens.
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
   * The labels with user-defined metadata to organize your Artifacts. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. No more than 64 user labels
   * can be associated with one Artifact (System labels are excluded).
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
   * Properties of the Artifact. Top level metadata keys' heading and trailing
   * spaces will be trimmed. The size of this field should not exceed 200KB.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. The resource name of the Artifact.
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
   * The title of the schema describing the metadata. Schema title and version
   * is expected to be registered in earlier Create Schema calls. And both are
   * used together as unique identifiers to identify schemas within the local
   * metadata store.
   *
   * @param string $schemaTitle
   */
  public function setSchemaTitle($schemaTitle)
  {
    $this->schemaTitle = $schemaTitle;
  }
  /**
   * @return string
   */
  public function getSchemaTitle()
  {
    return $this->schemaTitle;
  }
  /**
   * The version of the schema in schema_name to use. Schema title and version
   * is expected to be registered in earlier Create Schema calls. And both are
   * used together as unique identifiers to identify schemas within the local
   * metadata store.
   *
   * @param string $schemaVersion
   */
  public function setSchemaVersion($schemaVersion)
  {
    $this->schemaVersion = $schemaVersion;
  }
  /**
   * @return string
   */
  public function getSchemaVersion()
  {
    return $this->schemaVersion;
  }
  /**
   * The state of this Artifact. This is a property of the Artifact, and does
   * not imply or capture any ongoing process. This property is managed by
   * clients (such as Vertex AI Pipelines), and the system does not prescribe or
   * check the validity of state transitions.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, LIVE
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
  /**
   * Output only. Timestamp when this Artifact was last updated.
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
   * The uniform resource identifier of the artifact file. May be empty if there
   * is no actual artifact file.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Artifact::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Artifact');
