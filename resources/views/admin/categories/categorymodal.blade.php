 <!-- Create Category Modal -->
 <div class="modal fade" id="createCategoryModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Create New Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form id="createCategoryForm" enctype="multipart/form-data" novalidate>
                 @csrf
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="name" class="form-label">Category Name <span
                                 class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="name" name="name">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="description" class="form-label">Description</label>
                         <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="image" class="form-label">Image</label>
                         <input type="file" class="form-control" id="image" name="image" accept="image/*">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                             <label class="form-check-label" for="status">Active</label>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Create Category</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Edit Category Modal -->
 <div class="modal fade" id="editCategoryModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Edit Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form method="POST" id="editCategoryForm" enctype="multipart/form-data" novalidate>
                 @csrf
                 <input type="hidden" id="edit_category_id" name="category_id">
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="edit_name" class="form-label">Category Name <span
                                 class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="edit_name" name="name">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="edit_description" class="form-label">Description</label>
                         <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <label for="edit_image" class="form-label">Image</label>
                         <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                         <div class="invalid-feedback"></div>
                     </div>
                     <div class="mb-3">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="edit_status" name="status">
                             <label class="form-check-label" for="edit_status">Active</label>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Update Category</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
