<?php

/**
 * Licensed to Jasig under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership.
 *
 * Jasig licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP Version 5
 *
 * @file     CAS/Language/Japanese.php
 * @category Authentication
 * @package  PhpCAS
 * @author   fnorif <fnorif@yahoo.co.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASC/phpCAS
 */

/**
 * Japanese language class. Now Encoding is EUC-JP and LF
 *
 * @class    CAS_Languages_Japanese
 * @category Authentication
 * @package  PhpCAS
 * @author   fnorif <fnorif@yahoo.co.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASC/phpCAS
 *
 **/
class CAS_Languages_Japanese implements CAS_Languages_LanguageInterface
{
    /**
     * Get the using server string
     *
     * @return string using server
     */
    public function getUsingServer()
    {
        return 'using server';
    }

    /**
     * Get authentication wanted string
     *
     * @return string authentication wanted
     */
    public function getAuthenticationWanted()
    {
        return 'CAS�ˤ��ǧ�ڤ�Ԥ��ޤ�';
    }

    /**
     * Get logout string
     *
     * @return string logout
     */
    public function getLogout()
    {
        return 'CAS����?�����Ȥ��ޤ�!';
    }

    /**
     * Get the should have been redirected string
     *
     * @return string should habe been redirected
     */
    public function getShouldHaveBeenRedirected()
    {
        return 'CAS�����Ф˹Ԥ�ɬ�פ�����ޤ�����ưŪ��ž������ʤ����� <a href="%s">������</a> �򥯥�å�����³�Ԥ��ޤ��';
    }

    /**
     * Get authentication failed string
     *
     * @return string authentication failed
     */
    public function getAuthenticationFailed()
    {
        return 'CAS�ˤ��ǧ�ڤ˼��Ԥ��ޤ���';
    }

    /**
     * Get the your were not authenticated string
     *
     * @return string not authenticated
     */
    public function getYouWereNotAuthenticated()
    {
        return '<p>ǧ�ڤǤ��ޤ���Ǥ���.</p><p>�⤦���٥ꥯ�����Ȥ������������<a href="%s">������</a>�򥯥�å�.</p><p>���꤬��褷�ʤ����� <a href="mailto:%s">���Υ����Ȥδ����</a>���䤤��碌�Ƥ�������.</p>';
    }

    /**
     * Get the service unavailable string
     *
     * @return string service unavailable
     */
    public function getServiceUnavailable()
    {
        return '�����ӥ� `<b>%s</b>\' �����ѤǤ��ޤ��� (<b>%s</b>).';
    }
}
?>