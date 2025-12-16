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

namespace Google\Service\BinaryAuthorization;

class AttestorPublicKey extends \Google\Model
{
  /**
   * ASCII-armored representation of a PGP public key, as the entire output by
   * the command `gpg --export --armor foo@example.com` (either LF or CRLF line
   * endings). When using this field, `id` should be left blank. The Binary
   * Authorization API handlers will calculate the ID and fill it in
   * automatically. Binary Authorization computes this ID as the OpenPGP RFC4880
   * V4 fingerprint, represented as upper-case hex. If `id` is provided by the
   * caller, it will be overwritten by the API-calculated ID.
   *
   * @var string
   */
  public $asciiArmoredPgpPublicKey;
  /**
   * Optional. A descriptive comment. This field may be updated.
   *
   * @var string
   */
  public $comment;
  /**
   * The ID of this public key. Signatures verified by Binary Authorization must
   * include the ID of the public key that can be used to verify them, and that
   * ID must match the contents of this field exactly. Additional restrictions
   * on this field can be imposed based on which public key type is
   * encapsulated. See the documentation on `public_key` cases below for
   * details.
   *
   * @var string
   */
  public $id;
  protected $pkixPublicKeyType = PkixPublicKey::class;
  protected $pkixPublicKeyDataType = '';

  /**
   * ASCII-armored representation of a PGP public key, as the entire output by
   * the command `gpg --export --armor foo@example.com` (either LF or CRLF line
   * endings). When using this field, `id` should be left blank. The Binary
   * Authorization API handlers will calculate the ID and fill it in
   * automatically. Binary Authorization computes this ID as the OpenPGP RFC4880
   * V4 fingerprint, represented as upper-case hex. If `id` is provided by the
   * caller, it will be overwritten by the API-calculated ID.
   *
   * @param string $asciiArmoredPgpPublicKey
   */
  public function setAsciiArmoredPgpPublicKey($asciiArmoredPgpPublicKey)
  {
    $this->asciiArmoredPgpPublicKey = $asciiArmoredPgpPublicKey;
  }
  /**
   * @return string
   */
  public function getAsciiArmoredPgpPublicKey()
  {
    return $this->asciiArmoredPgpPublicKey;
  }
  /**
   * Optional. A descriptive comment. This field may be updated.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * The ID of this public key. Signatures verified by Binary Authorization must
   * include the ID of the public key that can be used to verify them, and that
   * ID must match the contents of this field exactly. Additional restrictions
   * on this field can be imposed based on which public key type is
   * encapsulated. See the documentation on `public_key` cases below for
   * details.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A raw PKIX SubjectPublicKeyInfo format public key. NOTE: `id` may be
   * explicitly provided by the caller when using this type of public key, but
   * it MUST be a valid RFC3986 URI. If `id` is left blank, a default one will
   * be computed based on the digest of the DER encoding of the public key.
   *
   * @param PkixPublicKey $pkixPublicKey
   */
  public function setPkixPublicKey(PkixPublicKey $pkixPublicKey)
  {
    $this->pkixPublicKey = $pkixPublicKey;
  }
  /**
   * @return PkixPublicKey
   */
  public function getPkixPublicKey()
  {
    return $this->pkixPublicKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttestorPublicKey::class, 'Google_Service_BinaryAuthorization_AttestorPublicKey');
