 <!-- Create Service Modal -->
 <div class="modal fade" id="createServiceModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Create New Service</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form id="createServiceForm" enctype="multipart/form-data" novalidate>
                 @csrf
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                         <select class="form-select" id="category_id" name="category_id">
                             <option value="">Select Category</option>
                         </select>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="name" class="form-label">Service Name <span
                                 class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="name" name="name">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="description" class="form-label">Description</label>
                         <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="price" class="form-label">Price (₹) <span
                                         class="text-danger">*</span></label>
                                 <input type="number" class="form-control" id="price" name="price" step="0.01"
                                     min="0">
                                 <div class="invalid-feedback"></div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="duration" class="form-label">Duration (minutes) <span
                                         class="text-danger">*</span></label>
                                 <input type="number" class="form-control" id="duration" name="duration"
                                     min="1">
                                 <div class="invalid-feedback"></div>
                             </div>
                         </div>
                     </div>
                     <div class="mb-3">
                         <label for="image" class="form-label">Image</label>
                         <input type="file" class="form-control" id="image" name="image" accept="image/*">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="status" name="status"
                                 value="1" checked>
                             <label class="form-check-label" for="status">Active</label>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Create Service</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Edit Service Modal -->
 <div class="modal fade" id="editServiceModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Edit Service</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form method="POST" id="editServiceForm" enctype="multipart/form-data" novalidate>
                 @csrf
                 <input type="text" id="edit_service_id" name="service_id">
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="edit_category_id" class="form-label">Category <span
                                 class="text-danger">*</span></label>
                         <select class="form-select" id="edit_category_id" name="category_id">
                             <option value="">Select Category</option>
                         </select>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="edit_name" class="form-label">Service Name <span
                                 class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="edit_name" name="name">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="edit_description" class="form-label">Description</label>
                         <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="edit_price" class="form-label">Price (₹) <span
                                         class="text-danger">*</span></label>
                                 <input type="number" class="form-control" id="edit_price" name="price"
                                     step="0.01" min="0">
                                 <div class="invalid-feedback"></div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="edit_duration" class="form-label">Duration (minutes) <span
                                         class="text-danger">*</span></label>
                                 <input type="number" class="form-control" id="edit_duration" name="duration"
                                     min="1">
                                 <div class="invalid-feedback"></div>
                             </div>
                         </div>
                     </div>
                     <div class="mb-3">
                         <label for="edit_image" class="form-label">Image</label>
                         <input type="file" class="form-control" id="edit_image" name="image"
                             accept="image/*">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="edit_status" name="status"
                                 value="1">
                             <label class="form-check-label" for="edit_status">Active</label>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Update Service</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
