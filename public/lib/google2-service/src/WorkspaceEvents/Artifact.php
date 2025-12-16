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

namespace Google\Service\WorkspaceEvents;

class Artifact extends \Google\Collection
{
  protected $collection_key = 'parts';
  /**
   * Unique identifier (e.g. UUID) for the artifact. It must be at least unique
   * within a task.
   *
   * @var string
   */
  public $artifactId;
  /**
   * A human readable description of the artifact, optional.
   *
   * @var string
   */
  public $description;
  /**
   * The URIs of extensions that are present or contributed to this Artifact.
   *
   * @var string[]
   */
  public $extensions;
  /**
   * Optional metadata included with the artifact.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * A human readable name for the artifact.
   *
   * @var string
   */
  public $name;
  protected $partsType = Part::class;
  protected $partsDataType = 'array';

  /**
   * Unique identifier (e.g. UUID) for the artifact. It must be at least unique
   * within a task.
   *
   * @param string $artifactId
   */
  public function setArtifactId($artifactId)
  {
    $this->artifactId = $artifactId;
  }
  /**
   * @return string
   */
  public function getArtifactId()
  {
    return $this->artifactId;
  }
  /**
   * A human readable description of the artifact, optional.
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
   * The URIs of extensions that are present or contributed to this Artifact.
   *
   * @param string[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return string[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * Optional metadata included with the artifact.
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
   * A human readable name for the artifact.
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
   * The content of the artifact.
   *
   * @param Part[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return Part[]
   */
  public function getParts()
  {
    return $this->parts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Artifact::class, 'Google_Service_WorkspaceEvents_Artifact');
