<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AcademicYear::class);

        $query = AcademicYear::with('school');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('school_id') && ! empty($request->school_id)) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('is_current')) {
            $query->where('is_current', filter_var($request->is_current, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Academic years retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', AcademicYear::class);

        $validated = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive,archived'],
        ]);

        if (! empty($validated['is_current']) && $validated['is_current']) {
            AcademicYear::where('school_id', $validated['school_id'])->update(['is_current' => false]);
        }

        $ay = AcademicYear::create($validated);

        return $this->success($ay, 'Academic year created successfully.', 201);
    }

    public function show(AcademicYear $academicYear): JsonResponse
    {
        $this->authorize('view', $academicYear);

        return $this->success($academicYear->load('school'), 'Academic year retrieved successfully.');
    }

    public function update(Request $request, AcademicYear $academicYear): JsonResponse
    {
        $this->authorize('update', $academicYear);

        $validated = $request->validate([
            'school_id' => ['sometimes', 'exists:schools,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'status' => ['sometimes', 'in:active,inactive,archived'],
        ]);

        if (! empty($validated['is_current']) && $validated['is_current']) {
            AcademicYear::where('school_id', $academicYear->school_id)->update(['is_current' => false]);
        }

        $academicYear->update($validated);

        return $this->success($academicYear, 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        $this->authorize('delete', $academicYear);

        $academicYear->delete();

        return $this->success(null, 'Academic year deleted successfully.');
    }
}
