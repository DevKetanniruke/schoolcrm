<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Rules\SanitizedTextRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subject::class);

        $query = Subject::with('department.school');

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

        if ($request->has('type') && ! empty($request->type)) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, 'Subjects retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Subject::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['required', 'string', 'max:50', new SanitizedTextRule],
            'credits' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', 'in:core,elective,optional'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $subject = Subject::create($validated);

        return $this->success($subject, 'Subject created successfully.', 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        return $this->success($subject->load('department.school'), 'Subject retrieved successfully.');
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('update', $subject);

        $validated = $request->validate([
            'department_id' => ['sometimes', 'exists:departments,id'],
            'name' => ['sometimes', 'string', 'max:255', new SanitizedTextRule],
            'code' => ['sometimes', 'string', 'max:50', new SanitizedTextRule],
            'credits' => ['nullable', 'integer', 'min:1'],
            'type' => ['sometimes', 'in:core,elective,optional'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $subject->update($validated);

        return $this->success($subject, 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return $this->success(null, 'Subject deleted successfully.');
    }
}
