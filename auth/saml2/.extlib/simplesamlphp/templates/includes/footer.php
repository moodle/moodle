<?php
if (!empty($this->data['htmlinject']['htmlContentPost'])) {
    foreach ($this->data['htmlinject']['htmlContentPost'] as $c) {
        echo $c;
    }
}
?>
            </div><!-- #content -->
            <div id="footer">
                <hr />
                <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/ssplogo-fish-small.png" alt="Small fish logo" style="float: right" />		
                    Copyright &copy; 2007-2021 <a href="https://uninett.no/">UNINETT AS</a>

                <br style="clear: right" />

            </div><!-- #footer -->
        </div><!-- #wrap -->
    </body>
</html>
