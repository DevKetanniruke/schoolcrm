<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SchoolClass::class);

        $query = SchoolClass::with('department.school')->withCount('divisions');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('department_id') && ! empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Classes retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', SchoolClass::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'year_level' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $schoolClass = SchoolClass::create($validated);

        return $this->success($schoolClass, 'Class created successfully.', 201);
    }

    public function show(SchoolClass $class): JsonResponse
    {
        $this->authorize('view', $class);

        return $this->success($class->load(['department.school', 'divisions']), 'Class retrieved successfully.');
    }

    public function update(Request $request, SchoolClass $class): JsonResponse
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'department_id' => ['sometimes', 'exists:departments,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'year_level' => ['nullable', 'integer'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $class->update($validated);

        return $this->success($class, 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class): JsonResponse
    {
        $this->authorize('delete', $class);

        $class->delete();

        return $this->success(null, 'Class deleted successfully.');
    }
}
