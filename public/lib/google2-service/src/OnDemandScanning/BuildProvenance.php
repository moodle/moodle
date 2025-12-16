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

namespace Google\Service\OnDemandScanning;

class BuildProvenance extends \Google\Collection
{
  protected $collection_key = 'commands';
  /**
   * Special options applied to this build. This is a catch-all field where
   * build providers can enter any desired additional details.
   *
   * @var string[]
   */
  public $buildOptions;
  /**
   * Version string of the builder at the time this build was executed.
   *
   * @var string
   */
  public $builderVersion;
  protected $builtArtifactsType = Artifact::class;
  protected $builtArtifactsDataType = 'array';
  protected $commandsType = Command::class;
  protected $commandsDataType = 'array';
  /**
   * Time at which the build was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * E-mail address of the user who initiated this build. Note that this was the
   * user's e-mail address at the time the build was initiated; this address may
   * not represent the same end-user for all time.
   *
   * @var string
   */
  public $creator;
  /**
   * Time at which execution of the build was finished.
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. Unique identifier of the build.
   *
   * @var string
   */
  public $id;
  /**
   * URI where any logs for this provenance were written.
   *
   * @var string
   */
  public $logsUri;
  /**
   * ID of the project.
   *
   * @var string
   */
  public $projectId;
  protected $sourceProvenanceType = Source::class;
  protected $sourceProvenanceDataType = '';
  /**
   * Time at which execution of the build was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Trigger identifier if the build was triggered automatically; empty if not.
   *
   * @var string
   */
  public $triggerId;

  /**
   * Special options applied to this build. This is a catch-all field where
   * build providers can enter any desired additional details.
   *
   * @param string[] $buildOptions
   */
  public function setBuildOptions($buildOptions)
  {
    $this->buildOptions = $buildOptions;
  }
  /**
   * @return string[]
   */
  public function getBuildOptions()
  {
    return $this->buildOptions;
  }
  /**
   * Version string of the builder at the time this build was executed.
   *
   * @param string $builderVersion
   */
  public function setBuilderVersion($builderVersion)
  {
    $this->builderVersion = $builderVersion;
  }
  /**
   * @return string
   */
  public function getBuilderVersion()
  {
    return $this->builderVersion;
  }
  /**
   * Output of the build.
   *
   * @param Artifact[] $builtArtifacts
   */
  public function setBuiltArtifacts($builtArtifacts)
  {
    $this->builtArtifacts = $builtArtifacts;
  }
  /**
   * @return Artifact[]
   */
  public function getBuiltArtifacts()
  {
    return $this->builtArtifacts;
  }
  /**
   * Commands requested by the build.
   *
   * @param Command[] $commands
   */
  public function setCommands($commands)
  {
    $this->commands = $commands;
  }
  /**
   * @return Command[]
   */
  public function getCommands()
  {
    return $this->commands;
  }
  /**
   * Time at which the build was created.
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
   * E-mail address of the user who initiated this build. Note that this was the
   * user's e-mail address at the time the build was initiated; this address may
   * not represent the same end-user for all time.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Time at which execution of the build was finished.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Required. Unique identifier of the build.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * URI where any logs for this provenance were written.
   *
   * @param string $logsUri
   */
  public function setLogsUri($logsUri)
  {
    $this->logsUri = $logsUri;
  }
  /**
   * @return string
   */
  public function getLogsUri()
  {
    return $this->logsUri;
  }
  /**
   * ID of the project.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Details of the Source input to the build.
   *
   * @param Source $sourceProvenance
   */
  public function setSourceProvenance(Source $sourceProvenance)
  {
    $this->sourceProvenance = $sourceProvenance;
  }
  /**
   * @return Source
   */
  public function getSourceProvenance()
  {
    return $this->sourceProvenance;
  }
  /**
   * Time at which execution of the build was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Trigger identifier if the build was triggered automatically; empty if not.
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildProvenance::class, 'Google_Service_OnDemandScanning_BuildProvenance');
