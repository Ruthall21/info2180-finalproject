<?php
// addContact.php
?>
<div class="form-box">
  <h2>New Contact</h2>

  <form id="addContactForm" method="POST" action="addcontactprocess.php">
    <!-- Title -->
    <div class="form-row">
      <div class="form-group">
        <label for="title">Title</label>
        <select name="title" id="title">
          <option value="Mr">Mr</option>
          <option value="Mrs">Mrs</option>
          <option value="Ms">Ms</option>
        </select>
      </div>
    </div>

    
    <div class="form-row">
      <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" id="firstname" required>
      </div>
      <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname" required>
      </div>
    </div>

    
    <div class="form-row">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="form-group">
        <label for="telephone">Telephone</label>
        <input type="text" name="telephone" id="telephone">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="company">Company</label>
        <input type="text" name="company" id="company">
      </div>
      <div class="form-group">
        <label for="type">Type</label>
        <select name="type" id="type">
          <option value="Sales">Sales Lead</option>
          <option value="Support">Support</option>
        </select>
      </div>
    </div>

    
    <div class="form-row" style="justify-content:flex-end;">
      <button type="submit" class="btn-save">Save</button>
    </div>
  </form>
</div>



