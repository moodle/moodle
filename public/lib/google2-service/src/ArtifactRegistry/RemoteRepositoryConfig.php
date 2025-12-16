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

namespace Google\Service\ArtifactRegistry;

class RemoteRepositoryConfig extends \Google\Model
{
  protected $aptRepositoryType = AptRepository::class;
  protected $aptRepositoryDataType = '';
  protected $commonRepositoryType = CommonRemoteRepository::class;
  protected $commonRepositoryDataType = '';
  /**
   * The description of the remote source.
   *
   * @var string
   */
  public $description;
  /**
   * Input only. A create/update remote repo option to avoid making a HEAD/GET
   * request to validate a remote repo and any supplied upstream credentials.
   *
   * @var bool
   */
  public $disableUpstreamValidation;
  protected $dockerRepositoryType = DockerRepository::class;
  protected $dockerRepositoryDataType = '';
  protected $mavenRepositoryType = MavenRepository::class;
  protected $mavenRepositoryDataType = '';
  protected $npmRepositoryType = NpmRepository::class;
  protected $npmRepositoryDataType = '';
  protected $pythonRepositoryType = PythonRepository::class;
  protected $pythonRepositoryDataType = '';
  protected $upstreamCredentialsType = UpstreamCredentials::class;
  protected $upstreamCredentialsDataType = '';
  protected $yumRepositoryType = YumRepository::class;
  protected $yumRepositoryDataType = '';

  /**
   * Specific settings for an Apt remote repository.
   *
   * @param AptRepository $aptRepository
   */
  public function setAptRepository(AptRepository $aptRepository)
  {
    $this->aptRepository = $aptRepository;
  }
  /**
   * @return AptRepository
   */
  public function getAptRepository()
  {
    return $this->aptRepository;
  }
  /**
   * Common remote repository settings. Used as the remote repository upstream
   * URL.
   *
   * @param CommonRemoteRepository $commonRepository
   */
  public function setCommonRepository(CommonRemoteRepository $commonRepository)
  {
    $this->commonRepository = $commonRepository;
  }
  /**
   * @return CommonRemoteRepository
   */
  public function getCommonRepository()
  {
    return $this->commonRepository;
  }
  /**
   * The description of the remote source.
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
   * Input only. A create/update remote repo option to avoid making a HEAD/GET
   * request to validate a remote repo and any supplied upstream credentials.
   *
   * @param bool $disableUpstreamValidation
   */
  public function setDisableUpstreamValidation($disableUpstreamValidation)
  {
    $this->disableUpstreamValidation = $disableUpstreamValidation;
  }
  /**
   * @return bool
   */
  public function getDisableUpstreamValidation()
  {
    return $this->disableUpstreamValidation;
  }
  /**
   * Specific settings for a Docker remote repository.
   *
   * @param DockerRepository $dockerRepository
   */
  public function setDockerRepository(DockerRepository $dockerRepository)
  {
    $this->dockerRepository = $dockerRepository;
  }
  /**
   * @return DockerRepository
   */
  public function getDockerRepository()
  {
    return $this->dockerRepository;
  }
  /**
   * Specific settings for a Maven remote repository.
   *
   * @param MavenRepository $mavenRepository
   */
  public function setMavenRepository(MavenRepository $mavenRepository)
  {
    $this->mavenRepository = $mavenRepository;
  }
  /**
   * @return MavenRepository
   */
  public function getMavenRepository()
  {
    return $this->mavenRepository;
  }
  /**
   * Specific settings for an Npm remote repository.
   *
   * @param NpmRepository $npmRepository
   */
  public function setNpmRepository(NpmRepository $npmRepository)
  {
    $this->npmRepository = $npmRepository;
  }
  /**
   * @return NpmRepository
   */
  public function getNpmRepository()
  {
    return $this->npmRepository;
  }
  /**
   * Specific settings for a Python remote repository.
   *
   * @param PythonRepository $pythonRepository
   */
  public function setPythonRepository(PythonRepository $pythonRepository)
  {
    $this->pythonRepository = $pythonRepository;
  }
  /**
   * @return PythonRepository
   */
  public function getPythonRepository()
  {
    return $this->pythonRepository;
  }
  /**
   * Optional. The credentials used to access the remote repository.
   *
   * @param UpstreamCredentials $upstreamCredentials
   */
  public function setUpstreamCredentials(UpstreamCredentials $upstreamCredentials)
  {
    $this->upstreamCredentials = $upstreamCredentials;
  }
  /**
   * @return UpstreamCredentials
   */
  public function getUpstreamCredentials()
  {
    return $this->upstreamCredentials;
  }
  /**
   * Specific settings for a Yum remote repository.
   *
   * @param YumRepository $yumRepository
   */
  public function setYumRepository(YumRepository $yumRepository)
  {
    $this->yumRepository = $yumRepository;
  }
  /**
   * @return YumRepository
   */
  public function getYumRepository()
  {
    return $this->yumRepository;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoteRepositoryConfig::class, 'Google_Service_ArtifactRegistry_RemoteRepositoryConfig');
