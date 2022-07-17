<div>
<!-- Ok. -->
<?php echo $var; ?>
Some content here.

<!-- Bad. Script style open tags. -->
<script language="php">
echo $var;
</script>
<script language='php'>echo $var;</script>

<!-- Bad. Invalid script style open tags. -->
<script type="text/php" language="php">
echo $var;
</script>
<script language='PHP' type='text/php'>
echo $var;
</script>

<!-- Bad. ASP style open tags. -->
<% echo $var; %>
<p>Some text <% echo $var; %> and some more text</p>
<%= $var . ' and some more text to make sure the snippet works'; %>
<p>Some text <%= $var %> and some more text</p>

</div>
