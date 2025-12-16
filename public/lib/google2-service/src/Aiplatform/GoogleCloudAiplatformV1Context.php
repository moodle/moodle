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

class GoogleCloudAiplatformV1Context extends \Google\Collection
{
  protected $collection_key = 'parentContexts';
  /**
   * Output only. Timestamp when this Context was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the Context
   *
   * @var string
   */
  public $description;
  /**
   * User provided display name of the Context. May be up to 128 Unicode
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
   * The labels with user-defined metadata to organize your Contexts. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Context (System labels are excluded).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Properties of the Context. Top level metadata keys' heading and trailing
   * spaces will be trimmed. The size of this field should not exceed 200KB.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Immutable. The resource name of the Context.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A list of resource names of Contexts that are parents of this
   * Context. A Context may have at most 10 parent_contexts.
   *
   * @var string[]
   */
  public $parentContexts;
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
   * Output only. Timestamp when this Context was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this Context was created.
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
   * Description of the Context
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
   * User provided display name of the Context. May be up to 128 Unicode
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
   * The labels with user-defined metadata to organize your Contexts. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. No more than 64 user labels can be
   * associated with one Context (System labels are excluded).
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
   * Properties of the Context. Top level metadata keys' heading and trailing
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
   * Immutable. The resource name of the Context.
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
   * Output only. A list of resource names of Contexts that are parents of this
   * Context. A Context may have at most 10 parent_contexts.
   *
   * @param string[] $parentContexts
   */
  public function setParentContexts($parentContexts)
  {
    $this->parentContexts = $parentContexts;
  }
  /**
   * @return string[]
   */
  public function getParentContexts()
  {
    return $this->parentContexts;
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
   * Output only. Timestamp when this Context was last updated.
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
class_alias(GoogleCloudAiplatformV1Context::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Context');
