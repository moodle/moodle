<?PHP  // $Id$

///////////////////////////////
/// CALCULATED HELPER CLASS ///
///////////////////////////////

/// OVERRIDDEN EDITION OF THE CLASS FOR QUESTION TYPE NUMERICAL ///

class quiz_calculated_qtype_numerical_helper extends quiz_numerical_qtype {
/// A question with qtype=CALCULATED will appear as a NUMERICAL
/// question in a quiz and it is therefore desirable to reuse
/// most of the grading and printing framework.
/// However, the CALCULATED functions will be fed with data
/// that differs from what the NUMERICAL qtype can handle.
/// Therefore the CALCULATED qtype often act by modifying the data
/// it has been fed and then pass it on to the NUMERICAL equivalent.

/// The NUMERICAL equivalent are called through an instance of this class,
/// for which the method get_answers has been modified so that its
/// caller will be fed with data fed to the qtype CALCULATED and then
/// modified to fit qtype NUMERICAL.

    // This solution assumes a single-threaded environment
    // for each instance...

    var $answers = false;

    function get_answers($question, $addedcondition='') {
        return $this->answers;
    }

    function set_answers($calculatedanswers) {
        $this->answers = $calculatedanswers;
    }
}
//// END OF CLASS ////

?>
