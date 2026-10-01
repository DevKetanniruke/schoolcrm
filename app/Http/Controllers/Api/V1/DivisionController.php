<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Division::class);

        $query = Division::with('schoolClass.department.school');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id') && ! empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Divisions retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Division::class);

        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $division = Division::create($validated);

        return $this->success($division, 'Division created successfully.', 201);
    }

    public function show(Division $division): JsonResponse
    {
        $this->authorize('view', $division);

        return $this->success($division->load('schoolClass.department.school'), 'Division retrieved successfully.');
    }

    public function update(Request $request, Division $division): JsonResponse
    {
        $this->authorize('update', $division);

        $validated = $request->validate([
            'class_id' => ['sometimes', 'exists:school_classes,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $division->update($validated);

        return $this->success($division, 'Division updated successfully.');
    }

    public function destroy(Division $division): JsonResponse
    {
        $this->authorize('delete', $division);

        $division->delete();

        return $this->success(null, 'Division deleted successfully.');
    }
}
