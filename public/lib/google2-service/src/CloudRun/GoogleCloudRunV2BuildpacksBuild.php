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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2BuildpacksBuild extends \Google\Model
{
  /**
   * Optional. The base image to use for the build.
   *
   * @var string
   */
  public $baseImage;
  /**
   * Optional. cache_image_uri is the GCR/AR URL where the cache image will be
   * stored. cache_image_uri is optional and omitting it will disable caching.
   * This URL must be stable across builds. It is used to derive a build-
   * specific temporary URL by substituting the tag with the build ID. The build
   * will clean up the temporary image on a best-effort basis.
   *
   * @var string
   */
  public $cacheImageUri;
  /**
   * Optional. Whether or not the application container will be enrolled in
   * automatic base image updates. When true, the application will be built on a
   * scratch base image, so the base layers can be appended at run time.
   *
   * @var bool
   */
  public $enableAutomaticUpdates;
  /**
   * Optional. User-provided build-time environment variables.
   *
   * @var string[]
   */
  public $environmentVariables;
  /**
   * Optional. Name of the function target if the source is a function source.
   * Required for function builds.
   *
   * @var string
   */
  public $functionTarget;
  /**
   * Optional. project_descriptor stores the path to the project descriptor
   * file. When empty, it means that there is no project descriptor file in the
   * source.
   *
   * @var string
   */
  public $projectDescriptor;
  /**
   * The runtime name, e.g. 'go113'. Leave blank for generic builds.
   *
   * @deprecated
   * @var string
   */
  public $runtime;

  /**
   * Optional. The base image to use for the build.
   *
   * @param string $baseImage
   */
  public function setBaseImage($baseImage)
  {
    $this->baseImage = $baseImage;
  }
  /**
   * @return string
   */
  public function getBaseImage()
  {
    return $this->baseImage;
  }
  /**
   * Optional. cache_image_uri is the GCR/AR URL where the cache image will be
   * stored. cache_image_uri is optional and omitting it will disable caching.
   * This URL must be stable across builds. It is used to derive a build-
   * specific temporary URL by substituting the tag with the build ID. The build
   * will clean up the temporary image on a best-effort basis.
   *
   * @param string $cacheImageUri
   */
  public function setCacheImageUri($cacheImageUri)
  {
    $this->cacheImageUri = $cacheImageUri;
  }
  /**
   * @return string
   */
  public function getCacheImageUri()
  {
    return $this->cacheImageUri;
  }
  /**
   * Optional. Whether or not the application container will be enrolled in
   * automatic base image updates. When true, the application will be built on a
   * scratch base image, so the base layers can be appended at run time.
   *
   * @param bool $enableAutomaticUpdates
   */
  public function setEnableAutomaticUpdates($enableAutomaticUpdates)
  {
    $this->enableAutomaticUpdates = $enableAutomaticUpdates;
  }
  /**
   * @return bool
   */
  public function getEnableAutomaticUpdates()
  {
    return $this->enableAutomaticUpdates;
  }
  /**
   * Optional. User-provided build-time environment variables.
   *
   * @param string[] $environmentVariables
   */
  public function setEnvironmentVariables($environmentVariables)
  {
    $this->environmentVariables = $environmentVariables;
  }
  /**
   * @return string[]
   */
  public function getEnvironmentVariables()
  {
    return $this->environmentVariables;
  }
  /**
   * Optional. Name of the function target if the source is a function source.
   * Required for function builds.
   *
   * @param string $functionTarget
   */
  public function setFunctionTarget($functionTarget)
  {
    $this->functionTarget = $functionTarget;
  }
  /**
   * @return string
   */
  public function getFunctionTarget()
  {
    return $this->functionTarget;
  }
  /**
   * Optional. project_descriptor stores the path to the project descriptor
   * file. When empty, it means that there is no project descriptor file in the
   * source.
   *
   * @param string $projectDescriptor
   */
  public function setProjectDescriptor($projectDescriptor)
  {
    $this->projectDescriptor = $projectDescriptor;
  }
  /**
   * @return string
   */
  public function getProjectDescriptor()
  {
    return $this->projectDescriptor;
  }
  /**
   * The runtime name, e.g. 'go113'. Leave blank for generic builds.
   *
   * @deprecated
   * @param string $runtime
   */
  public function setRuntime($runtime)
  {
    $this->runtime = $runtime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRuntime()
  {
    return $this->runtime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2BuildpacksBuild::class, 'Google_Service_CloudRun_GoogleCloudRunV2BuildpacksBuild');
