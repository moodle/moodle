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

namespace Google\Service\Spanner;

class DdlStatementActionInfo extends \Google\Collection
{
  protected $collection_key = 'entityNames';
  /**
   * The action for the DDL statement, for example, CREATE, ALTER, DROP, GRANT,
   * etc. This field is a non-empty string.
   *
   * @var string
   */
  public $action;
  /**
   * The entity names being operated on the DDL statement. For example, 1. For
   * statement "CREATE TABLE t1(...)", `entity_names` = ["t1"]. 2. For statement
   * "GRANT ROLE r1, r2 ...", `entity_names` = ["r1", "r2"]. 3. For statement
   * "ANALYZE", `entity_names` = [].
   *
   * @var string[]
   */
  public $entityNames;
  /**
   * The entity type for the DDL statement, for example, TABLE, INDEX, VIEW,
   * etc. This field can be empty string for some DDL statement, for example,
   * for statement "ANALYZE", `entity_type` = "".
   *
   * @var string
   */
  public $entityType;

  /**
   * The action for the DDL statement, for example, CREATE, ALTER, DROP, GRANT,
   * etc. This field is a non-empty string.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * The entity names being operated on the DDL statement. For example, 1. For
   * statement "CREATE TABLE t1(...)", `entity_names` = ["t1"]. 2. For statement
   * "GRANT ROLE r1, r2 ...", `entity_names` = ["r1", "r2"]. 3. For statement
   * "ANALYZE", `entity_names` = [].
   *
   * @param string[] $entityNames
   */
  public function setEntityNames($entityNames)
  {
    $this->entityNames = $entityNames;
  }
  /**
   * @return string[]
   */
  public function getEntityNames()
  {
    return $this->entityNames;
  }
  /**
   * The entity type for the DDL statement, for example, TABLE, INDEX, VIEW,
   * etc. This field can be empty string for some DDL statement, for example,
   * for statement "ANALYZE", `entity_type` = "".
   *
   * @param string $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return string
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DdlStatementActionInfo::class, 'Google_Service_Spanner_DdlStatementActionInfo');
