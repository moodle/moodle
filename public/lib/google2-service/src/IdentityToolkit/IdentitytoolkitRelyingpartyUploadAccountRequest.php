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

namespace Google\Service\IdentityToolkit;

class IdentitytoolkitRelyingpartyUploadAccountRequest extends \Google\Collection
{
  protected $collection_key = 'users';
  /**
   * Whether allow overwrite existing account when user local_id exists.
   *
   * @var bool
   */
  public $allowOverwrite;
  /**
   * @var int
   */
  public $blockSize;
  /**
   * The following 4 fields are for standard scrypt algorithm.
   *
   * @var int
   */
  public $cpuMemCost;
  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @var string
   */
  public $delegatedProjectNumber;
  /**
   * @var int
   */
  public $dkLen;
  /**
   * The password hash algorithm.
   *
   * @var string
   */
  public $hashAlgorithm;
  /**
   * Memory cost for hash calculation. Used by scrypt similar algorithms.
   *
   * @var int
   */
  public $memoryCost;
  /**
   * @var int
   */
  public $parallelization;
  /**
   * Rounds for hash calculation. Used by scrypt and similar algorithms.
   *
   * @var int
   */
  public $rounds;
  /**
   * The salt separator.
   *
   * @var string
   */
  public $saltSeparator;
  /**
   * If true, backend will do sanity check(including duplicate email and
   * federated id) when uploading account.
   *
   * @var bool
   */
  public $sanityCheck;
  /**
   * The key for to hash the password.
   *
   * @var string
   */
  public $signerKey;
  /**
   * Specify which project (field value is actually project id) to operate. Only
   * used when provided credential.
   *
   * @var string
   */
  public $targetProjectId;
  protected $usersType = UserInfo::class;
  protected $usersDataType = 'array';

  /**
   * Whether allow overwrite existing account when user local_id exists.
   *
   * @param bool $allowOverwrite
   */
  public function setAllowOverwrite($allowOverwrite)
  {
    $this->allowOverwrite = $allowOverwrite;
  }
  /**
   * @return bool
   */
  public function getAllowOverwrite()
  {
    return $this->allowOverwrite;
  }
  /**
   * @param int $blockSize
   */
  public function setBlockSize($blockSize)
  {
    $this->blockSize = $blockSize;
  }
  /**
   * @return int
   */
  public function getBlockSize()
  {
    return $this->blockSize;
  }
  /**
   * The following 4 fields are for standard scrypt algorithm.
   *
   * @param int $cpuMemCost
   */
  public function setCpuMemCost($cpuMemCost)
  {
    $this->cpuMemCost = $cpuMemCost;
  }
  /**
   * @return int
   */
  public function getCpuMemCost()
  {
    return $this->cpuMemCost;
  }
  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @param string $delegatedProjectNumber
   */
  public function setDelegatedProjectNumber($delegatedProjectNumber)
  {
    $this->delegatedProjectNumber = $delegatedProjectNumber;
  }
  /**
   * @return string
   */
  public function getDelegatedProjectNumber()
  {
    return $this->delegatedProjectNumber;
  }
  /**
   * @param int $dkLen
   */
  public function setDkLen($dkLen)
  {
    $this->dkLen = $dkLen;
  }
  /**
   * @return int
   */
  public function getDkLen()
  {
    return $this->dkLen;
  }
  /**
   * The password hash algorithm.
   *
   * @param string $hashAlgorithm
   */
  public function setHashAlgorithm($hashAlgorithm)
  {
    $this->hashAlgorithm = $hashAlgorithm;
  }
  /**
   * @return string
   */
  public function getHashAlgorithm()
  {
    return $this->hashAlgorithm;
  }
  /**
   * Memory cost for hash calculation. Used by scrypt similar algorithms.
   *
   * @param int $memoryCost
   */
  public function setMemoryCost($memoryCost)
  {
    $this->memoryCost = $memoryCost;
  }
  /**
   * @return int
   */
  public function getMemoryCost()
  {
    return $this->memoryCost;
  }
  /**
   * @param int $parallelization
   */
  public function setParallelization($parallelization)
  {
    $this->parallelization = $parallelization;
  }
  /**
   * @return int
   */
  public function getParallelization()
  {
    return $this->parallelization;
  }
  /**
   * Rounds for hash calculation. Used by scrypt and similar algorithms.
   *
   * @param int $rounds
   */
  public function setRounds($rounds)
  {
    $this->rounds = $rounds;
  }
  /**
   * @return int
   */
  public function getRounds()
  {
    return $this->rounds;
  }
  /**
   * The salt separator.
   *
   * @param string $saltSeparator
   */
  public function setSaltSeparator($saltSeparator)
  {
    $this->saltSeparator = $saltSeparator;
  }
  /**
   * @return string
   */
  public function getSaltSeparator()
  {
    return $this->saltSeparator;
  }
  /**
   * If true, backend will do sanity check(including duplicate email and
   * federated id) when uploading account.
   *
   * @param bool $sanityCheck
   */
  public function setSanityCheck($sanityCheck)
  {
    $this->sanityCheck = $sanityCheck;
  }
  /**
   * @return bool
   */
  public function getSanityCheck()
  {
    return $this->sanityCheck;
  }
  /**
   * The key for to hash the password.
   *
   * @param string $signerKey
   */
  public function setSignerKey($signerKey)
  {
    $this->signerKey = $signerKey;
  }
  /**
   * @return string
   */
  public function getSignerKey()
  {
    return $this->signerKey;
  }
  /**
   * Specify which project (field value is actually project id) to operate. Only
   * used when provided credential.
   *
   * @param string $targetProjectId
   */
  public function setTargetProjectId($targetProjectId)
  {
    $this->targetProjectId = $targetProjectId;
  }
  /**
   * @return string
   */
  public function getTargetProjectId()
  {
    return $this->targetProjectId;
  }
  /**
   * The account info to be stored.
   *
   * @param UserInfo[] $users
   */
  public function setUsers($users)
  {
    $this->users = $users;
  }
  /**
   * @return UserInfo[]
   */
  public function getUsers()
  {
    return $this->users;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartyUploadAccountRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyUploadAccountRequest');
