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

class GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope extends \Google\Collection
{
  protected $collection_key = 'projects';
  /**
   * The aspect types that are in scope for the export job, specified as
   * relative resource names in the format projects/{project_id_or_number}/locat
   * ions/{location}/aspectTypes/{aspect_type_id}. Only aspects that belong to
   * the specified aspect types are affected by the job.
   *
   * @var string[]
   */
  public $aspectTypes;
  /**
   * The entry groups whose metadata you want to export, in the format projects/
   * {project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id}
   * . Only the entries in the specified entry groups are exported.The entry
   * groups must be in the same location and the same VPC Service Controls
   * perimeter as the job.If you set the job scope to be a list of entry groups,
   * then set the organization-level export flag to false and don't provide a
   * list of projects.
   *
   * @var string[]
   */
  public $entryGroups;
  /**
   * The entry types that are in scope for the export job, specified as relative
   * resource names in the format projects/{project_id_or_number}/locations/{loc
   * ation}/entryTypes/{entry_type_id}. Only entries that belong to the
   * specified entry types are affected by the job.
   *
   * @var string[]
   */
  public $entryTypes;
  /**
   * Whether the metadata export job is an organization-level export job. If
   * true, the job exports the entries from the same organization and VPC
   * Service Controls perimeter as the job. The project that the job belongs to
   * determines the VPC Service Controls perimeter. If you set the job scope to
   * be at the organization level, then don't provide a list of projects or
   * entry groups. If false, you must specify a list of projects or a list of
   * entry groups whose entries you want to export.The default is false.
   *
   * @var bool
   */
  public $organizationLevel;
  /**
   * The projects whose metadata you want to export, in the format
   * projects/{project_id_or_number}. Only the entries from the specified
   * projects are exported.The projects must be in the same organization and VPC
   * Service Controls perimeter as the job.If you set the job scope to be a list
   * of projects, then set the organization-level export flag to false and don't
   * provide a list of entry groups.
   *
   * @var string[]
   */
  public $projects;

  /**
   * The aspect types that are in scope for the export job, specified as
   * relative resource names in the format projects/{project_id_or_number}/locat
   * ions/{location}/aspectTypes/{aspect_type_id}. Only aspects that belong to
   * the specified aspect types are affected by the job.
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
   * The entry groups whose metadata you want to export, in the format projects/
   * {project_id_or_number}/locations/{location_id}/entryGroups/{entry_group_id}
   * . Only the entries in the specified entry groups are exported.The entry
   * groups must be in the same location and the same VPC Service Controls
   * perimeter as the job.If you set the job scope to be a list of entry groups,
   * then set the organization-level export flag to false and don't provide a
   * list of projects.
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
   * The entry types that are in scope for the export job, specified as relative
   * resource names in the format projects/{project_id_or_number}/locations/{loc
   * ation}/entryTypes/{entry_type_id}. Only entries that belong to the
   * specified entry types are affected by the job.
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
   * Whether the metadata export job is an organization-level export job. If
   * true, the job exports the entries from the same organization and VPC
   * Service Controls perimeter as the job. The project that the job belongs to
   * determines the VPC Service Controls perimeter. If you set the job scope to
   * be at the organization level, then don't provide a list of projects or
   * entry groups. If false, you must specify a list of projects or a list of
   * entry groups whose entries you want to export.The default is false.
   *
   * @param bool $organizationLevel
   */
  public function setOrganizationLevel($organizationLevel)
  {
    $this->organizationLevel = $organizationLevel;
  }
  /**
   * @return bool
   */
  public function getOrganizationLevel()
  {
    return $this->organizationLevel;
  }
  /**
   * The projects whose metadata you want to export, in the format
   * projects/{project_id_or_number}. Only the entries from the specified
   * projects are exported.The projects must be in the same organization and VPC
   * Service Controls perimeter as the job.If you set the job scope to be a list
   * of projects, then set the organization-level export flag to false and don't
   * provide a list of entry groups.
   *
   * @param string[] $projects
   */
  public function setProjects($projects)
  {
    $this->projects = $projects;
  }
  /**
   * @return string[]
   */
  public function getProjects()
  {
    return $this->projects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope');
