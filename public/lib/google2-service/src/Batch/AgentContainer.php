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

namespace Google\Service\Batch;

class AgentContainer extends \Google\Collection
{
  protected $collection_key = 'volumes';
  /**
   * Overrides the `CMD` specified in the container. If there is an ENTRYPOINT
   * (either in the container image or with the entrypoint field below) then
   * commands are appended as arguments to the ENTRYPOINT.
   *
   * @var string[]
   */
  public $commands;
  /**
   * Overrides the `ENTRYPOINT` specified in the container.
   *
   * @var string
   */
  public $entrypoint;
  /**
   * The URI to pull the container image from.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Arbitrary additional options to include in the "docker run" command when
   * running this container, e.g. "--network host".
   *
   * @var string
   */
  public $options;
  /**
   * Volumes to mount (bind mount) from the host machine files or directories
   * into the container, formatted to match docker run's --volume option, e.g.
   * /foo:/bar, or /foo:/bar:ro
   *
   * @var string[]
   */
  public $volumes;

  /**
   * Overrides the `CMD` specified in the container. If there is an ENTRYPOINT
   * (either in the container image or with the entrypoint field below) then
   * commands are appended as arguments to the ENTRYPOINT.
   *
   * @param string[] $commands
   */
  public function setCommands($commands)
  {
    $this->commands = $commands;
  }
  /**
   * @return string[]
   */
  public function getCommands()
  {
    return $this->commands;
  }
  /**
   * Overrides the `ENTRYPOINT` specified in the container.
   *
   * @param string $entrypoint
   */
  public function setEntrypoint($entrypoint)
  {
    $this->entrypoint = $entrypoint;
  }
  /**
   * @return string
   */
  public function getEntrypoint()
  {
    return $this->entrypoint;
  }
  /**
   * The URI to pull the container image from.
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
   * Arbitrary additional options to include in the "docker run" command when
   * running this container, e.g. "--network host".
   *
   * @param string $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return string
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Volumes to mount (bind mount) from the host machine files or directories
   * into the container, formatted to match docker run's --volume option, e.g.
   * /foo:/bar, or /foo:/bar:ro
   *
   * @param string[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return string[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentContainer::class, 'Google_Service_Batch_AgentContainer');
