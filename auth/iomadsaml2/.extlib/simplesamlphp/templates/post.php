<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>POST data</title>
    <script src="/<?php echo $this->data['baseurlpath']; ?>resources/post.js"></script>
    <link type="text/css" rel="stylesheet" href="/<?php echo $this->data['baseurlpath']; ?>resources/post.css" />
</head>
<body>

    <noscript>
        <p><strong>Note:</strong> 
        Since your browser does not support JavaScript, 
        you must press the button below once to proceed.</p> 
    </noscript> 

    <form method="post" 
        action="<?php echo htmlspecialchars($this->data['destination']); ?>">
    <!-- Need to add this element and call click method, because calling 
    submit() on the form causes failed submission if the form has another 
    element with name or id of submit.
    See: https://developer.mozilla.org/en/DOM/form.submit#Specification -->
    <input type="submit" id="postLoginSubmitButton"/>
<?php
if (array_key_exists('post', $this->data)) {
    $post = $this->data['post'];
} else {
    // For backwards compatibility
    assert(array_key_exists('response', $this->data));
    assert(array_key_exists('RelayStateName', $this->data));
    assert(array_key_exists('RelayState', $this->data));
        $post = [
        'SAMLResponse' => $this->data['response'],
        $this->data['RelayStateName'] => $this->data['RelayState'],
        ];
}

/**
 * Write out one or more INPUT elements for the given name-value pair.
 *
 * If the value is a string, this function will write a single INPUT element.
 * If the value is an array, it will write multiple INPUT elements to
 * recreate the array.
 *
 * @param string $name  The name of the element.
 * @param string|array $value  The value of the element.
 */
function printItem($name, $value)
{
    assert(is_string($name));
    assert(is_string($value) || is_array($value));
    if (is_string($value)) {
        echo '<input type="hidden" name="'.
            htmlspecialchars($name).'" value="'.
            htmlspecialchars($value).'" />';
        return;
    }
    // This is an array...
    foreach ($value as $index => $item) {
        printItem($name.'['.$index.']', $item);
    }
}

foreach ($post as $name => $value) {
    printItem($name, $value);
}
?>
        <noscript>
            <button type="submit" class="btn">Submit</button>
        </noscript>
    </form>

</body>
</html>
