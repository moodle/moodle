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

namespace Google\Service\Speech;

class PhraseSet extends \Google\Collection
{
  /**
   * Unspecified state. This is only used/useful for distinguishing unset
   * values.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * This CustomClass has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'phrases';
  /**
   * Output only. Allows users to store small amounts of arbitrary data. Both
   * the key and the value must be 63 characters or less each. At most 100
   * annotations. This field is not used.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Hint Boost. Positive value will increase the probability that a specific
   * phrase will be recognized over other similar sounding phrases. The higher
   * the boost, the higher the chance of false positive recognition as well.
   * Negative boost values would correspond to anti-biasing. Anti-biasing is not
   * enabled, so negative boost will simply be ignored. Though `boost` can
   * accept a wide range of positive values, most use cases are best served with
   * values between 0 (exclusive) and 20. We recommend using a binary search
   * approach to finding the optimal value for your use case as well as adding
   * phrases both with and without boost to your requests.
   *
   * @var float
   */
  public $boost;
  /**
   * Output only. The time at which this resource was requested for deletion.
   * This field is not used.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Output only. User-settable, human-readable name for the PhraseSet. Must be
   * 63 characters or less. This field is not used.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields. This may be sent on update, undelete, and delete requests to
   * ensure the client has an up-to-date value before proceeding. This field is
   * not used.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The time at which this resource will be purged. This field is
   * not used.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The [KMS key name](https://cloud.google.com/kms/docs/resource-
   * hierarchy#keys) with which the content of the PhraseSet is encrypted. The
   * expected format is `projects/{project}/locations/{location}/keyRings/{key_r
   * ing}/cryptoKeys/{crypto_key}`.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. The [KMS key version
   * name](https://cloud.google.com/kms/docs/resource-hierarchy#key_versions)
   * with which content of the PhraseSet is encrypted. The expected format is `p
   * rojects/{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{cryp
   * to_key}/cryptoKeyVersions/{crypto_key_version}`.
   *
   * @var string
   */
  public $kmsKeyVersionName;
  /**
   * The resource name of the phrase set.
   *
   * @var string
   */
  public $name;
  protected $phrasesType = Phrase::class;
  protected $phrasesDataType = 'array';
  /**
   * Output only. Whether or not this PhraseSet is in the process of being
   * updated. This field is not used.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The CustomClass lifecycle state. This field is not used.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-assigned unique identifier for the PhraseSet. This
   * field is not used.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. Allows users to store small amounts of arbitrary data. Both
   * the key and the value must be 63 characters or less each. At most 100
   * annotations. This field is not used.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Hint Boost. Positive value will increase the probability that a specific
   * phrase will be recognized over other similar sounding phrases. The higher
   * the boost, the higher the chance of false positive recognition as well.
   * Negative boost values would correspond to anti-biasing. Anti-biasing is not
   * enabled, so negative boost will simply be ignored. Though `boost` can
   * accept a wide range of positive values, most use cases are best served with
   * values between 0 (exclusive) and 20. We recommend using a binary search
   * approach to finding the optimal value for your use case as well as adding
   * phrases both with and without boost to your requests.
   *
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * Output only. The time at which this resource was requested for deletion.
   * This field is not used.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Output only. User-settable, human-readable name for the PhraseSet. Must be
   * 63 characters or less. This field is not used.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields. This may be sent on update, undelete, and delete requests to
   * ensure the client has an up-to-date value before proceeding. This field is
   * not used.
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
   * Output only. The time at which this resource will be purged. This field is
   * not used.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The [KMS key name](https://cloud.google.com/kms/docs/resource-
   * hierarchy#keys) with which the content of the PhraseSet is encrypted. The
   * expected format is `projects/{project}/locations/{location}/keyRings/{key_r
   * ing}/cryptoKeys/{crypto_key}`.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. The [KMS key version
   * name](https://cloud.google.com/kms/docs/resource-hierarchy#key_versions)
   * with which content of the PhraseSet is encrypted. The expected format is `p
   * rojects/{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{cryp
   * to_key}/cryptoKeyVersions/{crypto_key_version}`.
   *
   * @param string $kmsKeyVersionName
   */
  public function setKmsKeyVersionName($kmsKeyVersionName)
  {
    $this->kmsKeyVersionName = $kmsKeyVersionName;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersionName()
  {
    return $this->kmsKeyVersionName;
  }
  /**
   * The resource name of the phrase set.
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
   * A list of word and phrases.
   *
   * @param Phrase[] $phrases
   */
  public function setPhrases($phrases)
  {
    $this->phrases = $phrases;
  }
  /**
   * @return Phrase[]
   */
  public function getPhrases()
  {
    return $this->phrases;
  }
  /**
   * Output only. Whether or not this PhraseSet is in the process of being
   * updated. This field is not used.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The CustomClass lifecycle state. This field is not used.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. System-assigned unique identifier for the PhraseSet. This
   * field is not used.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PhraseSet::class, 'Google_Service_Speech_PhraseSet');
