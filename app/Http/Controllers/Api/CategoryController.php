<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    public function index()
    {
        $categories = Category::all();
        return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequest($request, [
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category already exists.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $category = Category::create($request->only('name', 'description'));
            DB::commit();

            return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category Store Error: ' . $e->getMessage());
            return $this->sendError('Something went wrong while creating category.');
        }
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }

        return $this->sendResponse(new CategoryResource($category), 'Category retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }

        $validator = $this->validateRequest($request, [
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $category->update($request->only('name', 'description'));
            DB::commit();

            return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category Update Error: ' . $e->getMessage());
            return $this->sendError('Something went wrong while updating category.');
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }

        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();

            return $this->sendResponse([], 'Category deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category Delete Error: ' . $e->getMessage());
            return $this->sendError('Something went wrong while deleting category.');
        }
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category || !$category->trashed()) {
            return $this->sendError('Category not found or not deleted.');
        }

        DB::beginTransaction();
        try {
            $category->restore();
            DB::commit();

            return $this->sendResponse(new CategoryResource($category), 'Category restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category Restore Error: ' . $e->getMessage());
            return $this->sendError('Something went wrong while restoring category.');
        }
    }
}

