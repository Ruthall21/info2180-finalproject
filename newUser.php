<?php
// newUser.php
?>
<div class="form-box">
  <h2>New User</h2>

  <form method="POST" action="newuserprocess.php">
    <div class="form-row">
      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="firstname">
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="lastname">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Role</label>
        <select name="role">
          <option>Member</option>
          <option>Admin</option>
        </select>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn-save">Save</button>
    </div>
  </form>
</div>



