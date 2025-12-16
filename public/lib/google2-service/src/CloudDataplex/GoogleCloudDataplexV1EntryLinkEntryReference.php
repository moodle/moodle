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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EntryLinkEntryReference extends \Google\Model
{
  /**
   * Unspecified reference type. Implies that the Entry is referenced in a non-
   * directional Entry Link.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The Entry is referenced as the source of the directional Entry Link.
   */
  public const TYPE_SOURCE = 'SOURCE';
  /**
   * The Entry is referenced as the target of the directional Entry Link.
   */
  public const TYPE_TARGET = 'TARGET';
  /**
   * Required. Immutable. The relative resource name of the referenced Entry, of
   * the form: projects/{project_id_or_number}/locations/{location_id}/entryGrou
   * ps/{entry_group_id}/entries/{entry_id}
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The path in the Entry that is referenced in the Entry Link.
   * Empty path denotes that the Entry itself is referenced in the Entry Link.
   *
   * @var string
   */
  public $path;
  /**
   * Required. Immutable. The reference type of the Entry.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Immutable. The relative resource name of the referenced Entry, of
   * the form: projects/{project_id_or_number}/locations/{location_id}/entryGrou
   * ps/{entry_group_id}/entries/{entry_id}
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
   * Immutable. The path in the Entry that is referenced in the Entry Link.
   * Empty path denotes that the Entry itself is referenced in the Entry Link.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Required. Immutable. The reference type of the Entry.
   *
   * Accepted values: UNSPECIFIED, SOURCE, TARGET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EntryLinkEntryReference::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntryLinkEntryReference');
