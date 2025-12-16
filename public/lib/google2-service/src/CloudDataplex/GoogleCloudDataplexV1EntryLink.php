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

class GoogleCloudDataplexV1EntryLink extends \Google\Collection
{
  protected $collection_key = 'entryReferences';
  /**
   * Output only. The time when the Entry Link was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Immutable. Relative resource name of the Entry Link Type used to
   * create this Entry Link. For example: Entry link between synonym terms in a
   * glossary: projects/dataplex-types/locations/global/entryLinkTypes/synonym
   * Entry link between related terms in a glossary: projects/dataplex-
   * types/locations/global/entryLinkTypes/related Entry link between glossary
   * terms and data assets: projects/dataplex-
   * types/locations/global/entryLinkTypes/definition
   *
   * @var string
   */
  public $entryLinkType;
  protected $entryReferencesType = GoogleCloudDataplexV1EntryLinkEntryReference::class;
  protected $entryReferencesDataType = 'array';
  /**
   * Output only. Immutable. Identifier. The relative resource name of the Entry
   * Link, of the form: projects/{project_id_or_number}/locations/{location_id}/
   * entryGroups/{entry_group_id}/entryLinks/{entry_link_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the Entry Link was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the Entry Link was created.
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
   * Required. Immutable. Relative resource name of the Entry Link Type used to
   * create this Entry Link. For example: Entry link between synonym terms in a
   * glossary: projects/dataplex-types/locations/global/entryLinkTypes/synonym
   * Entry link between related terms in a glossary: projects/dataplex-
   * types/locations/global/entryLinkTypes/related Entry link between glossary
   * terms and data assets: projects/dataplex-
   * types/locations/global/entryLinkTypes/definition
   *
   * @param string $entryLinkType
   */
  public function setEntryLinkType($entryLinkType)
  {
    $this->entryLinkType = $entryLinkType;
  }
  /**
   * @return string
   */
  public function getEntryLinkType()
  {
    return $this->entryLinkType;
  }
  /**
   * Required. Specifies the Entries referenced in the Entry Link. There should
   * be exactly two entry references.
   *
   * @param GoogleCloudDataplexV1EntryLinkEntryReference[] $entryReferences
   */
  public function setEntryReferences($entryReferences)
  {
    $this->entryReferences = $entryReferences;
  }
  /**
   * @return GoogleCloudDataplexV1EntryLinkEntryReference[]
   */
  public function getEntryReferences()
  {
    return $this->entryReferences;
  }
  /**
   * Output only. Immutable. Identifier. The relative resource name of the Entry
   * Link, of the form: projects/{project_id_or_number}/locations/{location_id}/
   * entryGroups/{entry_group_id}/entryLinks/{entry_link_id}
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
   * Output only. The time when the Entry Link was last updated.
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
class_alias(GoogleCloudDataplexV1EntryLink::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntryLink');
