<?php
/*
 * @package TurnitinAPI
 * @subpackage TiiUser 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiUser data object which contains getters and setters for a Turnitin User object.
 * 
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiUser {
    private $email;
    private $firstname;
    private $lastname;
    private $defaultrole;
    private $userid;
    private $userids;
    private $usermessages;
    private $instructordefaults;
    private $defaultlanguage;
    private $accepteduseragreement;
    private $instructorrubrics;

    /**
     * Set the User ID for this User
     * 
     * @param integer $userid
     */
    public function setUserId( $userid ) {
        $this->userid = $userid;
    }
    
    /**
     * Get the User ID for this User
     * 
     * @return integer
     */
    public function getUserId() {
        return $this->userid;
    }

    /**
     * Set an array of User IDs for this User data object
     * 
     * @param array $userids
     */
    public function setUserIds( $userids ) {
        $this->userids = $userids;
    }
    
    /**
     * Get an array of User IDs for this User data object
     * 
     * @return array
     */
    public function getUserIds() {
        return $this->userids;
    }

    /**
     * Set the Email Address for this User
     * 
     * @param string $email
     */
    public function setEmail( $email ) {
        $this->email = $email;
    }
    
    /**
     * Get the Email Address for this User
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set the First Name for this User
     * 
     * @param string $firstname
     */
    public function setFirstName( $firstname ) {
        $this->firstname = $firstname;
    }
    
    /**
     * Get the First Name for this User
     * 
     * @return string
     */
    public function getFirstName() {
        return $this->firstname;
    }

    /**
     * Set the Last Name for this User
     * 
     * @param string $lastname
     */
    public function setLastName( $lastname ) {
        $this->lastname = $lastname;
    }
    
    /**
     * Get the Last Name for this User
     * 
     * @return string
     */
    public function getLastName() {
        return $this->lastname;
    }

    /**
     * Get the number of User Messages for this User
     * 
     * @return integer
     */
    public function getUserMessages() {
        return $this->usermessages;
    }

    /**
     * @ignore
     * Set the number of User Messages for this User
     * 
     * @param integer $usermessages
     */
    public function setUserMessages( $usermessages ) {
        $this->usermessages = $usermessages;
    }

    /**
     * Set the Default Role for this User
     *
     * Options are Instructor and Learner
     * 
     * @param string $defaultrole
     */
    public function setDefaultRole( $defaultrole ) {
        switch ( strtolower( $defaultrole ) ) {
            case 'student':
                $defaultrole = 'Learner';
                break;
            case 'learner':
                $defaultrole = 'Learner';
                break;
            case 'instructor':
                $defaultrole = 'Instructor';
                break;
        }
        $this->defaultrole = $defaultrole;
    }

    /**
     * Get the Default Role for this User
     *
     * Options are Instructor and Learner
     * 
     * @param string $defaultrole
     */
    public function getDefaultRole() {
        return $this->defaultrole;
    }
    
    /**
     * Get the Instructor Default Assignment settings for this user
     * 
     * Returns a prepopulated TiiAssignment data transfer object containing the instructor's default assignment settings
     * 
     * @return TiiAssignment
     */
    public function getInstructorDefaults() {
        $assignment = new TiiAssignment();
        $values = ( !empty( $this->instructordefaults ) ) ? json_decode( $this->instructordefaults ) : array();
        foreach ( $values as $key => $value ) {
            $method = 'set'.$key;
            if ( is_callable( array( $assignment, $method ) ) ) $value = $assignment->$method( $value );
        }
        return $assignment;
    }

    /**
     * @ignore
     * Set the Instructor Default Assignment settings for this user
     * 
     * @param string $instructordefaults
     */
    public function setInstructorDefaults($instructordefaults) {
        $this->instructordefaults = $instructordefaults;
    }

    /**
     * Get the Default Language for this user
     * 
     * Returns the following language codes:
     * 
     * <ul>
     * <li><b>en_us, en, en-EN, en-US</b><br />
     * English US</li>
     * <li><b>fr, fr-FR, fr_ca, fr-CA</b><br />
     * French</li>
     * <li><b>es, es-ES</b><br />
     * Spanish</li>
     * <li><b>de, de_du, de-DE, de-DU</b><br />
     * German</li>
     * <li><b>cn, zh_cn, zh-CN</b><br />
     * Simplified Chinese</li>
     * <li><b>zh_tw, zh-TW</b><br />
     * Traditional Chinese</li>
     * <li><b>pt, pt-PT, pt_br, pt-BR</b><br />
     * Portuguese</li>
     * <li><b>ja, ja-JA</b><br />
     * Japanese</li>
     * <li><b>ko, ko-KO</b><br />
     * Korean</li>
     * <li><b>tr, tr-TR</b><br />
     * Turkish</li>
     * <li><b>sv, sv-SV</b><br />
     * Swedish</li>
     * <li><b>nl, nl-NL</b><br />
     * Dutch</li>
     * <li><b>fi, fi-FI</b><br />
     * Finnish</li>
     * <li><b>ar, ar-AR</b><br />
     * Arabic</li>
     * </ul>
     * 
     * @return string
     */
    public function getDefaultLanguage() {
        return $this->defaultlanguage;
    }

    /**
     * Set the Default Language for this user
     * 
     * Accepts the following language codes:
     * 
     * <ul>
     * <li><b>en_us, en, en-EN, en-US</b><br />
     * English US</li>
     * <li><b>fr, fr-FR, fr_ca, fr-CA</b><br />
     * French</li>
     * <li><b>es, es-ES</b><br />
     * Spanish</li>
     * <li><b>de, de_du, de-DE, de-DU</b><br />
     * German</li>
     * <li><b>cn, zh_cn, zh-CN</b><br />
     * Simplified Chinese</li>
     * <li><b>zh_tw, zh-TW</b><br />
     * Traditional Chinese</li>
     * <li><b>pt, pt-PT, pt_br, pt-BR</b><br />
     * Portuguese</li>
     * <li><b>ja, ja-JA</b><br />
     * Japanese</li>
     * <li><b>ko, ko-KO</b><br />
     * Korean</li>
     * <li><b>tr, tr-TR</b><br />
     * Turkish</li>
     * <li><b>sv, sv-SV</b><br />
     * Swedish</li>
     * <li><b>nl, nl-NL</b><br />
     * Dutch</li>
     * <li><b>fi, fi-FI</b><br />
     * Finnish</li>
     * <li><b>ar, ar-AR</b><br />
     * Arabic</li>
     * </ul>
     * 
     * @param string $defaultlanguage
     */
    public function setDefaultLanguage($defaultlanguage) {
        $langarray = array(
            'en'=>'en_us',
            'en_us'=>'en_us',
            'en-EN'=>'en_us',
            'en-US'=>'en_us',
            'fr'=>'fr',
            'fr-FR'=>'fr',
            'fr_ca'=>'fr',
            'fr-CA'=>'fr',
            'es'=>'es',
            'es-ES'=>'es',
            'de'=>'de',
            'de-DE'=>'de',
            'de_du'=>'de',
            'de-DU'=>'de',
            'cn'=>'zh_hans',
            'zh_cn'=>'zh_hans',
            'zh-CN'=>'zh_hans',
            'zh_tw'=>'zh_tw',
            'zh-TW'=>'zh_tw',
            'pt_br'=>'pt_br',
            'pt'=>'pt_br',
            'pt-PT'=>'pt_br',
            'pt-BR'=>'pt_br',
            'th'=>'th',
            'th-TH'=>'th',
            'ja'=>'ja',
            'ja-JA'=>'ja',
            'ko'=>'ko',
            'ko-KO'=>'ko',
            'ms'=>'ms',
            'ms-MS'=>'ms',
            'tr'=>'tr',
            'tr-TR'=>'tr',
            'ca'=>'es',
            'ca-CA'=>'es',
            'sv'=>'sv',
            'sv-SV'=>'sv',
            'nl'=>'nl',
            'nl-NL'=>'nl',
            'fi'=>'fi',
            'fi-FI'=>'fi',
            'ar'=>'ar',
            'ar-AR'=>'ar'
        );
        $this->defaultlanguage = (isset( $langarray[$defaultlanguage] )) ? $langarray[$defaultlanguage] : 'en_us';
    }
    
    /**
     * Get the Boolean that determines if this user has accepted the latest available Turnitin User Agreement
     * 
     * @return boolean
     */
    public function getAcceptedUserAgreement() {
        return (boolean)(integer)$this->accepteduseragreement;
    }

    /**
     * @ignore
     * Set the Boolean that determines if this user has accepted the latest available Turnitin User Agreement
     * 
     * @param boolean $accepteduseragreement
     */
    public function setAcceptedUserAgreement(  $accepteduseragreement ) {
        $this->accepteduseragreement = $accepteduseragreement;
    }

    /**
     * Get any rubrics the instructor owns in Turnitin
     *
     * Returns an array of Rubric objects
     * 
     * @return array
     */
    public function getInstructorRubrics() {
        $rubrics = array();
        $values = ( !empty( $this->instructorrubrics ) ) ? json_decode( $this->instructorrubrics ) : array();

        foreach ($values as $key => $instructor_rubric) {
            $rubric = new TiiRubric();
            foreach ($instructor_rubric as $k => $v ) {
                $method = 'set'.$k;
                if (is_callable(array($rubric, $method))) {
                    $rubric->$method( $v );
                }
            }
            $rubrics[] = $rubric;
        }

        return $rubrics;
    }

    /**
     * @ignore
     * Set the Instructor Rubrics for this user
     * 
     * @param string $instructorrubrics
     */
    public function setInstructorRubrics($instructorrubrics) {
        $this->instructorrubrics = $instructorrubrics;
    }

}

