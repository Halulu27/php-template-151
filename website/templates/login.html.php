
 <form method="POST">
  Email:<br>
  <input type="text" name="email" value="<?= (isset($email)) ? $email: "" ?>"><br>
  Password:<br>
  <input type="text" name="password" value=""><br><br>
  <input type="submit" value="Submit">
</form> 