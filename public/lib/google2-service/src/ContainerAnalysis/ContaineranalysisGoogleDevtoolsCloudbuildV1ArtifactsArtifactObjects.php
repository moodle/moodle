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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1ArtifactsArtifactObjects extends \Google\Collection
{
  protected $collection_key = 'paths';
  /**
   * Cloud Storage bucket and optional object path, in the form
   * "gs://bucket/path/to/somewhere/". (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)). Files in the workspace matching any path pattern
   * will be uploaded to Cloud Storage with this location as a prefix.
   *
   * @var string
   */
  public $location;
  /**
   * Path globs used to match files in the build's workspace.
   *
   * @var string[]
   */
  public $paths;
  protected $timingType = ContaineranalysisGoogleDevtoolsCloudbuildV1TimeSpan::class;
  protected $timingDataType = '';

  /**
   * Cloud Storage bucket and optional object path, in the form
   * "gs://bucket/path/to/somewhere/". (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)). Files in the workspace matching any path pattern
   * will be uploaded to Cloud Storage with this location as a prefix.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Path globs used to match files in the build's workspace.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
  /**
   * Output only. Stores timing information for pushing all artifact objects.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1TimeSpan $timing
   */
  public function setTiming(ContaineranalysisGoogleDevtoolsCloudbuildV1TimeSpan $timing)
  {
    $this->timing = $timing;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1TimeSpan
   */
  public function getTiming()
  {
    return $this->timing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1ArtifactsArtifactObjects::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1ArtifactsArtifactObjects');
