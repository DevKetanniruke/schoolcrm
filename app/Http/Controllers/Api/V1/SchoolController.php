<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', School::class);

        $query = School::with(['organization'])->withCount(['campuses', 'departments', 'users']);

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('organization_id') && ! empty($request->organization_id)) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Schools retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', School::class);

        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['required', 'string', 'max:50', 'unique:schools,code', new SanitizedTextRule],
            'domain' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
        ]);

        $school = School::create($validated);

        return $this->success($school, 'School created successfully.', 201);
    }

    public function show(School $school): JsonResponse
    {
        $this->authorize('view', $school);

        return $this->success($school->load(['organization', 'campuses', 'academicYears', 'departments']), 'School retrieved successfully.');
    }

    public function update(Request $request, School $school): JsonResponse
    {
        $this->authorize('update', $school);

        $validated = $request->validate([
            'organization_id' => ['sometimes', 'exists:organizations,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['sometimes', 'string', 'max:50', 'unique:schools,code,' . $school->id, new SanitizedTextRule],
            'domain' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ]);

        $school->update($validated);

        return $this->success($school, 'School updated successfully.');
    }

    public function destroy(School $school): JsonResponse
    {
        $this->authorize('delete', $school);

        $school->delete();

        return $this->success(null, 'School deleted successfully.');
    }
}
