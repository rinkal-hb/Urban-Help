  <!-- Create User Modal -->
  <div class="modal fade" id="createUserModal" tabindex="-1">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Create New User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <form id="createUserForm">
                      <div class="mb-3">
                          <label for="name" class="form-label">Full Name</label>
                          <input type="text" class="form-control" id="name" name="name" required>
                      </div>
                      <div class="mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" class="form-control" id="email" name="email" required>
                      </div>
                      <div class="mb-3">
                          <label for="phone" class="form-label">Phone</label>
                          <input type="tel" class="form-control" id="phone" name="phone" required>
                      </div>
                      <div class="mb-3">
                          <label for="role" class="form-label">Role</label>
                          <select class="form-select" id="role" name="role" required>
                              <option value="">Select Role</option>
                              <option value="admin">Admin</option>
                              <option value="customer">Customer</option>
                              <option value="provider">Provider</option>
                          </select>
                      </div>
                      <div class="mb-3">
                          <label for="password" class="form-label">Password</label>
                          <input type="password" class="form-control" id="password" name="password" required>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" form="createUserForm" class="btn btn-primary">Create User</button>
              </div>
          </div>
      </div>
  </div>
