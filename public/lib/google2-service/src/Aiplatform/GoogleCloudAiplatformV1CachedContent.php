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

class GoogleCloudAiplatformV1CachedContent extends \Google\Collection
{
  protected $collection_key = 'tools';
  protected $contentsType = GoogleCloudAiplatformV1Content::class;
  protected $contentsDataType = 'array';
  /**
   * Output only. Creation time of the cache entry.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Immutable. The user-generated meaningful display name of the
   * cached content.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Timestamp of when this resource is considered expired. This is *always*
   * provided on output, regardless of what was sent on input.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Immutable. The name of the `Model` to use for cached content. Currently,
   * only the published Gemini base models are supported, in form of
   * projects/{PROJECT}/locations/{LOCATION}/publishers/google/models/{MODEL}
   *
   * @var string
   */
  public $model;
  /**
   * Immutable. Identifier. The server-generated resource name of the cached
   * content Format:
   * projects/{project}/locations/{location}/cachedContents/{cached_content}
   *
   * @var string
   */
  public $name;
  protected $systemInstructionType = GoogleCloudAiplatformV1Content::class;
  protected $systemInstructionDataType = '';
  protected $toolConfigType = GoogleCloudAiplatformV1ToolConfig::class;
  protected $toolConfigDataType = '';
  protected $toolsType = GoogleCloudAiplatformV1Tool::class;
  protected $toolsDataType = 'array';
  /**
   * Input only. The TTL for this resource. The expiration time is computed: now
   * + TTL.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. When the cache entry was last updated in UTC time.
   *
   * @var string
   */
  public $updateTime;
  protected $usageMetadataType = GoogleCloudAiplatformV1CachedContentUsageMetadata::class;
  protected $usageMetadataDataType = '';

  /**
   * Optional. Input only. Immutable. The content to cache
   *
   * @param GoogleCloudAiplatformV1Content[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. Creation time of the cache entry.
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
   * Optional. Immutable. The user-generated meaningful display name of the
   * cached content.
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
   * Input only. Immutable. Customer-managed encryption key spec for a
   * `CachedContent`. If set, this `CachedContent` and all its sub-resources
   * will be secured by this key.
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
   * Timestamp of when this resource is considered expired. This is *always*
   * provided on output, regardless of what was sent on input.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Immutable. The name of the `Model` to use for cached content. Currently,
   * only the published Gemini base models are supported, in form of
   * projects/{PROJECT}/locations/{LOCATION}/publishers/google/models/{MODEL}
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Immutable. Identifier. The server-generated resource name of the cached
   * content Format:
   * projects/{project}/locations/{location}/cachedContents/{cached_content}
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
   * Optional. Input only. Immutable. Developer set system instruction.
   * Currently, text only
   *
   * @param GoogleCloudAiplatformV1Content $systemInstruction
   */
  public function setSystemInstruction(GoogleCloudAiplatformV1Content $systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
  /**
   * Optional. Input only. Immutable. Tool config. This config is shared for all
   * tools
   *
   * @param GoogleCloudAiplatformV1ToolConfig $toolConfig
   */
  public function setToolConfig(GoogleCloudAiplatformV1ToolConfig $toolConfig)
  {
    $this->toolConfig = $toolConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolConfig
   */
  public function getToolConfig()
  {
    return $this->toolConfig;
  }
  /**
   * Optional. Input only. Immutable. A list of `Tools` the model may use to
   * generate the next response
   *
   * @param GoogleCloudAiplatformV1Tool[] $tools
   */
  public function setTools($tools)
  {
    $this->tools = $tools;
  }
  /**
   * @return GoogleCloudAiplatformV1Tool[]
   */
  public function getTools()
  {
    return $this->tools;
  }
  /**
   * Input only. The TTL for this resource. The expiration time is computed: now
   * + TTL.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. When the cache entry was last updated in UTC time.
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
   * Output only. Metadata on the usage of the cached content.
   *
   * @param GoogleCloudAiplatformV1CachedContentUsageMetadata $usageMetadata
   */
  public function setUsageMetadata(GoogleCloudAiplatformV1CachedContentUsageMetadata $usageMetadata)
  {
    $this->usageMetadata = $usageMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1CachedContentUsageMetadata
   */
  public function getUsageMetadata()
  {
    return $this->usageMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CachedContent::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CachedContent');
