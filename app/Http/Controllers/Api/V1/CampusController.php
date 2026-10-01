<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Campus::class);

        $query = Campus::with('school');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('school_id') && ! empty($request->school_id)) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Campuses retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Campus::class);

        $validated = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $campus = Campus::create($validated);

        return $this->success($campus, 'Campus created successfully.', 201);
    }

    public function show(Campus $campus): JsonResponse
    {
        $this->authorize('view', $campus);

        return $this->success($campus->load('school'), 'Campus retrieved successfully.');
    }

    public function update(Request $request, Campus $campus): JsonResponse
    {
        $this->authorize('update', $campus);

        $validated = $request->validate([
            'school_id' => ['sometimes', 'exists:schools,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $campus->update($validated);

        return $this->success($campus, 'Campus updated successfully.');
    }

    public function destroy(Campus $campus): JsonResponse
    {
        $this->authorize('delete', $campus);

        $campus->delete();

        return $this->success(null, 'Campus deleted successfully.');
    }
}
