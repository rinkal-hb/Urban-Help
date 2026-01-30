<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    /**
     * Display categories page
     */
    public function index()
    {
        return view('admin.categories.index');
    }

    /**
     * Get categories data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $draw = filter_var($request->get('draw'), FILTER_VALIDATE_INT);
        $start = filter_var($request->get('start'), FILTER_VALIDATE_INT);
        $rowperpage = filter_var($request->get('length'), FILTER_VALIDATE_INT);

        $order_arr = $request->get('order', []);
        $searchValue = $request->get('search')['value'] ?? '';

        $columnIndex = $order_arr[0]['column'] ?? 0;
        $columns = ['id', 'name', 'description', 'status'];
        $columnName = $columns[$columnIndex] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';

        $query = Category::query();

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('description', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $query->count();

        $categories = $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($categories as $index => $category) {
            $data_arr[] = [
                'id' => $category->id,
                'no' => $start + $index + 1,
                'name' => $category->name,
                'description' => $category->description ?? 'N/A',
                'image' => $category->image ? '<img src="' . Storage::url($category->image) . '" width="50" height="50" class="rounded">' : '<img src="' . asset('assets/img/avatar.png') . '" width="50" height="50" class="rounded">',
                'status' => $category->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>',
                'action' => '<div class="btn-group" role="group">
                <button class="btn btn-sm btn-primary" onclick="editCategory(' . $category->id . ')"><i class="ri-edit-line"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCategory(' . $category->id . ')"><i class="ri-delete-bin-line"></i></button>
                            </div>'

            ];
        }

        return response()->json([
            'draw' => $draw,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'data' => $data_arr
        ]);
    }

    /**
     * Store new category
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? 1 : 0
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = 'uploads/categories';
                $data['image'] = uploadImageToStorage($image, $imagePath);
            }

            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category for editing
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        // Add method spoofing support for PUT requests
        // if ($request->has('_method') && $request->_method === 'PUT') {
        //     $request->setMethod('PUT');
        // }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'request_data' => $request->all() // Debug info
            ], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? 1 : 0
            ];

            $imageUpdated = false;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $image = $request->file('image');
                $imagePath = 'uploads/categories';
                $data['image'] = uploadImageToStorage($image, $imagePath);
                $imageUpdated = true;
            }

            // Fill the model with new data
            $category->fill($data);

            // Check if any changes were made
            if (!$category->isDirty() && !$imageUpdated) {
                return response()->json([
                    'success' => true,
                    'message' => 'No changes detected',
                    'data' => $category
                ]);
            }

            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            // Check if category has services
            if ($category->services()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with existing services'
                ], 422);
            }

            // Delete image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
