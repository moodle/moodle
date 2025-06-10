<?php

namespace Integrations\PhpSdk;

/**
 * @ignore
 */
class TiiForm {

    private $formtarget;
    private $buttonstyle;
    private $buttontext;
    private $hasbutton;
    private $widemode;

    /**
     * Determines if the LTI form output should have a submit button
     *
     * @return boolean
     */
    public function getHasButton() {
        return ( is_null( $this->hasbutton ) ) ? true : $this->hasbutton;
    }

    /**
     * Determines if the LTI form output should have a submit button
     *
     * @param type $hasbutton
     */
    public function setHasButton(  $hasbutton) {
        $this->hasbutton = $hasbutton;
    }

    /**
     * Get the Button Text for an LTI Launch submit button
     *
     * @return string
     */
    public function getButtonText() {
        return $this->buttontext;
    }

    /**
     * Set the Button Text for an LTI Launch submit button
     *
     * @param string $buttontext
     */
    public function setButtonText( $buttontext ) {
        $this->buttontext = $buttontext;
    }

    /**
     * Get the Button Style for an LTI Launch submit button
     *
     * @return string
     */
    public function getButtonStyle() {
        return $this->buttonstyle;
    }

    /**
     * Set the Button Style for an LTI Launch submit button
     *
     * @param string $buttonstyle
     */
    public function setButtonStyle( $buttonstyle ) {
        $this->buttonstyle = $buttonstyle;
    }

    /**
     * Get the Form Target for an LTI Launch
     *
     * A window target to display the LTI launch destination screen in
     *
     * @return string
     */
    public function getFormTarget() {
        return isset( $this->formtarget ) ? $this->formtarget : '_blank';
    }

    /**
     * Set the Form Target for an LTI Launch
     *
     * A window target to display the LTI launch destination screen in
     *
     * @param string $formtarget
     */
    public function setFormTarget( $formtarget ) {
        $this->formtarget = $formtarget;
    }

    /**
     * Gets a the Boolean WideMode, this boolean determines the layout of the LTI UI, by default the view will be fixed width 'modal' output.
     * By setting WideMode to true the layout will be not be fixed width and will stretch to fit the window / frame
     *
     * @return boolean
     */
    public function getWideMode() {
        return (boolean)(integer)$this->widemode;
    }

    /**
     * Sets a the Boolean WideMode, this boolean determines the layout of the LTI UI, by default the view will be fixed width 'modal' output.
     * By setting WideMode to true the layout will be not be fixed width and will stretch to fit the window / frame
     *
     * @param boolean $widemode
     */
    public function setWideMode( $widemode ) {
        $this->widemode = $widemode;
    }

}

