<form method="POST" action="userprocess.php">
  <input name="firstname" placeholder="First Name" required>
  <input name="lastname" placeholder="Last Name" required>
  <input name="email" type="email" required>
  <input name="password" type="password" required>

  <select name="role">
    <option value="Admin">Admin</option>
    <option value="Member">Member</option>
  </select>

  <button type="submit">Add User</button>
</form>

