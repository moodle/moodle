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

class GoogleDevtoolsCloudbuildV1Artifacts extends \Google\Collection
{
  protected $collection_key = 'pythonPackages';
  protected $goModulesType = GoogleDevtoolsCloudbuildV1GoModule::class;
  protected $goModulesDataType = 'array';
  /**
   * A list of images to be pushed upon the successful completion of all build
   * steps. The images will be pushed using the builder service account's
   * credentials. The digests of the pushed images will be stored in the Build
   * resource's results field. If any of the images fail to be pushed, the build
   * is marked FAILURE.
   *
   * @var string[]
   */
  public $images;
  protected $mavenArtifactsType = GoogleDevtoolsCloudbuildV1MavenArtifact::class;
  protected $mavenArtifactsDataType = 'array';
  protected $npmPackagesType = GoogleDevtoolsCloudbuildV1NpmPackage::class;
  protected $npmPackagesDataType = 'array';
  protected $objectsType = GoogleDevtoolsCloudbuildV1ArtifactObjects::class;
  protected $objectsDataType = '';
  protected $pythonPackagesType = GoogleDevtoolsCloudbuildV1PythonPackage::class;
  protected $pythonPackagesDataType = 'array';

  /**
   * Optional. A list of Go modules to be uploaded to Artifact Registry upon
   * successful completion of all build steps. If any objects fail to be pushed,
   * the build is marked FAILURE.
   *
   * @param GoogleDevtoolsCloudbuildV1GoModule[] $goModules
   */
  public function setGoModules($goModules)
  {
    $this->goModules = $goModules;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1GoModule[]
   */
  public function getGoModules()
  {
    return $this->goModules;
  }
  /**
   * A list of images to be pushed upon the successful completion of all build
   * steps. The images will be pushed using the builder service account's
   * credentials. The digests of the pushed images will be stored in the Build
   * resource's results field. If any of the images fail to be pushed, the build
   * is marked FAILURE.
   *
   * @param string[] $images
   */
  public function setImages($images)
  {
    $this->images = $images;
  }
  /**
   * @return string[]
   */
  public function getImages()
  {
    return $this->images;
  }
  /**
   * A list of Maven artifacts to be uploaded to Artifact Registry upon
   * successful completion of all build steps. Artifacts in the workspace
   * matching specified paths globs will be uploaded to the specified Artifact
   * Registry repository using the builder service account's credentials. If any
   * artifacts fail to be pushed, the build is marked FAILURE.
   *
   * @param GoogleDevtoolsCloudbuildV1MavenArtifact[] $mavenArtifacts
   */
  public function setMavenArtifacts($mavenArtifacts)
  {
    $this->mavenArtifacts = $mavenArtifacts;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1MavenArtifact[]
   */
  public function getMavenArtifacts()
  {
    return $this->mavenArtifacts;
  }
  /**
   * A list of npm packages to be uploaded to Artifact Registry upon successful
   * completion of all build steps. Npm packages in the specified paths will be
   * uploaded to the specified Artifact Registry repository using the builder
   * service account's credentials. If any packages fail to be pushed, the build
   * is marked FAILURE.
   *
   * @param GoogleDevtoolsCloudbuildV1NpmPackage[] $npmPackages
   */
  public function setNpmPackages($npmPackages)
  {
    $this->npmPackages = $npmPackages;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1NpmPackage[]
   */
  public function getNpmPackages()
  {
    return $this->npmPackages;
  }
  /**
   * A list of objects to be uploaded to Cloud Storage upon successful
   * completion of all build steps. Files in the workspace matching specified
   * paths globs will be uploaded to the specified Cloud Storage location using
   * the builder service account's credentials. The location and generation of
   * the uploaded objects will be stored in the Build resource's results field.
   * If any objects fail to be pushed, the build is marked FAILURE.
   *
   * @param GoogleDevtoolsCloudbuildV1ArtifactObjects $objects
   */
  public function setObjects(GoogleDevtoolsCloudbuildV1ArtifactObjects $objects)
  {
    $this->objects = $objects;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1ArtifactObjects
   */
  public function getObjects()
  {
    return $this->objects;
  }
  /**
   * A list of Python packages to be uploaded to Artifact Registry upon
   * successful completion of all build steps. The build service account
   * credentials will be used to perform the upload. If any objects fail to be
   * pushed, the build is marked FAILURE.
   *
   * @param GoogleDevtoolsCloudbuildV1PythonPackage[] $pythonPackages
   */
  public function setPythonPackages($pythonPackages)
  {
    $this->pythonPackages = $pythonPackages;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1PythonPackage[]
   */
  public function getPythonPackages()
  {
    return $this->pythonPackages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1Artifacts::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Artifacts');
