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

namespace Google\Service\SecurityCommandCenter;

class AwsMetadata extends \Google\Collection
{
  protected $collection_key = 'organizationalUnits';
  protected $accountType = AwsAccount::class;
  protected $accountDataType = '';
  protected $organizationType = AwsOrganization::class;
  protected $organizationDataType = '';
  protected $organizationalUnitsType = AwsOrganizationalUnit::class;
  protected $organizationalUnitsDataType = 'array';

  /**
   * The AWS account associated with the resource.
   *
   * @param AwsAccount $account
   */
  public function setAccount(AwsAccount $account)
  {
    $this->account = $account;
  }
  /**
   * @return AwsAccount
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * The AWS organization associated with the resource.
   *
   * @param AwsOrganization $organization
   */
  public function setOrganization(AwsOrganization $organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return AwsOrganization
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * A list of AWS organizational units associated with the resource, ordered
   * from lowest level (closest to the account) to highest level.
   *
   * @param AwsOrganizationalUnit[] $organizationalUnits
   */
  public function setOrganizationalUnits($organizationalUnits)
  {
    $this->organizationalUnits = $organizationalUnits;
  }
  /**
   * @return AwsOrganizationalUnit[]
   */
  public function getOrganizationalUnits()
  {
    return $this->organizationalUnits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsMetadata::class, 'Google_Service_SecurityCommandCenter_AwsMetadata');
