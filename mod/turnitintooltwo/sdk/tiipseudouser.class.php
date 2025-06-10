<?php

/*
 * @package TurnitinAPI
 * @subpackage TiiPseudoUser
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Defines the TiiPseudo data object which contains getters and setters for a Turnitin Pseudo User object.
 *
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiPseudoUser extends TiiUser {

    private $pseudodomain;
    private $pseudosalt;
    private $email;

    /**
     * Instantiate the Pseudo User object
     *
     * The parameter is the domain that is to be used with fake / pseudo users email addresses
     *
     * @param string $pseudodomain
     */
    public function __construct( $pseudodomain ) {
        $this->setPseudoDomain( $pseudodomain );
    }

    /**
     * Set the Email Address for this Pseudo User
     *
     * Takes a real LMS email address which is converted into a fake / pseudo email address when retrieved.
     * This function is mainly useful when personally identifiable user data should not be transmitted and stored in Turnitin.
     *
     * @return string
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get the Email Address for this Pseudo User
     *
     * This function returns a fake user email, created using the pseudo domain and pseudo salt to SHA hash
     * the email address and form a fake / pseudo email address by combining the hash with the Pseudo Domain
     *
     * @return string
     */
    public function getEmail() {
        if ( substr( $this->pseudodomain, 0, 1 ) != '@' ) {
            $this->pseudodomain = '@' . $this->pseudodomain;
        }
        return sha1( $this->email.$this->pseudosalt ) . $this->pseudodomain;
    }

    /**
     * Get the Pseudo Domain for this Pseudo User
     *
     * @return string
     */
    public function getPseudoDomain() {
        return $this->pseudodomain;
    }

    /**
     * Set the Pseudo Domain for this Pseudo User
     *
     * @param string $pseudodomain
     */
    private function setPseudoDomain($pseudodomain) {
        $this->pseudodomain = $pseudodomain;
    }

    /**
     * Get the Pseudo Salt for this Pseudo User
     *
     * A salt string to use in order to make the Pseudo User Email address less reproducable
     *
     * @return text
     */
    public function getPseudoSalt() {
        return $this->pseudosalt;
    }

    /**
     * Set the Pseudo Salt for this Pseudo User
     *
     * A salt string to use in order to make the Pseudo User Email address less reproducable
     *
     * @param text $pseudosalt
     */
    public function setPseudoSalt($pseudosalt) {
        $this->pseudosalt = $pseudosalt;
    }

}

//?>