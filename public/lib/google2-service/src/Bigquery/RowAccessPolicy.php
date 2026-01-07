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

class RowAccessPolicy extends \Google\Collection
{
  protected $collection_key = 'grantees';
  /**
   * Output only. The time when this row access policy was created, in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. A hash of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. A SQL boolean expression that represents the rows defined by this
   * row access policy, similar to the boolean expression in a WHERE clause of a
   * SELECT query on a table. References to other tables, routines, and
   * temporary functions are not supported. Examples: region="EU" date_field =
   * CAST('2019-9-27' as DATE) nullable_field is not NULL numeric_field BETWEEN
   * 1.0 AND 5.0
   *
   * @var string
   */
  public $filterPredicate;
  /**
   * Optional. Input only. The optional list of iam_member users or groups that
   * specifies the initial members that the row-level access policy should be
   * created with. grantees types: - "user:alice@example.com": An email address
   * that represents a specific Google account. - "serviceAccount:my-other-
   * app@appspot.gserviceaccount.com": An email address that represents a
   * service account. - "group:admins@example.com": An email address that
   * represents a Google group. - "domain:example.com":The Google Workspace
   * domain (primary) that represents all the users of that domain. -
   * "allAuthenticatedUsers": A special identifier that represents all service
   * accounts and all users on the internet who have authenticated with a Google
   * Account. This identifier includes accounts that aren't connected to a
   * Google Workspace or Cloud Identity domain, such as personal Gmail accounts.
   * Users who aren't authenticated, such as anonymous visitors, aren't
   * included. - "allUsers":A special identifier that represents anyone who is
   * on the internet, including authenticated and unauthenticated users. Because
   * BigQuery requires authentication before a user can access the service,
   * allUsers includes only authenticated users.
   *
   * @var string[]
   */
  public $grantees;
  /**
   * Output only. The time when this row access policy was last modified, in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $rowAccessPolicyReferenceType = RowAccessPolicyReference::class;
  protected $rowAccessPolicyReferenceDataType = '';

  /**
   * Output only. The time when this row access policy was created, in
   * milliseconds since the epoch.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. A hash of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. A SQL boolean expression that represents the rows defined by this
   * row access policy, similar to the boolean expression in a WHERE clause of a
   * SELECT query on a table. References to other tables, routines, and
   * temporary functions are not supported. Examples: region="EU" date_field =
   * CAST('2019-9-27' as DATE) nullable_field is not NULL numeric_field BETWEEN
   * 1.0 AND 5.0
   *
   * @param string $filterPredicate
   */
  public function setFilterPredicate($filterPredicate)
  {
    $this->filterPredicate = $filterPredicate;
  }
  /**
   * @return string
   */
  public function getFilterPredicate()
  {
    return $this->filterPredicate;
  }
  /**
   * Optional. Input only. The optional list of iam_member users or groups that
   * specifies the initial members that the row-level access policy should be
   * created with. grantees types: - "user:alice@example.com": An email address
   * that represents a specific Google account. - "serviceAccount:my-other-
   * app@appspot.gserviceaccount.com": An email address that represents a
   * service account. - "group:admins@example.com": An email address that
   * represents a Google group. - "domain:example.com":The Google Workspace
   * domain (primary) that represents all the users of that domain. -
   * "allAuthenticatedUsers": A special identifier that represents all service
   * accounts and all users on the internet who have authenticated with a Google
   * Account. This identifier includes accounts that aren't connected to a
   * Google Workspace or Cloud Identity domain, such as personal Gmail accounts.
   * Users who aren't authenticated, such as anonymous visitors, aren't
   * included. - "allUsers":A special identifier that represents anyone who is
   * on the internet, including authenticated and unauthenticated users. Because
   * BigQuery requires authentication before a user can access the service,
   * allUsers includes only authenticated users.
   *
   * @param string[] $grantees
   */
  public function setGrantees($grantees)
  {
    $this->grantees = $grantees;
  }
  /**
   * @return string[]
   */
  public function getGrantees()
  {
    return $this->grantees;
  }
  /**
   * Output only. The time when this row access policy was last modified, in
   * milliseconds since the epoch.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Required. Reference describing the ID of this row access policy.
   *
   * @param RowAccessPolicyReference $rowAccessPolicyReference
   */
  public function setRowAccessPolicyReference(RowAccessPolicyReference $rowAccessPolicyReference)
  {
    $this->rowAccessPolicyReference = $rowAccessPolicyReference;
  }
  /**
   * @return RowAccessPolicyReference
   */
  public function getRowAccessPolicyReference()
  {
    return $this->rowAccessPolicyReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RowAccessPolicy::class, 'Google_Service_Bigquery_RowAccessPolicy');
