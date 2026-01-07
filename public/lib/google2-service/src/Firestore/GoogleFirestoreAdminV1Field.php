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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1Field extends \Google\Model
{
  protected $indexConfigType = GoogleFirestoreAdminV1IndexConfig::class;
  protected $indexConfigDataType = '';
  /**
   * Required. A field name of the form: `projects/{project_id}/databases/{datab
   * ase_id}/collectionGroups/{collection_id}/fields/{field_path}` A field path
   * can be a simple field name, e.g. `address` or a path to fields within
   * `map_value` , e.g. `address.city`, or a special field path. The only valid
   * special field is `*`, which represents any field. Field paths can be quoted
   * using `` ` `` (backtick). The only character that must be escaped within a
   * quoted field path is the backtick character itself, escaped using a
   * backslash. Special characters in field paths that must be quoted include:
   * `*`, `.`, `` ` `` (backtick), `[`, `]`, as well as any ascii symbolic
   * characters. Examples: `` `address.city` `` represents a field named
   * `address.city`, not the map key `city` in the field `address`. `` `*` ``
   * represents a field named `*`, not any field. A special `Field` contains the
   * default indexing settings for all fields. This field's resource name is: `p
   * rojects/{project_id}/databases/{database_id}/collectionGroups/__default__/f
   * ields` Indexes defined on this `Field` will be applied to all fields which
   * do not have their own `Field` index configuration.
   *
   * @var string
   */
  public $name;
  protected $ttlConfigType = GoogleFirestoreAdminV1TtlConfig::class;
  protected $ttlConfigDataType = '';

  /**
   * The index configuration for this field. If unset, field indexing will
   * revert to the configuration defined by the `ancestor_field`. To explicitly
   * remove all indexes for this field, specify an index config with an empty
   * list of indexes.
   *
   * @param GoogleFirestoreAdminV1IndexConfig $indexConfig
   */
  public function setIndexConfig(GoogleFirestoreAdminV1IndexConfig $indexConfig)
  {
    $this->indexConfig = $indexConfig;
  }
  /**
   * @return GoogleFirestoreAdminV1IndexConfig
   */
  public function getIndexConfig()
  {
    return $this->indexConfig;
  }
  /**
   * Required. A field name of the form: `projects/{project_id}/databases/{datab
   * ase_id}/collectionGroups/{collection_id}/fields/{field_path}` A field path
   * can be a simple field name, e.g. `address` or a path to fields within
   * `map_value` , e.g. `address.city`, or a special field path. The only valid
   * special field is `*`, which represents any field. Field paths can be quoted
   * using `` ` `` (backtick). The only character that must be escaped within a
   * quoted field path is the backtick character itself, escaped using a
   * backslash. Special characters in field paths that must be quoted include:
   * `*`, `.`, `` ` `` (backtick), `[`, `]`, as well as any ascii symbolic
   * characters. Examples: `` `address.city` `` represents a field named
   * `address.city`, not the map key `city` in the field `address`. `` `*` ``
   * represents a field named `*`, not any field. A special `Field` contains the
   * default indexing settings for all fields. This field's resource name is: `p
   * rojects/{project_id}/databases/{database_id}/collectionGroups/__default__/f
   * ields` Indexes defined on this `Field` will be applied to all fields which
   * do not have their own `Field` index configuration.
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
   * The TTL configuration for this `Field`. Setting or unsetting this will
   * enable or disable the TTL for documents that have this `Field`.
   *
   * @param GoogleFirestoreAdminV1TtlConfig $ttlConfig
   */
  public function setTtlConfig(GoogleFirestoreAdminV1TtlConfig $ttlConfig)
  {
    $this->ttlConfig = $ttlConfig;
  }
  /**
   * @return GoogleFirestoreAdminV1TtlConfig
   */
  public function getTtlConfig()
  {
    return $this->ttlConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1Field::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1Field');
