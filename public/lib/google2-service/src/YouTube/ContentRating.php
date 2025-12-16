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

namespace Google\Service\YouTube;

class ContentRating extends \Google\Collection
{
  public const ACB_RATING_acbUnspecified = 'acbUnspecified';
  /**
   * E
   */
  public const ACB_RATING_acbE = 'acbE';
  /**
   * Programs that have been given a P classification by the Australian
   * Communications and Media Authority. These programs are intended for
   * preschool children.
   */
  public const ACB_RATING_acbP = 'acbP';
  /**
   * Programs that have been given a C classification by the Australian
   * Communications and Media Authority. These programs are intended for
   * children (other than preschool children) who are younger than 14 years of
   * age.
   */
  public const ACB_RATING_acbC = 'acbC';
  /**
   * G
   */
  public const ACB_RATING_acbG = 'acbG';
  /**
   * PG
   */
  public const ACB_RATING_acbPg = 'acbPg';
  /**
   * M
   */
  public const ACB_RATING_acbM = 'acbM';
  /**
   * MA15+
   */
  public const ACB_RATING_acbMa15plus = 'acbMa15plus';
  /**
   * R18+
   */
  public const ACB_RATING_acbR18plus = 'acbR18plus';
  public const ACB_RATING_acbUnrated = 'acbUnrated';
  public const AGCOM_RATING_agcomUnspecified = 'agcomUnspecified';
  /**
   * T
   */
  public const AGCOM_RATING_agcomT = 'agcomT';
  /**
   * VM14
   */
  public const AGCOM_RATING_agcomVm14 = 'agcomVm14';
  /**
   * VM18
   */
  public const AGCOM_RATING_agcomVm18 = 'agcomVm18';
  public const AGCOM_RATING_agcomUnrated = 'agcomUnrated';
  public const ANATEL_RATING_anatelUnspecified = 'anatelUnspecified';
  /**
   * F
   */
  public const ANATEL_RATING_anatelF = 'anatelF';
  /**
   * I
   */
  public const ANATEL_RATING_anatelI = 'anatelI';
  /**
   * I-7
   */
  public const ANATEL_RATING_anatelI7 = 'anatelI7';
  /**
   * I-10
   */
  public const ANATEL_RATING_anatelI10 = 'anatelI10';
  /**
   * I-12
   */
  public const ANATEL_RATING_anatelI12 = 'anatelI12';
  /**
   * R
   */
  public const ANATEL_RATING_anatelR = 'anatelR';
  /**
   * A
   */
  public const ANATEL_RATING_anatelA = 'anatelA';
  public const ANATEL_RATING_anatelUnrated = 'anatelUnrated';
  public const BBFC_RATING_bbfcUnspecified = 'bbfcUnspecified';
  /**
   * U
   */
  public const BBFC_RATING_bbfcU = 'bbfcU';
  /**
   * PG
   */
  public const BBFC_RATING_bbfcPg = 'bbfcPg';
  /**
   * 12A
   */
  public const BBFC_RATING_bbfc12a = 'bbfc12a';
  /**
   * 12
   */
  public const BBFC_RATING_bbfc12 = 'bbfc12';
  /**
   * 15
   */
  public const BBFC_RATING_bbfc15 = 'bbfc15';
  /**
   * 18
   */
  public const BBFC_RATING_bbfc18 = 'bbfc18';
  /**
   * R18
   */
  public const BBFC_RATING_bbfcR18 = 'bbfcR18';
  public const BBFC_RATING_bbfcUnrated = 'bbfcUnrated';
  public const BFVC_RATING_bfvcUnspecified = 'bfvcUnspecified';
  /**
   * G
   */
  public const BFVC_RATING_bfvcG = 'bfvcG';
  /**
   * E
   */
  public const BFVC_RATING_bfvcE = 'bfvcE';
  /**
   * 13
   */
  public const BFVC_RATING_bfvc13 = 'bfvc13';
  /**
   * 15
   */
  public const BFVC_RATING_bfvc15 = 'bfvc15';
  /**
   * 18
   */
  public const BFVC_RATING_bfvc18 = 'bfvc18';
  /**
   * 20
   */
  public const BFVC_RATING_bfvc20 = 'bfvc20';
  /**
   * B
   */
  public const BFVC_RATING_bfvcB = 'bfvcB';
  public const BFVC_RATING_bfvcUnrated = 'bfvcUnrated';
  public const BMUKK_RATING_bmukkUnspecified = 'bmukkUnspecified';
  /**
   * Unrestricted
   */
  public const BMUKK_RATING_bmukkAa = 'bmukkAa';
  /**
   * 6+
   */
  public const BMUKK_RATING_bmukk6 = 'bmukk6';
  /**
   * 8+
   */
  public const BMUKK_RATING_bmukk8 = 'bmukk8';
  /**
   * 10+
   */
  public const BMUKK_RATING_bmukk10 = 'bmukk10';
  /**
   * 12+
   */
  public const BMUKK_RATING_bmukk12 = 'bmukk12';
  /**
   * 14+
   */
  public const BMUKK_RATING_bmukk14 = 'bmukk14';
  /**
   * 16+
   */
  public const BMUKK_RATING_bmukk16 = 'bmukk16';
  public const BMUKK_RATING_bmukkUnrated = 'bmukkUnrated';
  public const CATV_RATING_catvUnspecified = 'catvUnspecified';
  /**
   * C
   */
  public const CATV_RATING_catvC = 'catvC';
  /**
   * C8
   */
  public const CATV_RATING_catvC8 = 'catvC8';
  /**
   * G
   */
  public const CATV_RATING_catvG = 'catvG';
  /**
   * PG
   */
  public const CATV_RATING_catvPg = 'catvPg';
  /**
   * 14+
   */
  public const CATV_RATING_catv14plus = 'catv14plus';
  /**
   * 18+
   */
  public const CATV_RATING_catv18plus = 'catv18plus';
  public const CATV_RATING_catvUnrated = 'catvUnrated';
  public const CATV_RATING_catvE = 'catvE';
  public const CATVFR_RATING_catvfrUnspecified = 'catvfrUnspecified';
  /**
   * G
   */
  public const CATVFR_RATING_catvfrG = 'catvfrG';
  /**
   * 8+
   */
  public const CATVFR_RATING_catvfr8plus = 'catvfr8plus';
  /**
   * 13+
   */
  public const CATVFR_RATING_catvfr13plus = 'catvfr13plus';
  /**
   * 16+
   */
  public const CATVFR_RATING_catvfr16plus = 'catvfr16plus';
  /**
   * 18+
   */
  public const CATVFR_RATING_catvfr18plus = 'catvfr18plus';
  public const CATVFR_RATING_catvfrUnrated = 'catvfrUnrated';
  public const CATVFR_RATING_catvfrE = 'catvfrE';
  public const CBFC_RATING_cbfcUnspecified = 'cbfcUnspecified';
  /**
   * U
   */
  public const CBFC_RATING_cbfcU = 'cbfcU';
  /**
   * U/A
   */
  public const CBFC_RATING_cbfcUA = 'cbfcUA';
  /**
   * U/A 7+
   */
  public const CBFC_RATING_cbfcUA7plus = 'cbfcUA7plus';
  /**
   * U/A 13+
   */
  public const CBFC_RATING_cbfcUA13plus = 'cbfcUA13plus';
  /**
   * U/A 16+
   */
  public const CBFC_RATING_cbfcUA16plus = 'cbfcUA16plus';
  /**
   * A
   */
  public const CBFC_RATING_cbfcA = 'cbfcA';
  /**
   * S
   */
  public const CBFC_RATING_cbfcS = 'cbfcS';
  public const CBFC_RATING_cbfcUnrated = 'cbfcUnrated';
  public const CCC_RATING_cccUnspecified = 'cccUnspecified';
  /**
   * Todo espectador
   */
  public const CCC_RATING_cccTe = 'cccTe';
  /**
   * 6+ - Inconveniente para menores de 7 años
   */
  public const CCC_RATING_ccc6 = 'ccc6';
  /**
   * 14+
   */
  public const CCC_RATING_ccc14 = 'ccc14';
  /**
   * 18+
   */
  public const CCC_RATING_ccc18 = 'ccc18';
  /**
   * 18+ - contenido excesivamente violento
   */
  public const CCC_RATING_ccc18v = 'ccc18v';
  /**
   * 18+ - contenido pornográfico
   */
  public const CCC_RATING_ccc18s = 'ccc18s';
  public const CCC_RATING_cccUnrated = 'cccUnrated';
  public const CCE_RATING_cceUnspecified = 'cceUnspecified';
  /**
   * 4
   */
  public const CCE_RATING_cceM4 = 'cceM4';
  /**
   * 6
   */
  public const CCE_RATING_cceM6 = 'cceM6';
  /**
   * 12
   */
  public const CCE_RATING_cceM12 = 'cceM12';
  /**
   * 16
   */
  public const CCE_RATING_cceM16 = 'cceM16';
  /**
   * 18
   */
  public const CCE_RATING_cceM18 = 'cceM18';
  public const CCE_RATING_cceUnrated = 'cceUnrated';
  /**
   * 14
   */
  public const CCE_RATING_cceM14 = 'cceM14';
  public const CHFILM_RATING_chfilmUnspecified = 'chfilmUnspecified';
  /**
   * 0
   */
  public const CHFILM_RATING_chfilm0 = 'chfilm0';
  /**
   * 6
   */
  public const CHFILM_RATING_chfilm6 = 'chfilm6';
  /**
   * 12
   */
  public const CHFILM_RATING_chfilm12 = 'chfilm12';
  /**
   * 16
   */
  public const CHFILM_RATING_chfilm16 = 'chfilm16';
  /**
   * 18
   */
  public const CHFILM_RATING_chfilm18 = 'chfilm18';
  public const CHFILM_RATING_chfilmUnrated = 'chfilmUnrated';
  public const CHVRS_RATING_chvrsUnspecified = 'chvrsUnspecified';
  /**
   * G
   */
  public const CHVRS_RATING_chvrsG = 'chvrsG';
  /**
   * PG
   */
  public const CHVRS_RATING_chvrsPg = 'chvrsPg';
  /**
   * 14A
   */
  public const CHVRS_RATING_chvrs14a = 'chvrs14a';
  /**
   * 18A
   */
  public const CHVRS_RATING_chvrs18a = 'chvrs18a';
  /**
   * R
   */
  public const CHVRS_RATING_chvrsR = 'chvrsR';
  /**
   * E
   */
  public const CHVRS_RATING_chvrsE = 'chvrsE';
  public const CHVRS_RATING_chvrsUnrated = 'chvrsUnrated';
  public const CICF_RATING_cicfUnspecified = 'cicfUnspecified';
  /**
   * E
   */
  public const CICF_RATING_cicfE = 'cicfE';
  /**
   * KT/EA
   */
  public const CICF_RATING_cicfKtEa = 'cicfKtEa';
  /**
   * KNT/ENA
   */
  public const CICF_RATING_cicfKntEna = 'cicfKntEna';
  public const CICF_RATING_cicfUnrated = 'cicfUnrated';
  public const CNA_RATING_cnaUnspecified = 'cnaUnspecified';
  /**
   * AP
   */
  public const CNA_RATING_cnaAp = 'cnaAp';
  /**
   * 12
   */
  public const CNA_RATING_cna12 = 'cna12';
  /**
   * 15
   */
  public const CNA_RATING_cna15 = 'cna15';
  /**
   * 18
   */
  public const CNA_RATING_cna18 = 'cna18';
  /**
   * 18+
   */
  public const CNA_RATING_cna18plus = 'cna18plus';
  public const CNA_RATING_cnaUnrated = 'cnaUnrated';
  public const CNC_RATING_cncUnspecified = 'cncUnspecified';
  /**
   * T
   */
  public const CNC_RATING_cncT = 'cncT';
  /**
   * 10
   */
  public const CNC_RATING_cnc10 = 'cnc10';
  /**
   * 12
   */
  public const CNC_RATING_cnc12 = 'cnc12';
  /**
   * 16
   */
  public const CNC_RATING_cnc16 = 'cnc16';
  /**
   * 18
   */
  public const CNC_RATING_cnc18 = 'cnc18';
  /**
   * E
   */
  public const CNC_RATING_cncE = 'cncE';
  /**
   * interdiction
   */
  public const CNC_RATING_cncInterdiction = 'cncInterdiction';
  public const CNC_RATING_cncUnrated = 'cncUnrated';
  public const CSA_RATING_csaUnspecified = 'csaUnspecified';
  /**
   * T
   */
  public const CSA_RATING_csaT = 'csaT';
  /**
   * 10
   */
  public const CSA_RATING_csa10 = 'csa10';
  /**
   * 12
   */
  public const CSA_RATING_csa12 = 'csa12';
  /**
   * 16
   */
  public const CSA_RATING_csa16 = 'csa16';
  /**
   * 18
   */
  public const CSA_RATING_csa18 = 'csa18';
  /**
   * Interdiction
   */
  public const CSA_RATING_csaInterdiction = 'csaInterdiction';
  public const CSA_RATING_csaUnrated = 'csaUnrated';
  public const CSCF_RATING_cscfUnspecified = 'cscfUnspecified';
  /**
   * AL
   */
  public const CSCF_RATING_cscfAl = 'cscfAl';
  /**
   * A
   */
  public const CSCF_RATING_cscfA = 'cscfA';
  /**
   * 6
   */
  public const CSCF_RATING_cscf6 = 'cscf6';
  /**
   * 9
   */
  public const CSCF_RATING_cscf9 = 'cscf9';
  /**
   * 12
   */
  public const CSCF_RATING_cscf12 = 'cscf12';
  /**
   * 16
   */
  public const CSCF_RATING_cscf16 = 'cscf16';
  /**
   * 18
   */
  public const CSCF_RATING_cscf18 = 'cscf18';
  public const CSCF_RATING_cscfUnrated = 'cscfUnrated';
  public const CZFILM_RATING_czfilmUnspecified = 'czfilmUnspecified';
  /**
   * U
   */
  public const CZFILM_RATING_czfilmU = 'czfilmU';
  /**
   * 12
   */
  public const CZFILM_RATING_czfilm12 = 'czfilm12';
  /**
   * 14
   */
  public const CZFILM_RATING_czfilm14 = 'czfilm14';
  /**
   * 18
   */
  public const CZFILM_RATING_czfilm18 = 'czfilm18';
  public const CZFILM_RATING_czfilmUnrated = 'czfilmUnrated';
  public const DJCTQ_RATING_djctqUnspecified = 'djctqUnspecified';
  /**
   * L
   */
  public const DJCTQ_RATING_djctqL = 'djctqL';
  /**
   * 10
   */
  public const DJCTQ_RATING_djctq10 = 'djctq10';
  /**
   * 12
   */
  public const DJCTQ_RATING_djctq12 = 'djctq12';
  /**
   * 14
   */
  public const DJCTQ_RATING_djctq14 = 'djctq14';
  /**
   * 16
   */
  public const DJCTQ_RATING_djctq16 = 'djctq16';
  /**
   * 18
   */
  public const DJCTQ_RATING_djctq18 = 'djctq18';
  public const DJCTQ_RATING_djctqEr = 'djctqEr';
  public const DJCTQ_RATING_djctqL10 = 'djctqL10';
  public const DJCTQ_RATING_djctqL12 = 'djctqL12';
  public const DJCTQ_RATING_djctqL14 = 'djctqL14';
  public const DJCTQ_RATING_djctqL16 = 'djctqL16';
  public const DJCTQ_RATING_djctqL18 = 'djctqL18';
  public const DJCTQ_RATING_djctq1012 = 'djctq1012';
  public const DJCTQ_RATING_djctq1014 = 'djctq1014';
  public const DJCTQ_RATING_djctq1016 = 'djctq1016';
  public const DJCTQ_RATING_djctq1018 = 'djctq1018';
  public const DJCTQ_RATING_djctq1214 = 'djctq1214';
  public const DJCTQ_RATING_djctq1216 = 'djctq1216';
  public const DJCTQ_RATING_djctq1218 = 'djctq1218';
  public const DJCTQ_RATING_djctq1416 = 'djctq1416';
  public const DJCTQ_RATING_djctq1418 = 'djctq1418';
  public const DJCTQ_RATING_djctq1618 = 'djctq1618';
  public const DJCTQ_RATING_djctqUnrated = 'djctqUnrated';
  public const ECBMCT_RATING_ecbmctUnspecified = 'ecbmctUnspecified';
  /**
   * G
   */
  public const ECBMCT_RATING_ecbmctG = 'ecbmctG';
  /**
   * 7A
   */
  public const ECBMCT_RATING_ecbmct7a = 'ecbmct7a';
  /**
   * 7+
   */
  public const ECBMCT_RATING_ecbmct7plus = 'ecbmct7plus';
  /**
   * 13A
   */
  public const ECBMCT_RATING_ecbmct13a = 'ecbmct13a';
  /**
   * 13+
   */
  public const ECBMCT_RATING_ecbmct13plus = 'ecbmct13plus';
  /**
   * 15A
   */
  public const ECBMCT_RATING_ecbmct15a = 'ecbmct15a';
  /**
   * 15+
   */
  public const ECBMCT_RATING_ecbmct15plus = 'ecbmct15plus';
  /**
   * 18+
   */
  public const ECBMCT_RATING_ecbmct18plus = 'ecbmct18plus';
  public const ECBMCT_RATING_ecbmctUnrated = 'ecbmctUnrated';
  public const EEFILM_RATING_eefilmUnspecified = 'eefilmUnspecified';
  /**
   * Pere
   */
  public const EEFILM_RATING_eefilmPere = 'eefilmPere';
  /**
   * L
   */
  public const EEFILM_RATING_eefilmL = 'eefilmL';
  /**
   * MS-6
   */
  public const EEFILM_RATING_eefilmMs6 = 'eefilmMs6';
  /**
   * K-6
   */
  public const EEFILM_RATING_eefilmK6 = 'eefilmK6';
  /**
   * MS-12
   */
  public const EEFILM_RATING_eefilmMs12 = 'eefilmMs12';
  /**
   * K-12
   */
  public const EEFILM_RATING_eefilmK12 = 'eefilmK12';
  /**
   * K-14
   */
  public const EEFILM_RATING_eefilmK14 = 'eefilmK14';
  /**
   * K-16
   */
  public const EEFILM_RATING_eefilmK16 = 'eefilmK16';
  public const EEFILM_RATING_eefilmUnrated = 'eefilmUnrated';
  public const EGFILM_RATING_egfilmUnspecified = 'egfilmUnspecified';
  /**
   * GN
   */
  public const EGFILM_RATING_egfilmGn = 'egfilmGn';
  /**
   * 18
   */
  public const EGFILM_RATING_egfilm18 = 'egfilm18';
  /**
   * BN
   */
  public const EGFILM_RATING_egfilmBn = 'egfilmBn';
  public const EGFILM_RATING_egfilmUnrated = 'egfilmUnrated';
  public const EIRIN_RATING_eirinUnspecified = 'eirinUnspecified';
  /**
   * G
   */
  public const EIRIN_RATING_eirinG = 'eirinG';
  /**
   * PG-12
   */
  public const EIRIN_RATING_eirinPg12 = 'eirinPg12';
  /**
   * R15+
   */
  public const EIRIN_RATING_eirinR15plus = 'eirinR15plus';
  /**
   * R18+
   */
  public const EIRIN_RATING_eirinR18plus = 'eirinR18plus';
  public const EIRIN_RATING_eirinUnrated = 'eirinUnrated';
  public const FCBM_RATING_fcbmUnspecified = 'fcbmUnspecified';
  /**
   * U
   */
  public const FCBM_RATING_fcbmU = 'fcbmU';
  /**
   * PG13
   */
  public const FCBM_RATING_fcbmPg13 = 'fcbmPg13';
  /**
   * P13
   */
  public const FCBM_RATING_fcbmP13 = 'fcbmP13';
  /**
   * 18
   */
  public const FCBM_RATING_fcbm18 = 'fcbm18';
  /**
   * 18SX
   */
  public const FCBM_RATING_fcbm18sx = 'fcbm18sx';
  /**
   * 18PA
   */
  public const FCBM_RATING_fcbm18pa = 'fcbm18pa';
  /**
   * 18SG
   */
  public const FCBM_RATING_fcbm18sg = 'fcbm18sg';
  /**
   * 18PL
   */
  public const FCBM_RATING_fcbm18pl = 'fcbm18pl';
  public const FCBM_RATING_fcbmUnrated = 'fcbmUnrated';
  public const FCO_RATING_fcoUnspecified = 'fcoUnspecified';
  /**
   * I
   */
  public const FCO_RATING_fcoI = 'fcoI';
  /**
   * IIA
   */
  public const FCO_RATING_fcoIia = 'fcoIia';
  /**
   * IIB
   */
  public const FCO_RATING_fcoIib = 'fcoIib';
  /**
   * II
   */
  public const FCO_RATING_fcoIi = 'fcoIi';
  /**
   * III
   */
  public const FCO_RATING_fcoIii = 'fcoIii';
  public const FCO_RATING_fcoUnrated = 'fcoUnrated';
  public const FMOC_RATING_fmocUnspecified = 'fmocUnspecified';
  /**
   * U
   */
  public const FMOC_RATING_fmocU = 'fmocU';
  /**
   * 10
   */
  public const FMOC_RATING_fmoc10 = 'fmoc10';
  /**
   * 12
   */
  public const FMOC_RATING_fmoc12 = 'fmoc12';
  /**
   * 16
   */
  public const FMOC_RATING_fmoc16 = 'fmoc16';
  /**
   * 18
   */
  public const FMOC_RATING_fmoc18 = 'fmoc18';
  /**
   * E
   */
  public const FMOC_RATING_fmocE = 'fmocE';
  public const FMOC_RATING_fmocUnrated = 'fmocUnrated';
  public const FPB_RATING_fpbUnspecified = 'fpbUnspecified';
  /**
   * A
   */
  public const FPB_RATING_fpbA = 'fpbA';
  /**
   * PG
   */
  public const FPB_RATING_fpbPg = 'fpbPg';
  /**
   * 7-9PG
   */
  public const FPB_RATING_fpb79Pg = 'fpb79Pg';
  /**
   * 10-12PG
   */
  public const FPB_RATING_fpb1012Pg = 'fpb1012Pg';
  /**
   * 13
   */
  public const FPB_RATING_fpb13 = 'fpb13';
  /**
   * 16
   */
  public const FPB_RATING_fpb16 = 'fpb16';
  /**
   * 18
   */
  public const FPB_RATING_fpb18 = 'fpb18';
  /**
   * X18
   */
  public const FPB_RATING_fpbX18 = 'fpbX18';
  /**
   * XX
   */
  public const FPB_RATING_fpbXx = 'fpbXx';
  public const FPB_RATING_fpbUnrated = 'fpbUnrated';
  /**
   * 10
   */
  public const FPB_RATING_fpb10 = 'fpb10';
  public const FSK_RATING_fskUnspecified = 'fskUnspecified';
  /**
   * FSK 0
   */
  public const FSK_RATING_fsk0 = 'fsk0';
  /**
   * FSK 6
   */
  public const FSK_RATING_fsk6 = 'fsk6';
  /**
   * FSK 12
   */
  public const FSK_RATING_fsk12 = 'fsk12';
  /**
   * FSK 16
   */
  public const FSK_RATING_fsk16 = 'fsk16';
  /**
   * FSK 18
   */
  public const FSK_RATING_fsk18 = 'fsk18';
  public const FSK_RATING_fskUnrated = 'fskUnrated';
  public const GRFILM_RATING_grfilmUnspecified = 'grfilmUnspecified';
  /**
   * K
   */
  public const GRFILM_RATING_grfilmK = 'grfilmK';
  /**
   * E
   */
  public const GRFILM_RATING_grfilmE = 'grfilmE';
  /**
   * K-12
   */
  public const GRFILM_RATING_grfilmK12 = 'grfilmK12';
  /**
   * K-13
   */
  public const GRFILM_RATING_grfilmK13 = 'grfilmK13';
  /**
   * K-15
   */
  public const GRFILM_RATING_grfilmK15 = 'grfilmK15';
  /**
   * K-17
   */
  public const GRFILM_RATING_grfilmK17 = 'grfilmK17';
  /**
   * K-18
   */
  public const GRFILM_RATING_grfilmK18 = 'grfilmK18';
  public const GRFILM_RATING_grfilmUnrated = 'grfilmUnrated';
  public const ICAA_RATING_icaaUnspecified = 'icaaUnspecified';
  /**
   * APTA
   */
  public const ICAA_RATING_icaaApta = 'icaaApta';
  /**
   * 7
   */
  public const ICAA_RATING_icaa7 = 'icaa7';
  /**
   * 12
   */
  public const ICAA_RATING_icaa12 = 'icaa12';
  /**
   * 13
   */
  public const ICAA_RATING_icaa13 = 'icaa13';
  /**
   * 16
   */
  public const ICAA_RATING_icaa16 = 'icaa16';
  /**
   * 18
   */
  public const ICAA_RATING_icaa18 = 'icaa18';
  /**
   * X
   */
  public const ICAA_RATING_icaaX = 'icaaX';
  public const ICAA_RATING_icaaUnrated = 'icaaUnrated';
  public const IFCO_RATING_ifcoUnspecified = 'ifcoUnspecified';
  /**
   * G
   */
  public const IFCO_RATING_ifcoG = 'ifcoG';
  /**
   * PG
   */
  public const IFCO_RATING_ifcoPg = 'ifcoPg';
  /**
   * 12
   */
  public const IFCO_RATING_ifco12 = 'ifco12';
  /**
   * 12A
   */
  public const IFCO_RATING_ifco12a = 'ifco12a';
  /**
   * 15
   */
  public const IFCO_RATING_ifco15 = 'ifco15';
  /**
   * 15A
   */
  public const IFCO_RATING_ifco15a = 'ifco15a';
  /**
   * 16
   */
  public const IFCO_RATING_ifco16 = 'ifco16';
  /**
   * 18
   */
  public const IFCO_RATING_ifco18 = 'ifco18';
  public const IFCO_RATING_ifcoUnrated = 'ifcoUnrated';
  public const ILFILM_RATING_ilfilmUnspecified = 'ilfilmUnspecified';
  /**
   * AA
   */
  public const ILFILM_RATING_ilfilmAa = 'ilfilmAa';
  /**
   * 12
   */
  public const ILFILM_RATING_ilfilm12 = 'ilfilm12';
  /**
   * 14
   */
  public const ILFILM_RATING_ilfilm14 = 'ilfilm14';
  /**
   * 16
   */
  public const ILFILM_RATING_ilfilm16 = 'ilfilm16';
  /**
   * 18
   */
  public const ILFILM_RATING_ilfilm18 = 'ilfilm18';
  public const ILFILM_RATING_ilfilmUnrated = 'ilfilmUnrated';
  public const INCAA_RATING_incaaUnspecified = 'incaaUnspecified';
  /**
   * ATP (Apta para todo publico)
   */
  public const INCAA_RATING_incaaAtp = 'incaaAtp';
  /**
   * 13 (Solo apta para mayores de 13 años)
   */
  public const INCAA_RATING_incaaSam13 = 'incaaSam13';
  /**
   * 16 (Solo apta para mayores de 16 años)
   */
  public const INCAA_RATING_incaaSam16 = 'incaaSam16';
  /**
   * 18 (Solo apta para mayores de 18 años)
   */
  public const INCAA_RATING_incaaSam18 = 'incaaSam18';
  /**
   * X (Solo apta para mayores de 18 años, de exhibición condicionada)
   */
  public const INCAA_RATING_incaaC = 'incaaC';
  public const INCAA_RATING_incaaUnrated = 'incaaUnrated';
  public const KFCB_RATING_kfcbUnspecified = 'kfcbUnspecified';
  /**
   * GE
   */
  public const KFCB_RATING_kfcbG = 'kfcbG';
  /**
   * PG
   */
  public const KFCB_RATING_kfcbPg = 'kfcbPg';
  /**
   * 16
   */
  public const KFCB_RATING_kfcb16plus = 'kfcb16plus';
  /**
   * 18
   */
  public const KFCB_RATING_kfcbR = 'kfcbR';
  public const KFCB_RATING_kfcbUnrated = 'kfcbUnrated';
  public const KIJKWIJZER_RATING_kijkwijzerUnspecified = 'kijkwijzerUnspecified';
  /**
   * AL
   */
  public const KIJKWIJZER_RATING_kijkwijzerAl = 'kijkwijzerAl';
  /**
   * 6
   */
  public const KIJKWIJZER_RATING_kijkwijzer6 = 'kijkwijzer6';
  /**
   * 9
   */
  public const KIJKWIJZER_RATING_kijkwijzer9 = 'kijkwijzer9';
  /**
   * 12
   */
  public const KIJKWIJZER_RATING_kijkwijzer12 = 'kijkwijzer12';
  /**
   * 16
   */
  public const KIJKWIJZER_RATING_kijkwijzer16 = 'kijkwijzer16';
  public const KIJKWIJZER_RATING_kijkwijzer18 = 'kijkwijzer18';
  public const KIJKWIJZER_RATING_kijkwijzerUnrated = 'kijkwijzerUnrated';
  public const KMRB_RATING_kmrbUnspecified = 'kmrbUnspecified';
  /**
   * 전체관람가
   */
  public const KMRB_RATING_kmrbAll = 'kmrbAll';
  /**
   * 12세 이상 관람가
   */
  public const KMRB_RATING_kmrb12plus = 'kmrb12plus';
  /**
   * 15세 이상 관람가
   */
  public const KMRB_RATING_kmrb15plus = 'kmrb15plus';
  public const KMRB_RATING_kmrbTeenr = 'kmrbTeenr';
  /**
   * 청소년 관람불가
   */
  public const KMRB_RATING_kmrbR = 'kmrbR';
  public const KMRB_RATING_kmrbUnrated = 'kmrbUnrated';
  public const LSF_RATING_lsfUnspecified = 'lsfUnspecified';
  /**
   * SU
   */
  public const LSF_RATING_lsfSu = 'lsfSu';
  /**
   * A
   */
  public const LSF_RATING_lsfA = 'lsfA';
  /**
   * BO
   *
   * @deprecated
   */
  public const LSF_RATING_lsfBo = 'lsfBo';
  /**
   * 13
   */
  public const LSF_RATING_lsf13 = 'lsf13';
  /**
   * R
   *
   * @deprecated
   */
  public const LSF_RATING_lsfR = 'lsfR';
  /**
   * 17
   */
  public const LSF_RATING_lsf17 = 'lsf17';
  /**
   * D
   *
   * @deprecated
   */
  public const LSF_RATING_lsfD = 'lsfD';
  /**
   * 21
   */
  public const LSF_RATING_lsf21 = 'lsf21';
  /**
   * @deprecated
   */
  public const LSF_RATING_lsfUnrated = 'lsfUnrated';
  public const MCCAA_RATING_mccaaUnspecified = 'mccaaUnspecified';
  /**
   * U
   */
  public const MCCAA_RATING_mccaaU = 'mccaaU';
  /**
   * PG
   */
  public const MCCAA_RATING_mccaaPg = 'mccaaPg';
  /**
   * 12A
   */
  public const MCCAA_RATING_mccaa12a = 'mccaa12a';
  /**
   * 12
   */
  public const MCCAA_RATING_mccaa12 = 'mccaa12';
  /**
   * 14 - this rating was removed from the new classification structure
   * introduced in 2013.
   */
  public const MCCAA_RATING_mccaa14 = 'mccaa14';
  /**
   * 15
   */
  public const MCCAA_RATING_mccaa15 = 'mccaa15';
  /**
   * 16 - this rating was removed from the new classification structure
   * introduced in 2013.
   */
  public const MCCAA_RATING_mccaa16 = 'mccaa16';
  /**
   * 18
   */
  public const MCCAA_RATING_mccaa18 = 'mccaa18';
  public const MCCAA_RATING_mccaaUnrated = 'mccaaUnrated';
  public const MCCYP_RATING_mccypUnspecified = 'mccypUnspecified';
  /**
   * A
   */
  public const MCCYP_RATING_mccypA = 'mccypA';
  /**
   * 7
   */
  public const MCCYP_RATING_mccyp7 = 'mccyp7';
  /**
   * 11
   */
  public const MCCYP_RATING_mccyp11 = 'mccyp11';
  /**
   * 15
   */
  public const MCCYP_RATING_mccyp15 = 'mccyp15';
  public const MCCYP_RATING_mccypUnrated = 'mccypUnrated';
  public const MCST_RATING_mcstUnspecified = 'mcstUnspecified';
  /**
   * P
   */
  public const MCST_RATING_mcstP = 'mcstP';
  /**
   * 0
   */
  public const MCST_RATING_mcst0 = 'mcst0';
  /**
   * C13
   */
  public const MCST_RATING_mcstC13 = 'mcstC13';
  /**
   * C16
   */
  public const MCST_RATING_mcstC16 = 'mcstC16';
  /**
   * 16+
   */
  public const MCST_RATING_mcst16plus = 'mcst16plus';
  /**
   * C18
   */
  public const MCST_RATING_mcstC18 = 'mcstC18';
  /**
   * MCST_G_PG
   */
  public const MCST_RATING_mcstGPg = 'mcstGPg';
  public const MCST_RATING_mcstUnrated = 'mcstUnrated';
  public const MDA_RATING_mdaUnspecified = 'mdaUnspecified';
  /**
   * G
   */
  public const MDA_RATING_mdaG = 'mdaG';
  /**
   * PG
   */
  public const MDA_RATING_mdaPg = 'mdaPg';
  /**
   * PG13
   */
  public const MDA_RATING_mdaPg13 = 'mdaPg13';
  /**
   * NC16
   */
  public const MDA_RATING_mdaNc16 = 'mdaNc16';
  /**
   * M18
   */
  public const MDA_RATING_mdaM18 = 'mdaM18';
  /**
   * R21
   */
  public const MDA_RATING_mdaR21 = 'mdaR21';
  public const MDA_RATING_mdaUnrated = 'mdaUnrated';
  public const MEDIETILSYNET_RATING_medietilsynetUnspecified = 'medietilsynetUnspecified';
  /**
   * A
   */
  public const MEDIETILSYNET_RATING_medietilsynetA = 'medietilsynetA';
  /**
   * 6
   */
  public const MEDIETILSYNET_RATING_medietilsynet6 = 'medietilsynet6';
  /**
   * 7
   */
  public const MEDIETILSYNET_RATING_medietilsynet7 = 'medietilsynet7';
  /**
   * 9
   */
  public const MEDIETILSYNET_RATING_medietilsynet9 = 'medietilsynet9';
  /**
   * 11
   */
  public const MEDIETILSYNET_RATING_medietilsynet11 = 'medietilsynet11';
  /**
   * 12
   */
  public const MEDIETILSYNET_RATING_medietilsynet12 = 'medietilsynet12';
  /**
   * 15
   */
  public const MEDIETILSYNET_RATING_medietilsynet15 = 'medietilsynet15';
  /**
   * 18
   */
  public const MEDIETILSYNET_RATING_medietilsynet18 = 'medietilsynet18';
  public const MEDIETILSYNET_RATING_medietilsynetUnrated = 'medietilsynetUnrated';
  public const MEKU_RATING_mekuUnspecified = 'mekuUnspecified';
  /**
   * S
   */
  public const MEKU_RATING_mekuS = 'mekuS';
  /**
   * 7
   */
  public const MEKU_RATING_meku7 = 'meku7';
  /**
   * 12
   */
  public const MEKU_RATING_meku12 = 'meku12';
  /**
   * 16
   */
  public const MEKU_RATING_meku16 = 'meku16';
  /**
   * 18
   */
  public const MEKU_RATING_meku18 = 'meku18';
  public const MEKU_RATING_mekuUnrated = 'mekuUnrated';
  public const MENA_MPAA_RATING_menaMpaaUnspecified = 'menaMpaaUnspecified';
  /**
   * G
   */
  public const MENA_MPAA_RATING_menaMpaaG = 'menaMpaaG';
  /**
   * PG
   */
  public const MENA_MPAA_RATING_menaMpaaPg = 'menaMpaaPg';
  /**
   * PG-13
   */
  public const MENA_MPAA_RATING_menaMpaaPg13 = 'menaMpaaPg13';
  /**
   * R
   */
  public const MENA_MPAA_RATING_menaMpaaR = 'menaMpaaR';
  /**
   * To keep the same enum values as MPAA's items have, skip NC_17.
   */
  public const MENA_MPAA_RATING_menaMpaaUnrated = 'menaMpaaUnrated';
  public const MIBAC_RATING_mibacUnspecified = 'mibacUnspecified';
  public const MIBAC_RATING_mibacT = 'mibacT';
  public const MIBAC_RATING_mibacVap = 'mibacVap';
  public const MIBAC_RATING_mibacVm6 = 'mibacVm6';
  public const MIBAC_RATING_mibacVm12 = 'mibacVm12';
  public const MIBAC_RATING_mibacVm14 = 'mibacVm14';
  public const MIBAC_RATING_mibacVm16 = 'mibacVm16';
  public const MIBAC_RATING_mibacVm18 = 'mibacVm18';
  public const MIBAC_RATING_mibacUnrated = 'mibacUnrated';
  public const MOC_RATING_mocUnspecified = 'mocUnspecified';
  /**
   * E
   */
  public const MOC_RATING_mocE = 'mocE';
  /**
   * T
   */
  public const MOC_RATING_mocT = 'mocT';
  /**
   * 7
   */
  public const MOC_RATING_moc7 = 'moc7';
  /**
   * 12
   */
  public const MOC_RATING_moc12 = 'moc12';
  /**
   * 15
   */
  public const MOC_RATING_moc15 = 'moc15';
  /**
   * 18
   */
  public const MOC_RATING_moc18 = 'moc18';
  /**
   * X
   */
  public const MOC_RATING_mocX = 'mocX';
  /**
   * Banned
   */
  public const MOC_RATING_mocBanned = 'mocBanned';
  public const MOC_RATING_mocUnrated = 'mocUnrated';
  public const MOCTW_RATING_moctwUnspecified = 'moctwUnspecified';
  /**
   * G
   */
  public const MOCTW_RATING_moctwG = 'moctwG';
  /**
   * P
   */
  public const MOCTW_RATING_moctwP = 'moctwP';
  /**
   * PG
   */
  public const MOCTW_RATING_moctwPg = 'moctwPg';
  /**
   * R
   */
  public const MOCTW_RATING_moctwR = 'moctwR';
  public const MOCTW_RATING_moctwUnrated = 'moctwUnrated';
  /**
   * R-12
   */
  public const MOCTW_RATING_moctwR12 = 'moctwR12';
  /**
   * R-15
   */
  public const MOCTW_RATING_moctwR15 = 'moctwR15';
  public const MPAA_RATING_mpaaUnspecified = 'mpaaUnspecified';
  /**
   * G
   */
  public const MPAA_RATING_mpaaG = 'mpaaG';
  /**
   * PG
   */
  public const MPAA_RATING_mpaaPg = 'mpaaPg';
  /**
   * PG-13
   */
  public const MPAA_RATING_mpaaPg13 = 'mpaaPg13';
  /**
   * R
   */
  public const MPAA_RATING_mpaaR = 'mpaaR';
  /**
   * NC-17
   */
  public const MPAA_RATING_mpaaNc17 = 'mpaaNc17';
  /**
   * ! X
   */
  public const MPAA_RATING_mpaaX = 'mpaaX';
  public const MPAA_RATING_mpaaUnrated = 'mpaaUnrated';
  public const MPAAT_RATING_mpaatUnspecified = 'mpaatUnspecified';
  /**
   * GB
   */
  public const MPAAT_RATING_mpaatGb = 'mpaatGb';
  /**
   * RB
   */
  public const MPAAT_RATING_mpaatRb = 'mpaatRb';
  public const MTRCB_RATING_mtrcbUnspecified = 'mtrcbUnspecified';
  /**
   * G
   */
  public const MTRCB_RATING_mtrcbG = 'mtrcbG';
  /**
   * PG
   */
  public const MTRCB_RATING_mtrcbPg = 'mtrcbPg';
  /**
   * R-13
   */
  public const MTRCB_RATING_mtrcbR13 = 'mtrcbR13';
  /**
   * R-16
   */
  public const MTRCB_RATING_mtrcbR16 = 'mtrcbR16';
  /**
   * R-18
   */
  public const MTRCB_RATING_mtrcbR18 = 'mtrcbR18';
  /**
   * X
   */
  public const MTRCB_RATING_mtrcbX = 'mtrcbX';
  public const MTRCB_RATING_mtrcbUnrated = 'mtrcbUnrated';
  public const NBC_RATING_nbcUnspecified = 'nbcUnspecified';
  /**
   * G
   */
  public const NBC_RATING_nbcG = 'nbcG';
  /**
   * PG
   */
  public const NBC_RATING_nbcPg = 'nbcPg';
  /**
   * 12+
   */
  public const NBC_RATING_nbc12plus = 'nbc12plus';
  /**
   * 15+
   */
  public const NBC_RATING_nbc15plus = 'nbc15plus';
  /**
   * 18+
   */
  public const NBC_RATING_nbc18plus = 'nbc18plus';
  /**
   * 18+R
   */
  public const NBC_RATING_nbc18plusr = 'nbc18plusr';
  /**
   * PU
   */
  public const NBC_RATING_nbcPu = 'nbcPu';
  public const NBC_RATING_nbcUnrated = 'nbcUnrated';
  public const NBCPL_RATING_nbcplUnspecified = 'nbcplUnspecified';
  public const NBCPL_RATING_nbcplI = 'nbcplI';
  public const NBCPL_RATING_nbcplIi = 'nbcplIi';
  public const NBCPL_RATING_nbcplIii = 'nbcplIii';
  public const NBCPL_RATING_nbcplIv = 'nbcplIv';
  public const NBCPL_RATING_nbcpl18plus = 'nbcpl18plus';
  public const NBCPL_RATING_nbcplUnrated = 'nbcplUnrated';
  public const NFRC_RATING_nfrcUnspecified = 'nfrcUnspecified';
  /**
   * A
   */
  public const NFRC_RATING_nfrcA = 'nfrcA';
  /**
   * B
   */
  public const NFRC_RATING_nfrcB = 'nfrcB';
  /**
   * C
   */
  public const NFRC_RATING_nfrcC = 'nfrcC';
  /**
   * D
   */
  public const NFRC_RATING_nfrcD = 'nfrcD';
  /**
   * X
   */
  public const NFRC_RATING_nfrcX = 'nfrcX';
  public const NFRC_RATING_nfrcUnrated = 'nfrcUnrated';
  public const NFVCB_RATING_nfvcbUnspecified = 'nfvcbUnspecified';
  /**
   * G
   */
  public const NFVCB_RATING_nfvcbG = 'nfvcbG';
  /**
   * PG
   */
  public const NFVCB_RATING_nfvcbPg = 'nfvcbPg';
  /**
   * 12
   */
  public const NFVCB_RATING_nfvcb12 = 'nfvcb12';
  /**
   * 12A
   */
  public const NFVCB_RATING_nfvcb12a = 'nfvcb12a';
  /**
   * 15
   */
  public const NFVCB_RATING_nfvcb15 = 'nfvcb15';
  /**
   * 18
   */
  public const NFVCB_RATING_nfvcb18 = 'nfvcb18';
  /**
   * RE
   */
  public const NFVCB_RATING_nfvcbRe = 'nfvcbRe';
  public const NFVCB_RATING_nfvcbUnrated = 'nfvcbUnrated';
  public const NKCLV_RATING_nkclvUnspecified = 'nkclvUnspecified';
  /**
   * U
   */
  public const NKCLV_RATING_nkclvU = 'nkclvU';
  /**
   * 7+
   */
  public const NKCLV_RATING_nkclv7plus = 'nkclv7plus';
  /**
   * 12+
   */
  public const NKCLV_RATING_nkclv12plus = 'nkclv12plus';
  /**
   * ! 16+
   */
  public const NKCLV_RATING_nkclv16plus = 'nkclv16plus';
  /**
   * 18+
   */
  public const NKCLV_RATING_nkclv18plus = 'nkclv18plus';
  public const NKCLV_RATING_nkclvUnrated = 'nkclvUnrated';
  public const NMC_RATING_nmcUnspecified = 'nmcUnspecified';
  /**
   * G
   */
  public const NMC_RATING_nmcG = 'nmcG';
  /**
   * PG
   */
  public const NMC_RATING_nmcPg = 'nmcPg';
  /**
   * PG-13
   */
  public const NMC_RATING_nmcPg13 = 'nmcPg13';
  /**
   * PG-15
   */
  public const NMC_RATING_nmcPg15 = 'nmcPg15';
  /**
   * 15+
   */
  public const NMC_RATING_nmc15plus = 'nmc15plus';
  /**
   * 18+
   */
  public const NMC_RATING_nmc18plus = 'nmc18plus';
  /**
   * 18TC
   */
  public const NMC_RATING_nmc18tc = 'nmc18tc';
  public const NMC_RATING_nmcUnrated = 'nmcUnrated';
  public const OFLC_RATING_oflcUnspecified = 'oflcUnspecified';
  /**
   * G
   */
  public const OFLC_RATING_oflcG = 'oflcG';
  /**
   * PG
   */
  public const OFLC_RATING_oflcPg = 'oflcPg';
  /**
   * M
   */
  public const OFLC_RATING_oflcM = 'oflcM';
  /**
   * R13
   */
  public const OFLC_RATING_oflcR13 = 'oflcR13';
  /**
   * R15
   */
  public const OFLC_RATING_oflcR15 = 'oflcR15';
  /**
   * R16
   */
  public const OFLC_RATING_oflcR16 = 'oflcR16';
  /**
   * R18
   */
  public const OFLC_RATING_oflcR18 = 'oflcR18';
  public const OFLC_RATING_oflcUnrated = 'oflcUnrated';
  /**
   * RP13
   */
  public const OFLC_RATING_oflcRp13 = 'oflcRp13';
  /**
   * RP16
   */
  public const OFLC_RATING_oflcRp16 = 'oflcRp16';
  /**
   * RP18
   */
  public const OFLC_RATING_oflcRp18 = 'oflcRp18';
  public const PEFILM_RATING_pefilmUnspecified = 'pefilmUnspecified';
  /**
   * PT
   */
  public const PEFILM_RATING_pefilmPt = 'pefilmPt';
  /**
   * PG
   */
  public const PEFILM_RATING_pefilmPg = 'pefilmPg';
  /**
   * 14
   */
  public const PEFILM_RATING_pefilm14 = 'pefilm14';
  /**
   * 18
   */
  public const PEFILM_RATING_pefilm18 = 'pefilm18';
  public const PEFILM_RATING_pefilmUnrated = 'pefilmUnrated';
  public const RCNOF_RATING_rcnofUnspecified = 'rcnofUnspecified';
  public const RCNOF_RATING_rcnofI = 'rcnofI';
  public const RCNOF_RATING_rcnofIi = 'rcnofIi';
  public const RCNOF_RATING_rcnofIii = 'rcnofIii';
  public const RCNOF_RATING_rcnofIv = 'rcnofIv';
  public const RCNOF_RATING_rcnofV = 'rcnofV';
  public const RCNOF_RATING_rcnofVi = 'rcnofVi';
  public const RCNOF_RATING_rcnofUnrated = 'rcnofUnrated';
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaUnspecified = 'resorteviolenciaUnspecified';
  /**
   * A
   */
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaA = 'resorteviolenciaA';
  /**
   * B
   */
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaB = 'resorteviolenciaB';
  /**
   * C
   */
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaC = 'resorteviolenciaC';
  /**
   * D
   */
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaD = 'resorteviolenciaD';
  /**
   * E
   */
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaE = 'resorteviolenciaE';
  public const RESORTEVIOLENCIA_RATING_resorteviolenciaUnrated = 'resorteviolenciaUnrated';
  public const RTC_RATING_rtcUnspecified = 'rtcUnspecified';
  /**
   * AA
   */
  public const RTC_RATING_rtcAa = 'rtcAa';
  /**
   * A
   */
  public const RTC_RATING_rtcA = 'rtcA';
  /**
   * B
   */
  public const RTC_RATING_rtcB = 'rtcB';
  /**
   * B15
   */
  public const RTC_RATING_rtcB15 = 'rtcB15';
  /**
   * C
   */
  public const RTC_RATING_rtcC = 'rtcC';
  /**
   * D
   */
  public const RTC_RATING_rtcD = 'rtcD';
  public const RTC_RATING_rtcUnrated = 'rtcUnrated';
  public const RTE_RATING_rteUnspecified = 'rteUnspecified';
  /**
   * GA
   */
  public const RTE_RATING_rteGa = 'rteGa';
  /**
   * CH
   */
  public const RTE_RATING_rteCh = 'rteCh';
  /**
   * PS
   */
  public const RTE_RATING_rtePs = 'rtePs';
  /**
   * MA
   */
  public const RTE_RATING_rteMa = 'rteMa';
  public const RTE_RATING_rteUnrated = 'rteUnrated';
  public const RUSSIA_RATING_russiaUnspecified = 'russiaUnspecified';
  /**
   * 0+
   */
  public const RUSSIA_RATING_russia0 = 'russia0';
  /**
   * 6+
   */
  public const RUSSIA_RATING_russia6 = 'russia6';
  /**
   * 12+
   */
  public const RUSSIA_RATING_russia12 = 'russia12';
  /**
   * 16+
   */
  public const RUSSIA_RATING_russia16 = 'russia16';
  /**
   * 18+
   */
  public const RUSSIA_RATING_russia18 = 'russia18';
  public const RUSSIA_RATING_russiaUnrated = 'russiaUnrated';
  public const SKFILM_RATING_skfilmUnspecified = 'skfilmUnspecified';
  /**
   * G
   */
  public const SKFILM_RATING_skfilmG = 'skfilmG';
  /**
   * P2
   */
  public const SKFILM_RATING_skfilmP2 = 'skfilmP2';
  /**
   * P5
   */
  public const SKFILM_RATING_skfilmP5 = 'skfilmP5';
  /**
   * P8
   */
  public const SKFILM_RATING_skfilmP8 = 'skfilmP8';
  public const SKFILM_RATING_skfilmUnrated = 'skfilmUnrated';
  public const SMAIS_RATING_smaisUnspecified = 'smaisUnspecified';
  /**
   * L
   */
  public const SMAIS_RATING_smaisL = 'smaisL';
  /**
   * 7
   */
  public const SMAIS_RATING_smais7 = 'smais7';
  /**
   * 12
   */
  public const SMAIS_RATING_smais12 = 'smais12';
  /**
   * 14
   */
  public const SMAIS_RATING_smais14 = 'smais14';
  /**
   * 16
   */
  public const SMAIS_RATING_smais16 = 'smais16';
  /**
   * 18
   */
  public const SMAIS_RATING_smais18 = 'smais18';
  public const SMAIS_RATING_smaisUnrated = 'smaisUnrated';
  public const SMSA_RATING_smsaUnspecified = 'smsaUnspecified';
  /**
   * All ages
   */
  public const SMSA_RATING_smsaA = 'smsaA';
  /**
   * 7
   */
  public const SMSA_RATING_smsa7 = 'smsa7';
  /**
   * 11
   */
  public const SMSA_RATING_smsa11 = 'smsa11';
  /**
   * 15
   */
  public const SMSA_RATING_smsa15 = 'smsa15';
  public const SMSA_RATING_smsaUnrated = 'smsaUnrated';
  public const TVPG_RATING_tvpgUnspecified = 'tvpgUnspecified';
  /**
   * TV-Y
   */
  public const TVPG_RATING_tvpgY = 'tvpgY';
  /**
   * TV-Y7
   */
  public const TVPG_RATING_tvpgY7 = 'tvpgY7';
  /**
   * TV-Y7-FV
   */
  public const TVPG_RATING_tvpgY7Fv = 'tvpgY7Fv';
  /**
   * TV-G
   */
  public const TVPG_RATING_tvpgG = 'tvpgG';
  /**
   * TV-PG
   */
  public const TVPG_RATING_tvpgPg = 'tvpgPg';
  /**
   * TV-14
   */
  public const TVPG_RATING_pg14 = 'pg14';
  /**
   * TV-MA
   */
  public const TVPG_RATING_tvpgMa = 'tvpgMa';
  public const TVPG_RATING_tvpgUnrated = 'tvpgUnrated';
  public const YT_RATING_ytUnspecified = 'ytUnspecified';
  public const YT_RATING_ytAgeRestricted = 'ytAgeRestricted';
  protected $collection_key = 'fpbRatingReasons';
  /**
   * The video's Australian Classification Board (ACB) or Australian
   * Communications and Media Authority (ACMA) rating. ACMA ratings are used to
   * classify children's television programming.
   *
   * @var string
   */
  public $acbRating;
  /**
   * The video's rating from Italy's Autorità per le Garanzie nelle
   * Comunicazioni (AGCOM).
   *
   * @var string
   */
  public $agcomRating;
  /**
   * The video's Anatel (Asociación Nacional de Televisión) rating for Chilean
   * television.
   *
   * @var string
   */
  public $anatelRating;
  /**
   * The video's British Board of Film Classification (BBFC) rating.
   *
   * @var string
   */
  public $bbfcRating;
  /**
   * The video's rating from Thailand's Board of Film and Video Censors.
   *
   * @var string
   */
  public $bfvcRating;
  /**
   * The video's rating from the Austrian Board of Media Classification
   * (Bundesministerium für Unterricht, Kunst und Kultur).
   *
   * @var string
   */
  public $bmukkRating;
  /**
   * Rating system for Canadian TV - Canadian TV Classification System The
   * video's rating from the Canadian Radio-Television and Telecommunications
   * Commission (CRTC) for Canadian English-language broadcasts. For more
   * information, see the Canadian Broadcast Standards Council website.
   *
   * @var string
   */
  public $catvRating;
  /**
   * The video's rating from the Canadian Radio-Television and
   * Telecommunications Commission (CRTC) for Canadian French-language
   * broadcasts. For more information, see the Canadian Broadcast Standards
   * Council website.
   *
   * @var string
   */
  public $catvfrRating;
  /**
   * The video's Central Board of Film Certification (CBFC - India) rating.
   *
   * @var string
   */
  public $cbfcRating;
  /**
   * The video's Consejo de Calificación Cinematográfica (Chile) rating.
   *
   * @var string
   */
  public $cccRating;
  /**
   * The video's rating from Portugal's Comissão de Classificação de
   * Espect´culos.
   *
   * @var string
   */
  public $cceRating;
  /**
   * The video's rating in Switzerland.
   *
   * @var string
   */
  public $chfilmRating;
  /**
   * The video's Canadian Home Video Rating System (CHVRS) rating.
   *
   * @var string
   */
  public $chvrsRating;
  /**
   * The video's rating from the Commission de Contrôle des Films (Belgium).
   *
   * @var string
   */
  public $cicfRating;
  /**
   * The video's rating from Romania's CONSILIUL NATIONAL AL AUDIOVIZUALULUI
   * (CNA).
   *
   * @var string
   */
  public $cnaRating;
  /**
   * Rating system in France - Commission de classification cinematographique
   *
   * @var string
   */
  public $cncRating;
  /**
   * The video's rating from France's Conseil supérieur de l’audiovisuel, which
   * rates broadcast content.
   *
   * @var string
   */
  public $csaRating;
  /**
   * The video's rating from Luxembourg's Commission de surveillance de la
   * classification des films (CSCF).
   *
   * @var string
   */
  public $cscfRating;
  /**
   * The video's rating in the Czech Republic.
   *
   * @var string
   */
  public $czfilmRating;
  /**
   * The video's Departamento de Justiça, Classificação, Qualificação e Títulos
   * (DJCQT - Brazil) rating.
   *
   * @var string
   */
  public $djctqRating;
  /**
   * Reasons that explain why the video received its DJCQT (Brazil) rating.
   *
   * @var string[]
   */
  public $djctqRatingReasons;
  /**
   * Rating system in Turkey - Evaluation and Classification Board of the
   * Ministry of Culture and Tourism
   *
   * @var string
   */
  public $ecbmctRating;
  /**
   * The video's rating in Estonia.
   *
   * @var string
   */
  public $eefilmRating;
  /**
   * The video's rating in Egypt.
   *
   * @var string
   */
  public $egfilmRating;
  /**
   * The video's Eirin (映倫) rating. Eirin is the Japanese rating system.
   *
   * @var string
   */
  public $eirinRating;
  /**
   * The video's rating from Malaysia's Film Censorship Board.
   *
   * @var string
   */
  public $fcbmRating;
  /**
   * The video's rating from Hong Kong's Office for Film, Newspaper and Article
   * Administration.
   *
   * @var string
   */
  public $fcoRating;
  /**
   * This property has been deprecated. Use the
   * contentDetails.contentRating.cncRating instead.
   *
   * @deprecated
   * @var string
   */
  public $fmocRating;
  /**
   * The video's rating from South Africa's Film and Publication Board.
   *
   * @var string
   */
  public $fpbRating;
  /**
   * Reasons that explain why the video received its FPB (South Africa) rating.
   *
   * @var string[]
   */
  public $fpbRatingReasons;
  /**
   * The video's Freiwillige Selbstkontrolle der Filmwirtschaft (FSK - Germany)
   * rating.
   *
   * @var string
   */
  public $fskRating;
  /**
   * The video's rating in Greece.
   *
   * @var string
   */
  public $grfilmRating;
  /**
   * The video's Instituto de la Cinematografía y de las Artes Audiovisuales
   * (ICAA - Spain) rating.
   *
   * @var string
   */
  public $icaaRating;
  /**
   * The video's Irish Film Classification Office (IFCO - Ireland) rating. See
   * the IFCO website for more information.
   *
   * @var string
   */
  public $ifcoRating;
  /**
   * The video's rating in Israel.
   *
   * @var string
   */
  public $ilfilmRating;
  /**
   * The video's INCAA (Instituto Nacional de Cine y Artes Audiovisuales -
   * Argentina) rating.
   *
   * @var string
   */
  public $incaaRating;
  /**
   * The video's rating from the Kenya Film Classification Board.
   *
   * @var string
   */
  public $kfcbRating;
  /**
   * The video's NICAM/Kijkwijzer rating from the Nederlands Instituut voor de
   * Classificatie van Audiovisuele Media (Netherlands).
   *
   * @var string
   */
  public $kijkwijzerRating;
  /**
   * The video's Korea Media Rating Board (영상물등급위원회) rating. The KMRB rates
   * videos in South Korea.
   *
   * @var string
   */
  public $kmrbRating;
  /**
   * The video's rating from Indonesia's Lembaga Sensor Film.
   *
   * @var string
   */
  public $lsfRating;
  /**
   * The video's rating from Malta's Film Age-Classification Board.
   *
   * @var string
   */
  public $mccaaRating;
  /**
   * The video's rating from the Danish Film Institute's (Det Danske
   * Filminstitut) Media Council for Children and Young People.
   *
   * @var string
   */
  public $mccypRating;
  /**
   * The video's rating system for Vietnam - MCST
   *
   * @var string
   */
  public $mcstRating;
  /**
   * The video's rating from Singapore's Media Development Authority (MDA) and,
   * specifically, it's Board of Film Censors (BFC).
   *
   * @var string
   */
  public $mdaRating;
  /**
   * The video's rating from Medietilsynet, the Norwegian Media Authority.
   *
   * @var string
   */
  public $medietilsynetRating;
  /**
   * The video's rating from Finland's Kansallinen Audiovisuaalinen Instituutti
   * (National Audiovisual Institute).
   *
   * @var string
   */
  public $mekuRating;
  /**
   * The rating system for MENA countries, a clone of MPAA. It is needed to
   * prevent titles go live w/o additional QC check, since some of them can be
   * inappropriate for the countries at all. See b/33408548 for more details.
   *
   * @var string
   */
  public $menaMpaaRating;
  /**
   * The video's rating from the Ministero dei Beni e delle Attività Culturali e
   * del Turismo (Italy).
   *
   * @var string
   */
  public $mibacRating;
  /**
   * The video's Ministerio de Cultura (Colombia) rating.
   *
   * @var string
   */
  public $mocRating;
  /**
   * The video's rating from Taiwan's Ministry of Culture (文化部).
   *
   * @var string
   */
  public $moctwRating;
  /**
   * The video's Motion Picture Association of America (MPAA) rating.
   *
   * @var string
   */
  public $mpaaRating;
  /**
   * The rating system for trailer, DVD, and Ad in the US. See
   * http://movielabs.com/md/ratings/v2.3/html/US_MPAAT_Ratings.html.
   *
   * @var string
   */
  public $mpaatRating;
  /**
   * The video's rating from the Movie and Television Review and Classification
   * Board (Philippines).
   *
   * @var string
   */
  public $mtrcbRating;
  /**
   * The video's rating from the Maldives National Bureau of Classification.
   *
   * @var string
   */
  public $nbcRating;
  /**
   * The video's rating in Poland.
   *
   * @var string
   */
  public $nbcplRating;
  /**
   * The video's rating from the Bulgarian National Film Center.
   *
   * @var string
   */
  public $nfrcRating;
  /**
   * The video's rating from Nigeria's National Film and Video Censors Board.
   *
   * @var string
   */
  public $nfvcbRating;
  /**
   * The video's rating from the Nacionãlais Kino centrs (National Film Centre
   * of Latvia).
   *
   * @var string
   */
  public $nkclvRating;
  /**
   * The National Media Council ratings system for United Arab Emirates.
   *
   * @var string
   */
  public $nmcRating;
  /**
   * The video's Office of Film and Literature Classification (OFLC - New
   * Zealand) rating.
   *
   * @var string
   */
  public $oflcRating;
  /**
   * The video's rating in Peru.
   *
   * @var string
   */
  public $pefilmRating;
  /**
   * The video's rating from the Hungarian Nemzeti Filmiroda, the Rating
   * Committee of the National Office of Film.
   *
   * @var string
   */
  public $rcnofRating;
  /**
   * The video's rating in Venezuela.
   *
   * @var string
   */
  public $resorteviolenciaRating;
  /**
   * The video's General Directorate of Radio, Television and Cinematography
   * (Mexico) rating.
   *
   * @var string
   */
  public $rtcRating;
  /**
   * The video's rating from Ireland's Raidió Teilifís Éireann.
   *
   * @var string
   */
  public $rteRating;
  /**
   * The video's National Film Registry of the Russian Federation (MKRF -
   * Russia) rating.
   *
   * @var string
   */
  public $russiaRating;
  /**
   * The video's rating in Slovakia.
   *
   * @var string
   */
  public $skfilmRating;
  /**
   * The video's rating in Iceland.
   *
   * @var string
   */
  public $smaisRating;
  /**
   * The video's rating from Statens medieråd (Sweden's National Media Council).
   *
   * @var string
   */
  public $smsaRating;
  /**
   * The video's TV Parental Guidelines (TVPG) rating.
   *
   * @var string
   */
  public $tvpgRating;
  /**
   * A rating that YouTube uses to identify age-restricted content.
   *
   * @var string
   */
  public $ytRating;

  /**
   * The video's Australian Classification Board (ACB) or Australian
   * Communications and Media Authority (ACMA) rating. ACMA ratings are used to
   * classify children's television programming.
   *
   * Accepted values: acbUnspecified, acbE, acbP, acbC, acbG, acbPg, acbM,
   * acbMa15plus, acbR18plus, acbUnrated
   *
   * @param self::ACB_RATING_* $acbRating
   */
  public function setAcbRating($acbRating)
  {
    $this->acbRating = $acbRating;
  }
  /**
   * @return self::ACB_RATING_*
   */
  public function getAcbRating()
  {
    return $this->acbRating;
  }
  /**
   * The video's rating from Italy's Autorità per le Garanzie nelle
   * Comunicazioni (AGCOM).
   *
   * Accepted values: agcomUnspecified, agcomT, agcomVm14, agcomVm18,
   * agcomUnrated
   *
   * @param self::AGCOM_RATING_* $agcomRating
   */
  public function setAgcomRating($agcomRating)
  {
    $this->agcomRating = $agcomRating;
  }
  /**
   * @return self::AGCOM_RATING_*
   */
  public function getAgcomRating()
  {
    return $this->agcomRating;
  }
  /**
   * The video's Anatel (Asociación Nacional de Televisión) rating for Chilean
   * television.
   *
   * Accepted values: anatelUnspecified, anatelF, anatelI, anatelI7, anatelI10,
   * anatelI12, anatelR, anatelA, anatelUnrated
   *
   * @param self::ANATEL_RATING_* $anatelRating
   */
  public function setAnatelRating($anatelRating)
  {
    $this->anatelRating = $anatelRating;
  }
  /**
   * @return self::ANATEL_RATING_*
   */
  public function getAnatelRating()
  {
    return $this->anatelRating;
  }
  /**
   * The video's British Board of Film Classification (BBFC) rating.
   *
   * Accepted values: bbfcUnspecified, bbfcU, bbfcPg, bbfc12a, bbfc12, bbfc15,
   * bbfc18, bbfcR18, bbfcUnrated
   *
   * @param self::BBFC_RATING_* $bbfcRating
   */
  public function setBbfcRating($bbfcRating)
  {
    $this->bbfcRating = $bbfcRating;
  }
  /**
   * @return self::BBFC_RATING_*
   */
  public function getBbfcRating()
  {
    return $this->bbfcRating;
  }
  /**
   * The video's rating from Thailand's Board of Film and Video Censors.
   *
   * Accepted values: bfvcUnspecified, bfvcG, bfvcE, bfvc13, bfvc15, bfvc18,
   * bfvc20, bfvcB, bfvcUnrated
   *
   * @param self::BFVC_RATING_* $bfvcRating
   */
  public function setBfvcRating($bfvcRating)
  {
    $this->bfvcRating = $bfvcRating;
  }
  /**
   * @return self::BFVC_RATING_*
   */
  public function getBfvcRating()
  {
    return $this->bfvcRating;
  }
  /**
   * The video's rating from the Austrian Board of Media Classification
   * (Bundesministerium für Unterricht, Kunst und Kultur).
   *
   * Accepted values: bmukkUnspecified, bmukkAa, bmukk6, bmukk8, bmukk10,
   * bmukk12, bmukk14, bmukk16, bmukkUnrated
   *
   * @param self::BMUKK_RATING_* $bmukkRating
   */
  public function setBmukkRating($bmukkRating)
  {
    $this->bmukkRating = $bmukkRating;
  }
  /**
   * @return self::BMUKK_RATING_*
   */
  public function getBmukkRating()
  {
    return $this->bmukkRating;
  }
  /**
   * Rating system for Canadian TV - Canadian TV Classification System The
   * video's rating from the Canadian Radio-Television and Telecommunications
   * Commission (CRTC) for Canadian English-language broadcasts. For more
   * information, see the Canadian Broadcast Standards Council website.
   *
   * Accepted values: catvUnspecified, catvC, catvC8, catvG, catvPg, catv14plus,
   * catv18plus, catvUnrated, catvE
   *
   * @param self::CATV_RATING_* $catvRating
   */
  public function setCatvRating($catvRating)
  {
    $this->catvRating = $catvRating;
  }
  /**
   * @return self::CATV_RATING_*
   */
  public function getCatvRating()
  {
    return $this->catvRating;
  }
  /**
   * The video's rating from the Canadian Radio-Television and
   * Telecommunications Commission (CRTC) for Canadian French-language
   * broadcasts. For more information, see the Canadian Broadcast Standards
   * Council website.
   *
   * Accepted values: catvfrUnspecified, catvfrG, catvfr8plus, catvfr13plus,
   * catvfr16plus, catvfr18plus, catvfrUnrated, catvfrE
   *
   * @param self::CATVFR_RATING_* $catvfrRating
   */
  public function setCatvfrRating($catvfrRating)
  {
    $this->catvfrRating = $catvfrRating;
  }
  /**
   * @return self::CATVFR_RATING_*
   */
  public function getCatvfrRating()
  {
    return $this->catvfrRating;
  }
  /**
   * The video's Central Board of Film Certification (CBFC - India) rating.
   *
   * Accepted values: cbfcUnspecified, cbfcU, cbfcUA, cbfcUA7plus, cbfcUA13plus,
   * cbfcUA16plus, cbfcA, cbfcS, cbfcUnrated
   *
   * @param self::CBFC_RATING_* $cbfcRating
   */
  public function setCbfcRating($cbfcRating)
  {
    $this->cbfcRating = $cbfcRating;
  }
  /**
   * @return self::CBFC_RATING_*
   */
  public function getCbfcRating()
  {
    return $this->cbfcRating;
  }
  /**
   * The video's Consejo de Calificación Cinematográfica (Chile) rating.
   *
   * Accepted values: cccUnspecified, cccTe, ccc6, ccc14, ccc18, ccc18v, ccc18s,
   * cccUnrated
   *
   * @param self::CCC_RATING_* $cccRating
   */
  public function setCccRating($cccRating)
  {
    $this->cccRating = $cccRating;
  }
  /**
   * @return self::CCC_RATING_*
   */
  public function getCccRating()
  {
    return $this->cccRating;
  }
  /**
   * The video's rating from Portugal's Comissão de Classificação de
   * Espect´culos.
   *
   * Accepted values: cceUnspecified, cceM4, cceM6, cceM12, cceM16, cceM18,
   * cceUnrated, cceM14
   *
   * @param self::CCE_RATING_* $cceRating
   */
  public function setCceRating($cceRating)
  {
    $this->cceRating = $cceRating;
  }
  /**
   * @return self::CCE_RATING_*
   */
  public function getCceRating()
  {
    return $this->cceRating;
  }
  /**
   * The video's rating in Switzerland.
   *
   * Accepted values: chfilmUnspecified, chfilm0, chfilm6, chfilm12, chfilm16,
   * chfilm18, chfilmUnrated
   *
   * @param self::CHFILM_RATING_* $chfilmRating
   */
  public function setChfilmRating($chfilmRating)
  {
    $this->chfilmRating = $chfilmRating;
  }
  /**
   * @return self::CHFILM_RATING_*
   */
  public function getChfilmRating()
  {
    return $this->chfilmRating;
  }
  /**
   * The video's Canadian Home Video Rating System (CHVRS) rating.
   *
   * Accepted values: chvrsUnspecified, chvrsG, chvrsPg, chvrs14a, chvrs18a,
   * chvrsR, chvrsE, chvrsUnrated
   *
   * @param self::CHVRS_RATING_* $chvrsRating
   */
  public function setChvrsRating($chvrsRating)
  {
    $this->chvrsRating = $chvrsRating;
  }
  /**
   * @return self::CHVRS_RATING_*
   */
  public function getChvrsRating()
  {
    return $this->chvrsRating;
  }
  /**
   * The video's rating from the Commission de Contrôle des Films (Belgium).
   *
   * Accepted values: cicfUnspecified, cicfE, cicfKtEa, cicfKntEna, cicfUnrated
   *
   * @param self::CICF_RATING_* $cicfRating
   */
  public function setCicfRating($cicfRating)
  {
    $this->cicfRating = $cicfRating;
  }
  /**
   * @return self::CICF_RATING_*
   */
  public function getCicfRating()
  {
    return $this->cicfRating;
  }
  /**
   * The video's rating from Romania's CONSILIUL NATIONAL AL AUDIOVIZUALULUI
   * (CNA).
   *
   * Accepted values: cnaUnspecified, cnaAp, cna12, cna15, cna18, cna18plus,
   * cnaUnrated
   *
   * @param self::CNA_RATING_* $cnaRating
   */
  public function setCnaRating($cnaRating)
  {
    $this->cnaRating = $cnaRating;
  }
  /**
   * @return self::CNA_RATING_*
   */
  public function getCnaRating()
  {
    return $this->cnaRating;
  }
  /**
   * Rating system in France - Commission de classification cinematographique
   *
   * Accepted values: cncUnspecified, cncT, cnc10, cnc12, cnc16, cnc18, cncE,
   * cncInterdiction, cncUnrated
   *
   * @param self::CNC_RATING_* $cncRating
   */
  public function setCncRating($cncRating)
  {
    $this->cncRating = $cncRating;
  }
  /**
   * @return self::CNC_RATING_*
   */
  public function getCncRating()
  {
    return $this->cncRating;
  }
  /**
   * The video's rating from France's Conseil supérieur de l’audiovisuel, which
   * rates broadcast content.
   *
   * Accepted values: csaUnspecified, csaT, csa10, csa12, csa16, csa18,
   * csaInterdiction, csaUnrated
   *
   * @param self::CSA_RATING_* $csaRating
   */
  public function setCsaRating($csaRating)
  {
    $this->csaRating = $csaRating;
  }
  /**
   * @return self::CSA_RATING_*
   */
  public function getCsaRating()
  {
    return $this->csaRating;
  }
  /**
   * The video's rating from Luxembourg's Commission de surveillance de la
   * classification des films (CSCF).
   *
   * Accepted values: cscfUnspecified, cscfAl, cscfA, cscf6, cscf9, cscf12,
   * cscf16, cscf18, cscfUnrated
   *
   * @param self::CSCF_RATING_* $cscfRating
   */
  public function setCscfRating($cscfRating)
  {
    $this->cscfRating = $cscfRating;
  }
  /**
   * @return self::CSCF_RATING_*
   */
  public function getCscfRating()
  {
    return $this->cscfRating;
  }
  /**
   * The video's rating in the Czech Republic.
   *
   * Accepted values: czfilmUnspecified, czfilmU, czfilm12, czfilm14, czfilm18,
   * czfilmUnrated
   *
   * @param self::CZFILM_RATING_* $czfilmRating
   */
  public function setCzfilmRating($czfilmRating)
  {
    $this->czfilmRating = $czfilmRating;
  }
  /**
   * @return self::CZFILM_RATING_*
   */
  public function getCzfilmRating()
  {
    return $this->czfilmRating;
  }
  /**
   * The video's Departamento de Justiça, Classificação, Qualificação e Títulos
   * (DJCQT - Brazil) rating.
   *
   * Accepted values: djctqUnspecified, djctqL, djctq10, djctq12, djctq14,
   * djctq16, djctq18, djctqEr, djctqL10, djctqL12, djctqL14, djctqL16,
   * djctqL18, djctq1012, djctq1014, djctq1016, djctq1018, djctq1214, djctq1216,
   * djctq1218, djctq1416, djctq1418, djctq1618, djctqUnrated
   *
   * @param self::DJCTQ_RATING_* $djctqRating
   */
  public function setDjctqRating($djctqRating)
  {
    $this->djctqRating = $djctqRating;
  }
  /**
   * @return self::DJCTQ_RATING_*
   */
  public function getDjctqRating()
  {
    return $this->djctqRating;
  }
  /**
   * Reasons that explain why the video received its DJCQT (Brazil) rating.
   *
   * @param string[] $djctqRatingReasons
   */
  public function setDjctqRatingReasons($djctqRatingReasons)
  {
    $this->djctqRatingReasons = $djctqRatingReasons;
  }
  /**
   * @return string[]
   */
  public function getDjctqRatingReasons()
  {
    return $this->djctqRatingReasons;
  }
  /**
   * Rating system in Turkey - Evaluation and Classification Board of the
   * Ministry of Culture and Tourism
   *
   * Accepted values: ecbmctUnspecified, ecbmctG, ecbmct7a, ecbmct7plus,
   * ecbmct13a, ecbmct13plus, ecbmct15a, ecbmct15plus, ecbmct18plus,
   * ecbmctUnrated
   *
   * @param self::ECBMCT_RATING_* $ecbmctRating
   */
  public function setEcbmctRating($ecbmctRating)
  {
    $this->ecbmctRating = $ecbmctRating;
  }
  /**
   * @return self::ECBMCT_RATING_*
   */
  public function getEcbmctRating()
  {
    return $this->ecbmctRating;
  }
  /**
   * The video's rating in Estonia.
   *
   * Accepted values: eefilmUnspecified, eefilmPere, eefilmL, eefilmMs6,
   * eefilmK6, eefilmMs12, eefilmK12, eefilmK14, eefilmK16, eefilmUnrated
   *
   * @param self::EEFILM_RATING_* $eefilmRating
   */
  public function setEefilmRating($eefilmRating)
  {
    $this->eefilmRating = $eefilmRating;
  }
  /**
   * @return self::EEFILM_RATING_*
   */
  public function getEefilmRating()
  {
    return $this->eefilmRating;
  }
  /**
   * The video's rating in Egypt.
   *
   * Accepted values: egfilmUnspecified, egfilmGn, egfilm18, egfilmBn,
   * egfilmUnrated
   *
   * @param self::EGFILM_RATING_* $egfilmRating
   */
  public function setEgfilmRating($egfilmRating)
  {
    $this->egfilmRating = $egfilmRating;
  }
  /**
   * @return self::EGFILM_RATING_*
   */
  public function getEgfilmRating()
  {
    return $this->egfilmRating;
  }
  /**
   * The video's Eirin (映倫) rating. Eirin is the Japanese rating system.
   *
   * Accepted values: eirinUnspecified, eirinG, eirinPg12, eirinR15plus,
   * eirinR18plus, eirinUnrated
   *
   * @param self::EIRIN_RATING_* $eirinRating
   */
  public function setEirinRating($eirinRating)
  {
    $this->eirinRating = $eirinRating;
  }
  /**
   * @return self::EIRIN_RATING_*
   */
  public function getEirinRating()
  {
    return $this->eirinRating;
  }
  /**
   * The video's rating from Malaysia's Film Censorship Board.
   *
   * Accepted values: fcbmUnspecified, fcbmU, fcbmPg13, fcbmP13, fcbm18,
   * fcbm18sx, fcbm18pa, fcbm18sg, fcbm18pl, fcbmUnrated
   *
   * @param self::FCBM_RATING_* $fcbmRating
   */
  public function setFcbmRating($fcbmRating)
  {
    $this->fcbmRating = $fcbmRating;
  }
  /**
   * @return self::FCBM_RATING_*
   */
  public function getFcbmRating()
  {
    return $this->fcbmRating;
  }
  /**
   * The video's rating from Hong Kong's Office for Film, Newspaper and Article
   * Administration.
   *
   * Accepted values: fcoUnspecified, fcoI, fcoIia, fcoIib, fcoIi, fcoIii,
   * fcoUnrated
   *
   * @param self::FCO_RATING_* $fcoRating
   */
  public function setFcoRating($fcoRating)
  {
    $this->fcoRating = $fcoRating;
  }
  /**
   * @return self::FCO_RATING_*
   */
  public function getFcoRating()
  {
    return $this->fcoRating;
  }
  /**
   * This property has been deprecated. Use the
   * contentDetails.contentRating.cncRating instead.
   *
   * Accepted values: fmocUnspecified, fmocU, fmoc10, fmoc12, fmoc16, fmoc18,
   * fmocE, fmocUnrated
   *
   * @deprecated
   * @param self::FMOC_RATING_* $fmocRating
   */
  public function setFmocRating($fmocRating)
  {
    $this->fmocRating = $fmocRating;
  }
  /**
   * @deprecated
   * @return self::FMOC_RATING_*
   */
  public function getFmocRating()
  {
    return $this->fmocRating;
  }
  /**
   * The video's rating from South Africa's Film and Publication Board.
   *
   * Accepted values: fpbUnspecified, fpbA, fpbPg, fpb79Pg, fpb1012Pg, fpb13,
   * fpb16, fpb18, fpbX18, fpbXx, fpbUnrated, fpb10
   *
   * @param self::FPB_RATING_* $fpbRating
   */
  public function setFpbRating($fpbRating)
  {
    $this->fpbRating = $fpbRating;
  }
  /**
   * @return self::FPB_RATING_*
   */
  public function getFpbRating()
  {
    return $this->fpbRating;
  }
  /**
   * Reasons that explain why the video received its FPB (South Africa) rating.
   *
   * @param string[] $fpbRatingReasons
   */
  public function setFpbRatingReasons($fpbRatingReasons)
  {
    $this->fpbRatingReasons = $fpbRatingReasons;
  }
  /**
   * @return string[]
   */
  public function getFpbRatingReasons()
  {
    return $this->fpbRatingReasons;
  }
  /**
   * The video's Freiwillige Selbstkontrolle der Filmwirtschaft (FSK - Germany)
   * rating.
   *
   * Accepted values: fskUnspecified, fsk0, fsk6, fsk12, fsk16, fsk18,
   * fskUnrated
   *
   * @param self::FSK_RATING_* $fskRating
   */
  public function setFskRating($fskRating)
  {
    $this->fskRating = $fskRating;
  }
  /**
   * @return self::FSK_RATING_*
   */
  public function getFskRating()
  {
    return $this->fskRating;
  }
  /**
   * The video's rating in Greece.
   *
   * Accepted values: grfilmUnspecified, grfilmK, grfilmE, grfilmK12, grfilmK13,
   * grfilmK15, grfilmK17, grfilmK18, grfilmUnrated
   *
   * @param self::GRFILM_RATING_* $grfilmRating
   */
  public function setGrfilmRating($grfilmRating)
  {
    $this->grfilmRating = $grfilmRating;
  }
  /**
   * @return self::GRFILM_RATING_*
   */
  public function getGrfilmRating()
  {
    return $this->grfilmRating;
  }
  /**
   * The video's Instituto de la Cinematografía y de las Artes Audiovisuales
   * (ICAA - Spain) rating.
   *
   * Accepted values: icaaUnspecified, icaaApta, icaa7, icaa12, icaa13, icaa16,
   * icaa18, icaaX, icaaUnrated
   *
   * @param self::ICAA_RATING_* $icaaRating
   */
  public function setIcaaRating($icaaRating)
  {
    $this->icaaRating = $icaaRating;
  }
  /**
   * @return self::ICAA_RATING_*
   */
  public function getIcaaRating()
  {
    return $this->icaaRating;
  }
  /**
   * The video's Irish Film Classification Office (IFCO - Ireland) rating. See
   * the IFCO website for more information.
   *
   * Accepted values: ifcoUnspecified, ifcoG, ifcoPg, ifco12, ifco12a, ifco15,
   * ifco15a, ifco16, ifco18, ifcoUnrated
   *
   * @param self::IFCO_RATING_* $ifcoRating
   */
  public function setIfcoRating($ifcoRating)
  {
    $this->ifcoRating = $ifcoRating;
  }
  /**
   * @return self::IFCO_RATING_*
   */
  public function getIfcoRating()
  {
    return $this->ifcoRating;
  }
  /**
   * The video's rating in Israel.
   *
   * Accepted values: ilfilmUnspecified, ilfilmAa, ilfilm12, ilfilm14, ilfilm16,
   * ilfilm18, ilfilmUnrated
   *
   * @param self::ILFILM_RATING_* $ilfilmRating
   */
  public function setIlfilmRating($ilfilmRating)
  {
    $this->ilfilmRating = $ilfilmRating;
  }
  /**
   * @return self::ILFILM_RATING_*
   */
  public function getIlfilmRating()
  {
    return $this->ilfilmRating;
  }
  /**
   * The video's INCAA (Instituto Nacional de Cine y Artes Audiovisuales -
   * Argentina) rating.
   *
   * Accepted values: incaaUnspecified, incaaAtp, incaaSam13, incaaSam16,
   * incaaSam18, incaaC, incaaUnrated
   *
   * @param self::INCAA_RATING_* $incaaRating
   */
  public function setIncaaRating($incaaRating)
  {
    $this->incaaRating = $incaaRating;
  }
  /**
   * @return self::INCAA_RATING_*
   */
  public function getIncaaRating()
  {
    return $this->incaaRating;
  }
  /**
   * The video's rating from the Kenya Film Classification Board.
   *
   * Accepted values: kfcbUnspecified, kfcbG, kfcbPg, kfcb16plus, kfcbR,
   * kfcbUnrated
   *
   * @param self::KFCB_RATING_* $kfcbRating
   */
  public function setKfcbRating($kfcbRating)
  {
    $this->kfcbRating = $kfcbRating;
  }
  /**
   * @return self::KFCB_RATING_*
   */
  public function getKfcbRating()
  {
    return $this->kfcbRating;
  }
  /**
   * The video's NICAM/Kijkwijzer rating from the Nederlands Instituut voor de
   * Classificatie van Audiovisuele Media (Netherlands).
   *
   * Accepted values: kijkwijzerUnspecified, kijkwijzerAl, kijkwijzer6,
   * kijkwijzer9, kijkwijzer12, kijkwijzer16, kijkwijzer18, kijkwijzerUnrated
   *
   * @param self::KIJKWIJZER_RATING_* $kijkwijzerRating
   */
  public function setKijkwijzerRating($kijkwijzerRating)
  {
    $this->kijkwijzerRating = $kijkwijzerRating;
  }
  /**
   * @return self::KIJKWIJZER_RATING_*
   */
  public function getKijkwijzerRating()
  {
    return $this->kijkwijzerRating;
  }
  /**
   * The video's Korea Media Rating Board (영상물등급위원회) rating. The KMRB rates
   * videos in South Korea.
   *
   * Accepted values: kmrbUnspecified, kmrbAll, kmrb12plus, kmrb15plus,
   * kmrbTeenr, kmrbR, kmrbUnrated
   *
   * @param self::KMRB_RATING_* $kmrbRating
   */
  public function setKmrbRating($kmrbRating)
  {
    $this->kmrbRating = $kmrbRating;
  }
  /**
   * @return self::KMRB_RATING_*
   */
  public function getKmrbRating()
  {
    return $this->kmrbRating;
  }
  /**
   * The video's rating from Indonesia's Lembaga Sensor Film.
   *
   * Accepted values: lsfUnspecified, lsfSu, lsfA, lsfBo, lsf13, lsfR, lsf17,
   * lsfD, lsf21, lsfUnrated
   *
   * @param self::LSF_RATING_* $lsfRating
   */
  public function setLsfRating($lsfRating)
  {
    $this->lsfRating = $lsfRating;
  }
  /**
   * @return self::LSF_RATING_*
   */
  public function getLsfRating()
  {
    return $this->lsfRating;
  }
  /**
   * The video's rating from Malta's Film Age-Classification Board.
   *
   * Accepted values: mccaaUnspecified, mccaaU, mccaaPg, mccaa12a, mccaa12,
   * mccaa14, mccaa15, mccaa16, mccaa18, mccaaUnrated
   *
   * @param self::MCCAA_RATING_* $mccaaRating
   */
  public function setMccaaRating($mccaaRating)
  {
    $this->mccaaRating = $mccaaRating;
  }
  /**
   * @return self::MCCAA_RATING_*
   */
  public function getMccaaRating()
  {
    return $this->mccaaRating;
  }
  /**
   * The video's rating from the Danish Film Institute's (Det Danske
   * Filminstitut) Media Council for Children and Young People.
   *
   * Accepted values: mccypUnspecified, mccypA, mccyp7, mccyp11, mccyp15,
   * mccypUnrated
   *
   * @param self::MCCYP_RATING_* $mccypRating
   */
  public function setMccypRating($mccypRating)
  {
    $this->mccypRating = $mccypRating;
  }
  /**
   * @return self::MCCYP_RATING_*
   */
  public function getMccypRating()
  {
    return $this->mccypRating;
  }
  /**
   * The video's rating system for Vietnam - MCST
   *
   * Accepted values: mcstUnspecified, mcstP, mcst0, mcstC13, mcstC16,
   * mcst16plus, mcstC18, mcstGPg, mcstUnrated
   *
   * @param self::MCST_RATING_* $mcstRating
   */
  public function setMcstRating($mcstRating)
  {
    $this->mcstRating = $mcstRating;
  }
  /**
   * @return self::MCST_RATING_*
   */
  public function getMcstRating()
  {
    return $this->mcstRating;
  }
  /**
   * The video's rating from Singapore's Media Development Authority (MDA) and,
   * specifically, it's Board of Film Censors (BFC).
   *
   * Accepted values: mdaUnspecified, mdaG, mdaPg, mdaPg13, mdaNc16, mdaM18,
   * mdaR21, mdaUnrated
   *
   * @param self::MDA_RATING_* $mdaRating
   */
  public function setMdaRating($mdaRating)
  {
    $this->mdaRating = $mdaRating;
  }
  /**
   * @return self::MDA_RATING_*
   */
  public function getMdaRating()
  {
    return $this->mdaRating;
  }
  /**
   * The video's rating from Medietilsynet, the Norwegian Media Authority.
   *
   * Accepted values: medietilsynetUnspecified, medietilsynetA, medietilsynet6,
   * medietilsynet7, medietilsynet9, medietilsynet11, medietilsynet12,
   * medietilsynet15, medietilsynet18, medietilsynetUnrated
   *
   * @param self::MEDIETILSYNET_RATING_* $medietilsynetRating
   */
  public function setMedietilsynetRating($medietilsynetRating)
  {
    $this->medietilsynetRating = $medietilsynetRating;
  }
  /**
   * @return self::MEDIETILSYNET_RATING_*
   */
  public function getMedietilsynetRating()
  {
    return $this->medietilsynetRating;
  }
  /**
   * The video's rating from Finland's Kansallinen Audiovisuaalinen Instituutti
   * (National Audiovisual Institute).
   *
   * Accepted values: mekuUnspecified, mekuS, meku7, meku12, meku16, meku18,
   * mekuUnrated
   *
   * @param self::MEKU_RATING_* $mekuRating
   */
  public function setMekuRating($mekuRating)
  {
    $this->mekuRating = $mekuRating;
  }
  /**
   * @return self::MEKU_RATING_*
   */
  public function getMekuRating()
  {
    return $this->mekuRating;
  }
  /**
   * The rating system for MENA countries, a clone of MPAA. It is needed to
   * prevent titles go live w/o additional QC check, since some of them can be
   * inappropriate for the countries at all. See b/33408548 for more details.
   *
   * Accepted values: menaMpaaUnspecified, menaMpaaG, menaMpaaPg, menaMpaaPg13,
   * menaMpaaR, menaMpaaUnrated
   *
   * @param self::MENA_MPAA_RATING_* $menaMpaaRating
   */
  public function setMenaMpaaRating($menaMpaaRating)
  {
    $this->menaMpaaRating = $menaMpaaRating;
  }
  /**
   * @return self::MENA_MPAA_RATING_*
   */
  public function getMenaMpaaRating()
  {
    return $this->menaMpaaRating;
  }
  /**
   * The video's rating from the Ministero dei Beni e delle Attività Culturali e
   * del Turismo (Italy).
   *
   * Accepted values: mibacUnspecified, mibacT, mibacVap, mibacVm6, mibacVm12,
   * mibacVm14, mibacVm16, mibacVm18, mibacUnrated
   *
   * @param self::MIBAC_RATING_* $mibacRating
   */
  public function setMibacRating($mibacRating)
  {
    $this->mibacRating = $mibacRating;
  }
  /**
   * @return self::MIBAC_RATING_*
   */
  public function getMibacRating()
  {
    return $this->mibacRating;
  }
  /**
   * The video's Ministerio de Cultura (Colombia) rating.
   *
   * Accepted values: mocUnspecified, mocE, mocT, moc7, moc12, moc15, moc18,
   * mocX, mocBanned, mocUnrated
   *
   * @param self::MOC_RATING_* $mocRating
   */
  public function setMocRating($mocRating)
  {
    $this->mocRating = $mocRating;
  }
  /**
   * @return self::MOC_RATING_*
   */
  public function getMocRating()
  {
    return $this->mocRating;
  }
  /**
   * The video's rating from Taiwan's Ministry of Culture (文化部).
   *
   * Accepted values: moctwUnspecified, moctwG, moctwP, moctwPg, moctwR,
   * moctwUnrated, moctwR12, moctwR15
   *
   * @param self::MOCTW_RATING_* $moctwRating
   */
  public function setMoctwRating($moctwRating)
  {
    $this->moctwRating = $moctwRating;
  }
  /**
   * @return self::MOCTW_RATING_*
   */
  public function getMoctwRating()
  {
    return $this->moctwRating;
  }
  /**
   * The video's Motion Picture Association of America (MPAA) rating.
   *
   * Accepted values: mpaaUnspecified, mpaaG, mpaaPg, mpaaPg13, mpaaR, mpaaNc17,
   * mpaaX, mpaaUnrated
   *
   * @param self::MPAA_RATING_* $mpaaRating
   */
  public function setMpaaRating($mpaaRating)
  {
    $this->mpaaRating = $mpaaRating;
  }
  /**
   * @return self::MPAA_RATING_*
   */
  public function getMpaaRating()
  {
    return $this->mpaaRating;
  }
  /**
   * The rating system for trailer, DVD, and Ad in the US. See
   * http://movielabs.com/md/ratings/v2.3/html/US_MPAAT_Ratings.html.
   *
   * Accepted values: mpaatUnspecified, mpaatGb, mpaatRb
   *
   * @param self::MPAAT_RATING_* $mpaatRating
   */
  public function setMpaatRating($mpaatRating)
  {
    $this->mpaatRating = $mpaatRating;
  }
  /**
   * @return self::MPAAT_RATING_*
   */
  public function getMpaatRating()
  {
    return $this->mpaatRating;
  }
  /**
   * The video's rating from the Movie and Television Review and Classification
   * Board (Philippines).
   *
   * Accepted values: mtrcbUnspecified, mtrcbG, mtrcbPg, mtrcbR13, mtrcbR16,
   * mtrcbR18, mtrcbX, mtrcbUnrated
   *
   * @param self::MTRCB_RATING_* $mtrcbRating
   */
  public function setMtrcbRating($mtrcbRating)
  {
    $this->mtrcbRating = $mtrcbRating;
  }
  /**
   * @return self::MTRCB_RATING_*
   */
  public function getMtrcbRating()
  {
    return $this->mtrcbRating;
  }
  /**
   * The video's rating from the Maldives National Bureau of Classification.
   *
   * Accepted values: nbcUnspecified, nbcG, nbcPg, nbc12plus, nbc15plus,
   * nbc18plus, nbc18plusr, nbcPu, nbcUnrated
   *
   * @param self::NBC_RATING_* $nbcRating
   */
  public function setNbcRating($nbcRating)
  {
    $this->nbcRating = $nbcRating;
  }
  /**
   * @return self::NBC_RATING_*
   */
  public function getNbcRating()
  {
    return $this->nbcRating;
  }
  /**
   * The video's rating in Poland.
   *
   * Accepted values: nbcplUnspecified, nbcplI, nbcplIi, nbcplIii, nbcplIv,
   * nbcpl18plus, nbcplUnrated
   *
   * @param self::NBCPL_RATING_* $nbcplRating
   */
  public function setNbcplRating($nbcplRating)
  {
    $this->nbcplRating = $nbcplRating;
  }
  /**
   * @return self::NBCPL_RATING_*
   */
  public function getNbcplRating()
  {
    return $this->nbcplRating;
  }
  /**
   * The video's rating from the Bulgarian National Film Center.
   *
   * Accepted values: nfrcUnspecified, nfrcA, nfrcB, nfrcC, nfrcD, nfrcX,
   * nfrcUnrated
   *
   * @param self::NFRC_RATING_* $nfrcRating
   */
  public function setNfrcRating($nfrcRating)
  {
    $this->nfrcRating = $nfrcRating;
  }
  /**
   * @return self::NFRC_RATING_*
   */
  public function getNfrcRating()
  {
    return $this->nfrcRating;
  }
  /**
   * The video's rating from Nigeria's National Film and Video Censors Board.
   *
   * Accepted values: nfvcbUnspecified, nfvcbG, nfvcbPg, nfvcb12, nfvcb12a,
   * nfvcb15, nfvcb18, nfvcbRe, nfvcbUnrated
   *
   * @param self::NFVCB_RATING_* $nfvcbRating
   */
  public function setNfvcbRating($nfvcbRating)
  {
    $this->nfvcbRating = $nfvcbRating;
  }
  /**
   * @return self::NFVCB_RATING_*
   */
  public function getNfvcbRating()
  {
    return $this->nfvcbRating;
  }
  /**
   * The video's rating from the Nacionãlais Kino centrs (National Film Centre
   * of Latvia).
   *
   * Accepted values: nkclvUnspecified, nkclvU, nkclv7plus, nkclv12plus,
   * nkclv16plus, nkclv18plus, nkclvUnrated
   *
   * @param self::NKCLV_RATING_* $nkclvRating
   */
  public function setNkclvRating($nkclvRating)
  {
    $this->nkclvRating = $nkclvRating;
  }
  /**
   * @return self::NKCLV_RATING_*
   */
  public function getNkclvRating()
  {
    return $this->nkclvRating;
  }
  /**
   * The National Media Council ratings system for United Arab Emirates.
   *
   * Accepted values: nmcUnspecified, nmcG, nmcPg, nmcPg13, nmcPg15, nmc15plus,
   * nmc18plus, nmc18tc, nmcUnrated
   *
   * @param self::NMC_RATING_* $nmcRating
   */
  public function setNmcRating($nmcRating)
  {
    $this->nmcRating = $nmcRating;
  }
  /**
   * @return self::NMC_RATING_*
   */
  public function getNmcRating()
  {
    return $this->nmcRating;
  }
  /**
   * The video's Office of Film and Literature Classification (OFLC - New
   * Zealand) rating.
   *
   * Accepted values: oflcUnspecified, oflcG, oflcPg, oflcM, oflcR13, oflcR15,
   * oflcR16, oflcR18, oflcUnrated, oflcRp13, oflcRp16, oflcRp18
   *
   * @param self::OFLC_RATING_* $oflcRating
   */
  public function setOflcRating($oflcRating)
  {
    $this->oflcRating = $oflcRating;
  }
  /**
   * @return self::OFLC_RATING_*
   */
  public function getOflcRating()
  {
    return $this->oflcRating;
  }
  /**
   * The video's rating in Peru.
   *
   * Accepted values: pefilmUnspecified, pefilmPt, pefilmPg, pefilm14, pefilm18,
   * pefilmUnrated
   *
   * @param self::PEFILM_RATING_* $pefilmRating
   */
  public function setPefilmRating($pefilmRating)
  {
    $this->pefilmRating = $pefilmRating;
  }
  /**
   * @return self::PEFILM_RATING_*
   */
  public function getPefilmRating()
  {
    return $this->pefilmRating;
  }
  /**
   * The video's rating from the Hungarian Nemzeti Filmiroda, the Rating
   * Committee of the National Office of Film.
   *
   * Accepted values: rcnofUnspecified, rcnofI, rcnofIi, rcnofIii, rcnofIv,
   * rcnofV, rcnofVi, rcnofUnrated
   *
   * @param self::RCNOF_RATING_* $rcnofRating
   */
  public function setRcnofRating($rcnofRating)
  {
    $this->rcnofRating = $rcnofRating;
  }
  /**
   * @return self::RCNOF_RATING_*
   */
  public function getRcnofRating()
  {
    return $this->rcnofRating;
  }
  /**
   * The video's rating in Venezuela.
   *
   * Accepted values: resorteviolenciaUnspecified, resorteviolenciaA,
   * resorteviolenciaB, resorteviolenciaC, resorteviolenciaD, resorteviolenciaE,
   * resorteviolenciaUnrated
   *
   * @param self::RESORTEVIOLENCIA_RATING_* $resorteviolenciaRating
   */
  public function setResorteviolenciaRating($resorteviolenciaRating)
  {
    $this->resorteviolenciaRating = $resorteviolenciaRating;
  }
  /**
   * @return self::RESORTEVIOLENCIA_RATING_*
   */
  public function getResorteviolenciaRating()
  {
    return $this->resorteviolenciaRating;
  }
  /**
   * The video's General Directorate of Radio, Television and Cinematography
   * (Mexico) rating.
   *
   * Accepted values: rtcUnspecified, rtcAa, rtcA, rtcB, rtcB15, rtcC, rtcD,
   * rtcUnrated
   *
   * @param self::RTC_RATING_* $rtcRating
   */
  public function setRtcRating($rtcRating)
  {
    $this->rtcRating = $rtcRating;
  }
  /**
   * @return self::RTC_RATING_*
   */
  public function getRtcRating()
  {
    return $this->rtcRating;
  }
  /**
   * The video's rating from Ireland's Raidió Teilifís Éireann.
   *
   * Accepted values: rteUnspecified, rteGa, rteCh, rtePs, rteMa, rteUnrated
   *
   * @param self::RTE_RATING_* $rteRating
   */
  public function setRteRating($rteRating)
  {
    $this->rteRating = $rteRating;
  }
  /**
   * @return self::RTE_RATING_*
   */
  public function getRteRating()
  {
    return $this->rteRating;
  }
  /**
   * The video's National Film Registry of the Russian Federation (MKRF -
   * Russia) rating.
   *
   * Accepted values: russiaUnspecified, russia0, russia6, russia12, russia16,
   * russia18, russiaUnrated
   *
   * @param self::RUSSIA_RATING_* $russiaRating
   */
  public function setRussiaRating($russiaRating)
  {
    $this->russiaRating = $russiaRating;
  }
  /**
   * @return self::RUSSIA_RATING_*
   */
  public function getRussiaRating()
  {
    return $this->russiaRating;
  }
  /**
   * The video's rating in Slovakia.
   *
   * Accepted values: skfilmUnspecified, skfilmG, skfilmP2, skfilmP5, skfilmP8,
   * skfilmUnrated
   *
   * @param self::SKFILM_RATING_* $skfilmRating
   */
  public function setSkfilmRating($skfilmRating)
  {
    $this->skfilmRating = $skfilmRating;
  }
  /**
   * @return self::SKFILM_RATING_*
   */
  public function getSkfilmRating()
  {
    return $this->skfilmRating;
  }
  /**
   * The video's rating in Iceland.
   *
   * Accepted values: smaisUnspecified, smaisL, smais7, smais12, smais14,
   * smais16, smais18, smaisUnrated
   *
   * @param self::SMAIS_RATING_* $smaisRating
   */
  public function setSmaisRating($smaisRating)
  {
    $this->smaisRating = $smaisRating;
  }
  /**
   * @return self::SMAIS_RATING_*
   */
  public function getSmaisRating()
  {
    return $this->smaisRating;
  }
  /**
   * The video's rating from Statens medieråd (Sweden's National Media Council).
   *
   * Accepted values: smsaUnspecified, smsaA, smsa7, smsa11, smsa15, smsaUnrated
   *
   * @param self::SMSA_RATING_* $smsaRating
   */
  public function setSmsaRating($smsaRating)
  {
    $this->smsaRating = $smsaRating;
  }
  /**
   * @return self::SMSA_RATING_*
   */
  public function getSmsaRating()
  {
    return $this->smsaRating;
  }
  /**
   * The video's TV Parental Guidelines (TVPG) rating.
   *
   * Accepted values: tvpgUnspecified, tvpgY, tvpgY7, tvpgY7Fv, tvpgG, tvpgPg,
   * pg14, tvpgMa, tvpgUnrated
   *
   * @param self::TVPG_RATING_* $tvpgRating
   */
  public function setTvpgRating($tvpgRating)
  {
    $this->tvpgRating = $tvpgRating;
  }
  /**
   * @return self::TVPG_RATING_*
   */
  public function getTvpgRating()
  {
    return $this->tvpgRating;
  }
  /**
   * A rating that YouTube uses to identify age-restricted content.
   *
   * Accepted values: ytUnspecified, ytAgeRestricted
   *
   * @param self::YT_RATING_* $ytRating
   */
  public function setYtRating($ytRating)
  {
    $this->ytRating = $ytRating;
  }
  /**
   * @return self::YT_RATING_*
   */
  public function getYtRating()
  {
    return $this->ytRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentRating::class, 'Google_Service_YouTube_ContentRating');
