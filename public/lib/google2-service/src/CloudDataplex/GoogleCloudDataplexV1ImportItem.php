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

class GoogleCloudDataplexV1ImportItem extends \Google\Collection
{
  protected $collection_key = 'aspectKeys';
  /**
   * The aspects to modify. Supports the following syntaxes:
   * {aspect_type_reference}: matches aspects that belong to the specified
   * aspect type and are attached directly to the entry.
   * {aspect_type_reference}@{path}: matches aspects that belong to the
   * specified aspect type and path. {aspect_type_reference}@* : matches aspects
   * of the given type for all paths. *@path : matches aspects of all types on
   * the given path.Replace {aspect_type_reference} with a reference to the
   * aspect type, in the format
   * {project_id_or_number}.{location_id}.{aspect_type_id}.In FULL entry sync
   * mode, if you leave this field empty, it is treated as specifying exactly
   * those aspects that are present within the specified entry. Dataplex
   * Universal Catalog implicitly adds the keys for all of the required aspects
   * of an entry.
   *
   * @var string[]
   */
  public $aspectKeys;
  protected $entryType = GoogleCloudDataplexV1Entry::class;
  protected $entryDataType = '';
  protected $entryLinkType = GoogleCloudDataplexV1EntryLink::class;
  protected $entryLinkDataType = '';
  /**
   * The fields to update, in paths that are relative to the Entry resource.
   * Separate each field with a comma.In FULL entry sync mode, Dataplex
   * Universal Catalog includes the paths of all of the fields for an entry that
   * can be modified, including aspects. This means that Dataplex Universal
   * Catalog replaces the existing entry with the entry in the metadata import
   * file. All modifiable fields are updated, regardless of the fields that are
   * listed in the update mask, and regardless of whether a field is present in
   * the entry object.The update_mask field is ignored when an entry is created
   * or re-created.In an aspect-only metadata job (when entry sync mode is
   * NONE), set this value to aspects.Dataplex Universal Catalog also determines
   * which entries and aspects to modify by comparing the values and timestamps
   * that you provide in the metadata import file with the values and timestamps
   * that exist in your project. For more information, see Comparison logic
   * (https://cloud.google.com/dataplex/docs/import-metadata#data-modification-
   * logic).
   *
   * @var string
   */
  public $updateMask;

  /**
   * The aspects to modify. Supports the following syntaxes:
   * {aspect_type_reference}: matches aspects that belong to the specified
   * aspect type and are attached directly to the entry.
   * {aspect_type_reference}@{path}: matches aspects that belong to the
   * specified aspect type and path. {aspect_type_reference}@* : matches aspects
   * of the given type for all paths. *@path : matches aspects of all types on
   * the given path.Replace {aspect_type_reference} with a reference to the
   * aspect type, in the format
   * {project_id_or_number}.{location_id}.{aspect_type_id}.In FULL entry sync
   * mode, if you leave this field empty, it is treated as specifying exactly
   * those aspects that are present within the specified entry. Dataplex
   * Universal Catalog implicitly adds the keys for all of the required aspects
   * of an entry.
   *
   * @param string[] $aspectKeys
   */
  public function setAspectKeys($aspectKeys)
  {
    $this->aspectKeys = $aspectKeys;
  }
  /**
   * @return string[]
   */
  public function getAspectKeys()
  {
    return $this->aspectKeys;
  }
  /**
   * Information about an entry and its attached aspects.
   *
   * @param GoogleCloudDataplexV1Entry $entry
   */
  public function setEntry(GoogleCloudDataplexV1Entry $entry)
  {
    $this->entry = $entry;
  }
  /**
   * @return GoogleCloudDataplexV1Entry
   */
  public function getEntry()
  {
    return $this->entry;
  }
  /**
   * Information about the entry link. User should provide either one of the
   * entry or entry_link. While providing entry_link, user should not provide
   * update_mask and aspect_keys.
   *
   * @param GoogleCloudDataplexV1EntryLink $entryLink
   */
  public function setEntryLink(GoogleCloudDataplexV1EntryLink $entryLink)
  {
    $this->entryLink = $entryLink;
  }
  /**
   * @return GoogleCloudDataplexV1EntryLink
   */
  public function getEntryLink()
  {
    return $this->entryLink;
  }
  /**
   * The fields to update, in paths that are relative to the Entry resource.
   * Separate each field with a comma.In FULL entry sync mode, Dataplex
   * Universal Catalog includes the paths of all of the fields for an entry that
   * can be modified, including aspects. This means that Dataplex Universal
   * Catalog replaces the existing entry with the entry in the metadata import
   * file. All modifiable fields are updated, regardless of the fields that are
   * listed in the update mask, and regardless of whether a field is present in
   * the entry object.The update_mask field is ignored when an entry is created
   * or re-created.In an aspect-only metadata job (when entry sync mode is
   * NONE), set this value to aspects.Dataplex Universal Catalog also determines
   * which entries and aspects to modify by comparing the values and timestamps
   * that you provide in the metadata import file with the values and timestamps
   * that exist in your project. For more information, see Comparison logic
   * (https://cloud.google.com/dataplex/docs/import-metadata#data-modification-
   * logic).
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ImportItem::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ImportItem');
