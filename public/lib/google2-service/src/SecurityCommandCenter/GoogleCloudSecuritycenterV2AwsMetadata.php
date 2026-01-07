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

class GoogleCloudSecuritycenterV2AwsMetadata extends \Google\Collection
{
  protected $collection_key = 'organizationalUnits';
  protected $accountType = GoogleCloudSecuritycenterV2AwsAccount::class;
  protected $accountDataType = '';
  protected $organizationType = GoogleCloudSecuritycenterV2AwsOrganization::class;
  protected $organizationDataType = '';
  protected $organizationalUnitsType = GoogleCloudSecuritycenterV2AwsOrganizationalUnit::class;
  protected $organizationalUnitsDataType = 'array';

  /**
   * The AWS account associated with the resource.
   *
   * @param GoogleCloudSecuritycenterV2AwsAccount $account
   */
  public function setAccount(GoogleCloudSecuritycenterV2AwsAccount $account)
  {
    $this->account = $account;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AwsAccount
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * The AWS organization associated with the resource.
   *
   * @param GoogleCloudSecuritycenterV2AwsOrganization $organization
   */
  public function setOrganization(GoogleCloudSecuritycenterV2AwsOrganization $organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AwsOrganization
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * A list of AWS organizational units associated with the resource, ordered
   * from lowest level (closest to the account) to highest level.
   *
   * @param GoogleCloudSecuritycenterV2AwsOrganizationalUnit[] $organizationalUnits
   */
  public function setOrganizationalUnits($organizationalUnits)
  {
    $this->organizationalUnits = $organizationalUnits;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AwsOrganizationalUnit[]
   */
  public function getOrganizationalUnits()
  {
    return $this->organizationalUnits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2AwsMetadata::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2AwsMetadata');
