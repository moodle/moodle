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

namespace Google\Service\NetworkServices;

class WasmPluginVersion extends \Google\Model
{
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A human-readable description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. This field holds the digest (usually checksum) value for the
   * plugin image. The value is calculated based on the `image_uri` field. If
   * the `image_uri` field refers to a container image, the digest value is
   * obtained from the container image. If the `image_uri` field refers to a
   * generic artifact, the digest value is calculated based on the contents of
   * the file.
   *
   * @var string
   */
  public $imageDigest;
  /**
   * Optional. URI of the image containing the Wasm module, stored in Artifact
   * Registry. The URI can refer to one of the following repository formats: *
   * Container images: the `image_uri` must point to a container that contains a
   * single file with the name `plugin.wasm`. When a new `WasmPluginVersion`
   * resource is created, the digest of the image is saved in the `image_digest`
   * field. When pulling a container image from Artifact Registry, the digest
   * value is used instead of an image tag. * Generic artifacts: the `image_uri`
   * must be in this format:
   * `projects/{project}/locations/{location}/repositories/{repository}/
   * genericArtifacts/{package}:{version}`. The specified package and version
   * must contain a file with the name `plugin.wasm`. When a new
   * `WasmPluginVersion` resource is created, the checksum of the contents of
   * the file is saved in the `image_digest` field.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Optional. Set of labels associated with the `WasmPluginVersion` resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the `WasmPluginVersion` resource in the following
   * format: `projects/{project}/locations/{location}/wasmPlugins/{wasm_plugin}/
   * versions/{wasm_plugin_version}`.
   *
   * @var string
   */
  public $name;
  /**
   * Configuration for the plugin. The configuration is provided to the plugin
   * at runtime through the `ON_CONFIGURE` callback. When a new
   * `WasmPluginVersion` resource is created, the digest of the contents is
   * saved in the `plugin_config_digest` field.
   *
   * @var string
   */
  public $pluginConfigData;
  /**
   * Output only. This field holds the digest (usually checksum) value for the
   * plugin configuration. The value is calculated based on the contents of
   * `plugin_config_data` field or the image defined by the `plugin_config_uri`
   * field.
   *
   * @var string
   */
  public $pluginConfigDigest;
  /**
   * URI of the plugin configuration stored in the Artifact Registry. The
   * configuration is provided to the plugin at runtime through the
   * `ON_CONFIGURE` callback. The URI can refer to one of the following
   * repository formats: * Container images: the `plugin_config_uri` must point
   * to a container that contains a single file with the name `plugin.config`.
   * When a new `WasmPluginVersion` resource is created, the digest of the image
   * is saved in the `plugin_config_digest` field. When pulling a container
   * image from Artifact Registry, the digest value is used instead of an image
   * tag. * Generic artifacts: the `plugin_config_uri` must be in this format:
   * `projects/{project}/locations/{location}/repositories/{repository}/
   * genericArtifacts/{package}:{version}`. The specified package and version
   * must contain a file with the name `plugin.config`. When a new
   * `WasmPluginVersion` resource is created, the checksum of the contents of
   * the file is saved in the `plugin_config_digest` field.
   *
   * @var string
   */
  public $pluginConfigUri;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
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
   * Optional. A human-readable description of the resource.
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
   * Output only. This field holds the digest (usually checksum) value for the
   * plugin image. The value is calculated based on the `image_uri` field. If
   * the `image_uri` field refers to a container image, the digest value is
   * obtained from the container image. If the `image_uri` field refers to a
   * generic artifact, the digest value is calculated based on the contents of
   * the file.
   *
   * @param string $imageDigest
   */
  public function setImageDigest($imageDigest)
  {
    $this->imageDigest = $imageDigest;
  }
  /**
   * @return string
   */
  public function getImageDigest()
  {
    return $this->imageDigest;
  }
  /**
   * Optional. URI of the image containing the Wasm module, stored in Artifact
   * Registry. The URI can refer to one of the following repository formats: *
   * Container images: the `image_uri` must point to a container that contains a
   * single file with the name `plugin.wasm`. When a new `WasmPluginVersion`
   * resource is created, the digest of the image is saved in the `image_digest`
   * field. When pulling a container image from Artifact Registry, the digest
   * value is used instead of an image tag. * Generic artifacts: the `image_uri`
   * must be in this format:
   * `projects/{project}/locations/{location}/repositories/{repository}/
   * genericArtifacts/{package}:{version}`. The specified package and version
   * must contain a file with the name `plugin.wasm`. When a new
   * `WasmPluginVersion` resource is created, the checksum of the contents of
   * the file is saved in the `image_digest` field.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Optional. Set of labels associated with the `WasmPluginVersion` resource.
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
   * Identifier. Name of the `WasmPluginVersion` resource in the following
   * format: `projects/{project}/locations/{location}/wasmPlugins/{wasm_plugin}/
   * versions/{wasm_plugin_version}`.
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
   * Configuration for the plugin. The configuration is provided to the plugin
   * at runtime through the `ON_CONFIGURE` callback. When a new
   * `WasmPluginVersion` resource is created, the digest of the contents is
   * saved in the `plugin_config_digest` field.
   *
   * @param string $pluginConfigData
   */
  public function setPluginConfigData($pluginConfigData)
  {
    $this->pluginConfigData = $pluginConfigData;
  }
  /**
   * @return string
   */
  public function getPluginConfigData()
  {
    return $this->pluginConfigData;
  }
  /**
   * Output only. This field holds the digest (usually checksum) value for the
   * plugin configuration. The value is calculated based on the contents of
   * `plugin_config_data` field or the image defined by the `plugin_config_uri`
   * field.
   *
   * @param string $pluginConfigDigest
   */
  public function setPluginConfigDigest($pluginConfigDigest)
  {
    $this->pluginConfigDigest = $pluginConfigDigest;
  }
  /**
   * @return string
   */
  public function getPluginConfigDigest()
  {
    return $this->pluginConfigDigest;
  }
  /**
   * URI of the plugin configuration stored in the Artifact Registry. The
   * configuration is provided to the plugin at runtime through the
   * `ON_CONFIGURE` callback. The URI can refer to one of the following
   * repository formats: * Container images: the `plugin_config_uri` must point
   * to a container that contains a single file with the name `plugin.config`.
   * When a new `WasmPluginVersion` resource is created, the digest of the image
   * is saved in the `plugin_config_digest` field. When pulling a container
   * image from Artifact Registry, the digest value is used instead of an image
   * tag. * Generic artifacts: the `plugin_config_uri` must be in this format:
   * `projects/{project}/locations/{location}/repositories/{repository}/
   * genericArtifacts/{package}:{version}`. The specified package and version
   * must contain a file with the name `plugin.config`. When a new
   * `WasmPluginVersion` resource is created, the checksum of the contents of
   * the file is saved in the `plugin_config_digest` field.
   *
   * @param string $pluginConfigUri
   */
  public function setPluginConfigUri($pluginConfigUri)
  {
    $this->pluginConfigUri = $pluginConfigUri;
  }
  /**
   * @return string
   */
  public function getPluginConfigUri()
  {
    return $this->pluginConfigUri;
  }
  /**
   * Output only. The timestamp when the resource was updated.
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
class_alias(WasmPluginVersion::class, 'Google_Service_NetworkServices_WasmPluginVersion');
