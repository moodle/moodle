CREATING NEW BLOCKS
-------------------------------------------------------------------------------

You have to derive a class that extends MoodleBlock.

The derived class MUST:

    * Implement a constructor that:
        1. Sets $this->content_type (BLOCK_TYPE_LIST or BLOCK_TYPE_TEXT)
        2. Sets $this->header (BLOCK_SHOW_HEADER or BLOCK_HIDE_HEADER)
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

        1. Define a method applicable_formats() which returns a
          bitwise AND of one or more COURSE_FORMAT_XXX defined
          constants.

    * Select a "preferred" width which the course format will try to honor.
      To do so:

        1. Define a method preferred_width() which returns a number
          measured in pixels.

    * Declare that the block is going to hide its header.
      To do so:

        1. Define a method hide_header() {return true;}
