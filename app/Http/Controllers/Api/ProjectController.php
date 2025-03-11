<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $request->user()->projects()->latest()->paginate();

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): ProjectResource
    {
        $project = $request->user()->projects()->create([
            ...$request->validated(),
            'status' => ProjectStatus::PENDING->value,
            'share_hash' => Hash::make($request->validated('share_password')),
        ]);

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, UpdateProjectRequest $request): ProjectResource
    {
        Gate::authorize('update', $project);

        $project->update([
            ...$request->validated(),
            'share_hash' => Hash::make($request->validated('share_password')),
        ]);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): ProjectResource
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return new ProjectResource($project);
    }
}
