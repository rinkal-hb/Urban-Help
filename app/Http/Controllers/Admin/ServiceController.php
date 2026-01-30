<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    /**
     * Display services page
     */
    public function index()
    {
        return view('admin.services.index');
    }

    /**
     * Get services data for DataTables
     */
    public function data(Request $request): JsonResponse
    {
        $draw = filter_var($request->get('draw'), FILTER_VALIDATE_INT);
        $start = filter_var($request->get('start'), FILTER_VALIDATE_INT);
        $rowperpage = filter_var($request->get('length'), FILTER_VALIDATE_INT);

        $order_arr = $request->get('order', []);
        $searchValue = $request->get('search')['value'] ?? '';

        $columnIndex = $order_arr[0]['column'] ?? 0;
        $columns = ['id', 'name', 'category_id', 'price', 'duration', 'status'];
        $columnName = $columns[$columnIndex] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';

        $query = Service::with('category');

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('description', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($searchValue) {
                        $categoryQuery->where('name', 'like', '%' . $searchValue . '%');
                    });
            });
        }

        $totalRecords = $query->count();

        $services = $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($services as $index => $service) {
            $data_arr[] = [
                'id' => $service->id,
                'no' => $start + $index + 1,
                'name' => $service->name,
                'category_name' => $service->category ? $service->category->name : 'N/A',
                'price' => '₹' . number_format($service->price, 2),
                'duration' => $service->duration . ' min',
                'image' => $service->image ? '<img src="' . Storage::url($service->image) . '" width="50" height="50" class="rounded">' : '<img src="' . asset('assets/img/avatar.png') . '" width="50" height="50" class="rounded">',
                'status' => '<div class="form-check form-switch">
                    <input class="form-check-input status-toggle" type="checkbox" data-id="' . $service->id . '" ' . ($service->status ? 'checked' : '') . '>
                </div>',
                'action' => '<div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary" onclick="editService(' . $service->id . ')"><i class="ri-edit-line"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteService(' . $service->id . ')"><i class="ri-delete-bin-line"></i></button>
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
     * Store new service
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
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
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'status' => $request->has('status') ? 1 : 0
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = 'uploads/services';
                $data['image'] = uploadImageToStorage($image, $imagePath);
            }

            $service = Service::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service for editing
     */
    public function show(Service $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Update service
     */
    public function update(Request $request, Service $service): JsonResponse
    {

       
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
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
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'status' => $request->has('status') ? 1 : 0
            ];

            $imageUpdated = false;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                $image = $request->file('image');
                $imagePath = 'uploads/services';
                $data['image'] = uploadImageToStorage($image, $imagePath);
                $imageUpdated = true;
            }

            // Fill the model with new data
            $service->fill($data);

            // Check if any changes were made
            if (!$service->isDirty() && !$imageUpdated) {
                return response()->json([
                    'success' => true,
                    'message' => 'No changes detected',
                    'data' => $service
                ]);
            }

            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete service
     */
    public function destroy(Service $service): JsonResponse
    {
        try {
            // Check if service has bookings
            if ($service->bookings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete service with existing bookings'
                ], 422);
            }

            // Delete image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(Request $request, Service $service): JsonResponse
    {
        try {
            $service->update(['status' => !$service->status]);

            return response()->json([
                'success' => true,
                'status' => $service->status,
                'message' => 'Service status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active categories for dropdown
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
