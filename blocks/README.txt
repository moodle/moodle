-------------------------------------------------------------------------------
CREATING NEW BLOCKS
-------------------------------------------------------------------------------

-------------------------------------------------------------------------------
WARNING - PRELIMINARY DOCUMENTATION
This is designed to point new block developers in the right direction. At times
it may NOT be fully up-to-date with the source, or it may even contain some
tiny bit of misinformation that has slipped our notice. If you encounter such a
case, please:
    1. Use the existing block code as reference
    2. Come to the moodle.org forums and tell the world! :) We 'll help you!
-------------------------------------------------------------------------------

You have to derive a class that extends MoodleBlock.

The derived class MUST:

    * Implement a constructor that:
        1. Sets $this->content_type (BLOCK_TYPE_LIST or BLOCK_TYPE_TEXT)
        3. Sets $this->title
        4. Sets $this->version
        5. Sets $this->course equal to its only argument

The derived class MAY:

    * Declare that the block has a configuration interface.
      To do so:

        1. Define a method has_config() {return true;}
        2. Define a method print_config() that prints whatever
           configuration interface you want to have.
        3. Define a method handle_config($data) that does what
           is needed. $data comes straight from data_submitted().

    * Limit the course formats it can be displayed in.
      To do so:

        1. Define a method applicable_formats() which returns a bitwise
           OR of one or more COURSE_FORMAT_XXX defined constants. These
           are defined in lib/blocklib.php.

    * Select a "preferred" width which the course format will try to honor.
      To do so:

        1. Define a method preferred_width() which returns an integer.
           This is the block's preferred width in pixels.

    * Declare that the block is going to hide its header. This will result
      in a more lightweight appearance. Ideal for announcements/notices.
      To do so:

        1. Define a method hide_header() {return true;}
