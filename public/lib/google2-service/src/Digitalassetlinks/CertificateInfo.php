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

namespace Google\Service\Digitalassetlinks;

class CertificateInfo extends \Google\Model
{
  /**
   * The uppercase SHA-265 fingerprint of the certificate. From the PEM
   * certificate, it can be acquired like this: $ keytool -printcert -file
   * $CERTFILE | grep SHA256: SHA256:
   * 14:6D:E9:83:C5:73:06:50:D8:EE:B9:95:2F:34:FC:64:16:A0:83: \
   * 42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:44:E5 or like this: $ openssl x509 -in
   * $CERTFILE -noout -fingerprint -sha256 SHA256
   * Fingerprint=14:6D:E9:83:C5:73:06:50:D8:EE:B9:95:2F:34:FC:64: \
   * 16:A0:83:42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:44:E5 In this example, the
   * contents of this field would be `14:6D:E9:83:C5:73:
   * 06:50:D8:EE:B9:95:2F:34:FC:64:16:A0:83:42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:
   * 44:E5`. If these tools are not available to you, you can convert the PEM
   * certificate into the DER format, compute the SHA-256 hash of that string
   * and represent the result as a hexstring (that is, uppercase hexadecimal
   * representations of each octet, separated by colons).
   *
   * @var string
   */
  public $sha256Fingerprint;

  /**
   * The uppercase SHA-265 fingerprint of the certificate. From the PEM
   * certificate, it can be acquired like this: $ keytool -printcert -file
   * $CERTFILE | grep SHA256: SHA256:
   * 14:6D:E9:83:C5:73:06:50:D8:EE:B9:95:2F:34:FC:64:16:A0:83: \
   * 42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:44:E5 or like this: $ openssl x509 -in
   * $CERTFILE -noout -fingerprint -sha256 SHA256
   * Fingerprint=14:6D:E9:83:C5:73:06:50:D8:EE:B9:95:2F:34:FC:64: \
   * 16:A0:83:42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:44:E5 In this example, the
   * contents of this field would be `14:6D:E9:83:C5:73:
   * 06:50:D8:EE:B9:95:2F:34:FC:64:16:A0:83:42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:
   * 44:E5`. If these tools are not available to you, you can convert the PEM
   * certificate into the DER format, compute the SHA-256 hash of that string
   * and represent the result as a hexstring (that is, uppercase hexadecimal
   * representations of each octet, separated by colons).
   *
   * @param string $sha256Fingerprint
   */
  public function setSha256Fingerprint($sha256Fingerprint)
  {
    $this->sha256Fingerprint = $sha256Fingerprint;
  }
  /**
   * @return string
   */
  public function getSha256Fingerprint()
  {
    return $this->sha256Fingerprint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateInfo::class, 'Google_Service_Digitalassetlinks_CertificateInfo');
