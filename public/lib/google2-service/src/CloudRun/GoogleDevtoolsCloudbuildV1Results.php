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

class GoogleDevtoolsCloudbuildV1Results extends \Google\Collection
{
  protected $collection_key = 'pythonPackages';
  /**
   * Path to the artifact manifest for non-container artifacts uploaded to Cloud
   * Storage. Only populated when artifacts are uploaded to Cloud Storage.
   *
   * @var string
   */
  public $artifactManifest;
  protected $artifactTimingType = GoogleDevtoolsCloudbuildV1TimeSpan::class;
  protected $artifactTimingDataType = '';
  /**
   * List of build step digests, in the order corresponding to build step
   * indices.
   *
   * @var string[]
   */
  public $buildStepImages;
  /**
   * List of build step outputs, produced by builder images, in the order
   * corresponding to build step indices. [Cloud
   * Builders](https://cloud.google.com/cloud-build/docs/cloud-builders) can
   * produce this output by writing to `$BUILDER_OUTPUT/output`. Only the first
   * 50KB of data is stored. Note that the `$BUILDER_OUTPUT` variable is read-
   * only and can't be substituted.
   *
   * @var string[]
   */
  public $buildStepOutputs;
  protected $goModulesType = GoogleDevtoolsCloudbuildV1UploadedGoModule::class;
  protected $goModulesDataType = 'array';
  protected $imagesType = GoogleDevtoolsCloudbuildV1BuiltImage::class;
  protected $imagesDataType = 'array';
  protected $mavenArtifactsType = GoogleDevtoolsCloudbuildV1UploadedMavenArtifact::class;
  protected $mavenArtifactsDataType = 'array';
  protected $npmPackagesType = GoogleDevtoolsCloudbuildV1UploadedNpmPackage::class;
  protected $npmPackagesDataType = 'array';
  /**
   * Number of non-container artifacts uploaded to Cloud Storage. Only populated
   * when artifacts are uploaded to Cloud Storage.
   *
   * @var string
   */
  public $numArtifacts;
  protected $pythonPackagesType = GoogleDevtoolsCloudbuildV1UploadedPythonPackage::class;
  protected $pythonPackagesDataType = 'array';

  /**
   * Path to the artifact manifest for non-container artifacts uploaded to Cloud
   * Storage. Only populated when artifacts are uploaded to Cloud Storage.
   *
   * @param string $artifactManifest
   */
  public function setArtifactManifest($artifactManifest)
  {
    $this->artifactManifest = $artifactManifest;
  }
  /**
   * @return string
   */
  public function getArtifactManifest()
  {
    return $this->artifactManifest;
  }
  /**
   * Time to push all non-container artifacts to Cloud Storage.
   *
   * @param GoogleDevtoolsCloudbuildV1TimeSpan $artifactTiming
   */
  public function setArtifactTiming(GoogleDevtoolsCloudbuildV1TimeSpan $artifactTiming)
  {
    $this->artifactTiming = $artifactTiming;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1TimeSpan
   */
  public function getArtifactTiming()
  {
    return $this->artifactTiming;
  }
  /**
   * List of build step digests, in the order corresponding to build step
   * indices.
   *
   * @param string[] $buildStepImages
   */
  public function setBuildStepImages($buildStepImages)
  {
    $this->buildStepImages = $buildStepImages;
  }
  /**
   * @return string[]
   */
  public function getBuildStepImages()
  {
    return $this->buildStepImages;
  }
  /**
   * List of build step outputs, produced by builder images, in the order
   * corresponding to build step indices. [Cloud
   * Builders](https://cloud.google.com/cloud-build/docs/cloud-builders) can
   * produce this output by writing to `$BUILDER_OUTPUT/output`. Only the first
   * 50KB of data is stored. Note that the `$BUILDER_OUTPUT` variable is read-
   * only and can't be substituted.
   *
   * @param string[] $buildStepOutputs
   */
  public function setBuildStepOutputs($buildStepOutputs)
  {
    $this->buildStepOutputs = $buildStepOutputs;
  }
  /**
   * @return string[]
   */
  public function getBuildStepOutputs()
  {
    return $this->buildStepOutputs;
  }
  /**
   * Optional. Go module artifacts uploaded to Artifact Registry at the end of
   * the build.
   *
   * @param GoogleDevtoolsCloudbuildV1UploadedGoModule[] $goModules
   */
  public function setGoModules($goModules)
  {
    $this->goModules = $goModules;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1UploadedGoModule[]
   */
  public function getGoModules()
  {
    return $this->goModules;
  }
  /**
   * Container images that were built as a part of the build.
   *
   * @param GoogleDevtoolsCloudbuildV1BuiltImage[] $images
   */
  public function setImages($images)
  {
    $this->images = $images;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1BuiltImage[]
   */
  public function getImages()
  {
    return $this->images;
  }
  /**
   * Maven artifacts uploaded to Artifact Registry at the end of the build.
   *
   * @param GoogleDevtoolsCloudbuildV1UploadedMavenArtifact[] $mavenArtifacts
   */
  public function setMavenArtifacts($mavenArtifacts)
  {
    $this->mavenArtifacts = $mavenArtifacts;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1UploadedMavenArtifact[]
   */
  public function getMavenArtifacts()
  {
    return $this->mavenArtifacts;
  }
  /**
   * Npm packages uploaded to Artifact Registry at the end of the build.
   *
   * @param GoogleDevtoolsCloudbuildV1UploadedNpmPackage[] $npmPackages
   */
  public function setNpmPackages($npmPackages)
  {
    $this->npmPackages = $npmPackages;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1UploadedNpmPackage[]
   */
  public function getNpmPackages()
  {
    return $this->npmPackages;
  }
  /**
   * Number of non-container artifacts uploaded to Cloud Storage. Only populated
   * when artifacts are uploaded to Cloud Storage.
   *
   * @param string $numArtifacts
   */
  public function setNumArtifacts($numArtifacts)
  {
    $this->numArtifacts = $numArtifacts;
  }
  /**
   * @return string
   */
  public function getNumArtifacts()
  {
    return $this->numArtifacts;
  }
  /**
   * Python artifacts uploaded to Artifact Registry at the end of the build.
   *
   * @param GoogleDevtoolsCloudbuildV1UploadedPythonPackage[] $pythonPackages
   */
  public function setPythonPackages($pythonPackages)
  {
    $this->pythonPackages = $pythonPackages;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1UploadedPythonPackage[]
   */
  public function getPythonPackages()
  {
    return $this->pythonPackages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1Results::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Results');
