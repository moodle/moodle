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

class GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope extends \Google\Collection
{
  protected $collection_key = 'referencedEntryScopes';
  /**
   * Optional. The aspect types that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/aspectTypes/{aspect_type_id}. The job modifies only
   * the aspects that belong to these aspect types.This field is required when
   * creating an aspect-only import job.If the metadata import file attempts to
   * modify an aspect whose type isn't included in this list, the import job is
   * halted before modifying any entries or aspects.The location of an aspect
   * type must either match the location of the job, or the aspect type must be
   * global.
   *
   * @var string[]
   */
  public $aspectTypes;
  /**
   * Required. The entry groups that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/entryGroups/{entry_group_id}. Only entries and
   * aspects that belong to the specified entry groups are affected by the
   * job.The entry groups and the job must be in the same location.
   *
   * @var string[]
   */
  public $entryGroups;
  /**
   * Optional. The entry link types that are in scope for the import job,
   * specified as relative resource names in the format projects/{project_number
   * _or_id}/locations/{location_id}/entryLinkTypes/{entry_link_type_id}. The
   * job modifies only the entryLinks that belong to these entry link types.If
   * the metadata import file attempts to create or delete an entry link whose
   * entry link type isn't included in this list, the import job will skip those
   * entry links.
   *
   * @var string[]
   */
  public $entryLinkTypes;
  /**
   * Required. The entry types that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/entryTypes/{entry_type_id}. The job modifies only the
   * entries and aspects that belong to these entry types.If the metadata import
   * file attempts to modify an entry whose type isn't included in this list,
   * the import job is halted before modifying any entries or aspects.The
   * location of an entry type must either match the location of the job, or the
   * entry type must be global.
   *
   * @var string[]
   */
  public $entryTypes;
  /**
   * Optional. The glossaries that are in scope for the import job, specified as
   * relative resource names in the format projects/{project_number_or_id}/locat
   * ions/{location_id}/glossaries/{glossary_id}.While importing Business
   * Glossary entries, the user must provide glossaries. While importing
   * entries, the user does not have to provide glossaries. If the metadata
   * import file attempts to modify Business Glossary entries whose glossary
   * isn't included in this list, the import job will skip those entries.The
   * location of a glossary must either match the location of the job, or the
   * glossary must be global.
   *
   * @var string[]
   */
  public $glossaries;
  /**
   * Optional. Defines the scope of entries that can be referenced in the entry
   * links.Currently, projects are supported as valid scopes. Format:
   * projects/{project_number_or_id}If the metadata import file attempts to
   * create an entry link which references an entry that is not in the scope,
   * the import job will skip that entry link.
   *
   * @var string[]
   */
  public $referencedEntryScopes;

  /**
   * Optional. The aspect types that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/aspectTypes/{aspect_type_id}. The job modifies only
   * the aspects that belong to these aspect types.This field is required when
   * creating an aspect-only import job.If the metadata import file attempts to
   * modify an aspect whose type isn't included in this list, the import job is
   * halted before modifying any entries or aspects.The location of an aspect
   * type must either match the location of the job, or the aspect type must be
   * global.
   *
   * @param string[] $aspectTypes
   */
  public function setAspectTypes($aspectTypes)
  {
    $this->aspectTypes = $aspectTypes;
  }
  /**
   * @return string[]
   */
  public function getAspectTypes()
  {
    return $this->aspectTypes;
  }
  /**
   * Required. The entry groups that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/entryGroups/{entry_group_id}. Only entries and
   * aspects that belong to the specified entry groups are affected by the
   * job.The entry groups and the job must be in the same location.
   *
   * @param string[] $entryGroups
   */
  public function setEntryGroups($entryGroups)
  {
    $this->entryGroups = $entryGroups;
  }
  /**
   * @return string[]
   */
  public function getEntryGroups()
  {
    return $this->entryGroups;
  }
  /**
   * Optional. The entry link types that are in scope for the import job,
   * specified as relative resource names in the format projects/{project_number
   * _or_id}/locations/{location_id}/entryLinkTypes/{entry_link_type_id}. The
   * job modifies only the entryLinks that belong to these entry link types.If
   * the metadata import file attempts to create or delete an entry link whose
   * entry link type isn't included in this list, the import job will skip those
   * entry links.
   *
   * @param string[] $entryLinkTypes
   */
  public function setEntryLinkTypes($entryLinkTypes)
  {
    $this->entryLinkTypes = $entryLinkTypes;
  }
  /**
   * @return string[]
   */
  public function getEntryLinkTypes()
  {
    return $this->entryLinkTypes;
  }
  /**
   * Required. The entry types that are in scope for the import job, specified
   * as relative resource names in the format projects/{project_number_or_id}/lo
   * cations/{location_id}/entryTypes/{entry_type_id}. The job modifies only the
   * entries and aspects that belong to these entry types.If the metadata import
   * file attempts to modify an entry whose type isn't included in this list,
   * the import job is halted before modifying any entries or aspects.The
   * location of an entry type must either match the location of the job, or the
   * entry type must be global.
   *
   * @param string[] $entryTypes
   */
  public function setEntryTypes($entryTypes)
  {
    $this->entryTypes = $entryTypes;
  }
  /**
   * @return string[]
   */
  public function getEntryTypes()
  {
    return $this->entryTypes;
  }
  /**
   * Optional. The glossaries that are in scope for the import job, specified as
   * relative resource names in the format projects/{project_number_or_id}/locat
   * ions/{location_id}/glossaries/{glossary_id}.While importing Business
   * Glossary entries, the user must provide glossaries. While importing
   * entries, the user does not have to provide glossaries. If the metadata
   * import file attempts to modify Business Glossary entries whose glossary
   * isn't included in this list, the import job will skip those entries.The
   * location of a glossary must either match the location of the job, or the
   * glossary must be global.
   *
   * @param string[] $glossaries
   */
  public function setGlossaries($glossaries)
  {
    $this->glossaries = $glossaries;
  }
  /**
   * @return string[]
   */
  public function getGlossaries()
  {
    return $this->glossaries;
  }
  /**
   * Optional. Defines the scope of entries that can be referenced in the entry
   * links.Currently, projects are supported as valid scopes. Format:
   * projects/{project_number_or_id}If the metadata import file attempts to
   * create an entry link which references an entry that is not in the scope,
   * the import job will skip that entry link.
   *
   * @param string[] $referencedEntryScopes
   */
  public function setReferencedEntryScopes($referencedEntryScopes)
  {
    $this->referencedEntryScopes = $referencedEntryScopes;
  }
  /**
   * @return string[]
   */
  public function getReferencedEntryScopes()
  {
    return $this->referencedEntryScopes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope');
