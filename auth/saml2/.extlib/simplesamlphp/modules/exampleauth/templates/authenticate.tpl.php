<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>exampleauth login page</title>
  </head>
  <body>
    <h1>exampleauth login page</h1>
    <p>
      In this example you can log in with two accounts: <code>student</code> and <code>admin</code>.
      In both cases, the password is the same as the username.
    </p>
    <form method="post" action="?">
      <p>
        Username:
        <input type="text" name="username">
      </p>
      <p>
        Password:
        <input type="text" name="password">
      </p>
      <input type="hidden" name="ReturnTo" value="<?= htmlspecialchars($this->data['returnTo']) ?>">
      <p><input type="submit" value="Log in"></p>
    </form>
<?php if($this->data['badUserPass']): ?>
    <p>!!! Bad username or password !!!</p>
<?php endif; ?>
  </body>
</html>
