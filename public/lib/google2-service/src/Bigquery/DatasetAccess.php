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

namespace Google\Service\Bigquery;

class DatasetAccess extends \Google\Model
{
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  protected $datasetType = DatasetAccessEntry::class;
  protected $datasetDataType = '';
  /**
   * [Pick one] A domain to grant access to. Any users signed in with the domain
   * specified will be granted the specified access. Example: "example.com".
   * Maps to IAM policy member "domain:DOMAIN".
   *
   * @var string
   */
  public $domain;
  /**
   * [Pick one] An email address of a Google Group to grant access to. Maps to
   * IAM policy member "group:GROUP".
   *
   * @var string
   */
  public $groupByEmail;
  /**
   * [Pick one] Some other type of member that appears in the IAM Policy but
   * isn't a user, group, domain, or special group.
   *
   * @var string
   */
  public $iamMember;
  /**
   * An IAM role ID that should be granted to the user, group, or domain
   * specified in this access entry. The following legacy mappings will be
   * applied: * `OWNER`: `roles/bigquery.dataOwner` * `WRITER`:
   * `roles/bigquery.dataEditor` * `READER`: `roles/bigquery.dataViewer` This
   * field will accept any of the above formats, but will return only the legacy
   * format. For example, if you set this field to "roles/bigquery.dataOwner",
   * it will be returned back as "OWNER".
   *
   * @var string
   */
  public $role;
  protected $routineType = RoutineReference::class;
  protected $routineDataType = '';
  /**
   * [Pick one] A special group to grant access to. Possible values include: *
   * projectOwners: Owners of the enclosing project. * projectReaders: Readers
   * of the enclosing project. * projectWriters: Writers of the enclosing
   * project. * allAuthenticatedUsers: All authenticated BigQuery users. Maps to
   * similarly-named IAM members.
   *
   * @var string
   */
  public $specialGroup;
  /**
   * [Pick one] An email address of a user to grant access to. For example:
   * fred@example.com. Maps to IAM policy member "user:EMAIL" or
   * "serviceAccount:EMAIL".
   *
   * @var string
   */
  public $userByEmail;
  protected $viewType = TableReference::class;
  protected $viewDataType = '';

  /**
   * Optional. condition for the binding. If CEL expression in this field is
   * true, this access binding will be considered
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * [Pick one] A grant authorizing all resources of a particular type in a
   * particular dataset access to this dataset. Only views are supported for
   * now. The role field is not required when this field is set. If that dataset
   * is deleted and re-created, its access needs to be granted again via an
   * update operation.
   *
   * @param DatasetAccessEntry $dataset
   */
  public function setDataset(DatasetAccessEntry $dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return DatasetAccessEntry
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * [Pick one] A domain to grant access to. Any users signed in with the domain
   * specified will be granted the specified access. Example: "example.com".
   * Maps to IAM policy member "domain:DOMAIN".
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * [Pick one] An email address of a Google Group to grant access to. Maps to
   * IAM policy member "group:GROUP".
   *
   * @param string $groupByEmail
   */
  public function setGroupByEmail($groupByEmail)
  {
    $this->groupByEmail = $groupByEmail;
  }
  /**
   * @return string
   */
  public function getGroupByEmail()
  {
    return $this->groupByEmail;
  }
  /**
   * [Pick one] Some other type of member that appears in the IAM Policy but
   * isn't a user, group, domain, or special group.
   *
   * @param string $iamMember
   */
  public function setIamMember($iamMember)
  {
    $this->iamMember = $iamMember;
  }
  /**
   * @return string
   */
  public function getIamMember()
  {
    return $this->iamMember;
  }
  /**
   * An IAM role ID that should be granted to the user, group, or domain
   * specified in this access entry. The following legacy mappings will be
   * applied: * `OWNER`: `roles/bigquery.dataOwner` * `WRITER`:
   * `roles/bigquery.dataEditor` * `READER`: `roles/bigquery.dataViewer` This
   * field will accept any of the above formats, but will return only the legacy
   * format. For example, if you set this field to "roles/bigquery.dataOwner",
   * it will be returned back as "OWNER".
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * [Pick one] A routine from a different dataset to grant access to. Queries
   * executed against that routine will have read access to
   * views/tables/routines in this dataset. Only UDF is supported for now. The
   * role field is not required when this field is set. If that routine is
   * updated by any user, access to the routine needs to be granted again via an
   * update operation.
   *
   * @param RoutineReference $routine
   */
  public function setRoutine(RoutineReference $routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return RoutineReference
   */
  public function getRoutine()
  {
    return $this->routine;
  }
  /**
   * [Pick one] A special group to grant access to. Possible values include: *
   * projectOwners: Owners of the enclosing project. * projectReaders: Readers
   * of the enclosing project. * projectWriters: Writers of the enclosing
   * project. * allAuthenticatedUsers: All authenticated BigQuery users. Maps to
   * similarly-named IAM members.
   *
   * @param string $specialGroup
   */
  public function setSpecialGroup($specialGroup)
  {
    $this->specialGroup = $specialGroup;
  }
  /**
   * @return string
   */
  public function getSpecialGroup()
  {
    return $this->specialGroup;
  }
  /**
   * [Pick one] An email address of a user to grant access to. For example:
   * fred@example.com. Maps to IAM policy member "user:EMAIL" or
   * "serviceAccount:EMAIL".
   *
   * @param string $userByEmail
   */
  public function setUserByEmail($userByEmail)
  {
    $this->userByEmail = $userByEmail;
  }
  /**
   * @return string
   */
  public function getUserByEmail()
  {
    return $this->userByEmail;
  }
  /**
   * [Pick one] A view from a different dataset to grant access to. Queries
   * executed against that view will have read access to views/tables/routines
   * in this dataset. The role field is not required when this field is set. If
   * that view is updated by any user, access to the view needs to be granted
   * again via an update operation.
   *
   * @param TableReference $view
   */
  public function setView(TableReference $view)
  {
    $this->view = $view;
  }
  /**
   * @return TableReference
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatasetAccess::class, 'Google_Service_Bigquery_DatasetAccess');
