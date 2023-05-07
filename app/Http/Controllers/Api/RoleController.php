<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateContentRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Role::query()
            ->where('name', 'like', "%$search%")
            ->with(['permissions'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
        $permissions['permissions'] = Permission::query()->get()->toArray();

        return RoleResource::collection($query)->additional($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContentRequest $request)
    {
        $data = $request->validated();
        $user = Role::create($data);

        return new RoleResource($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePermissionRequest $request, Role $role)
    {
        $data = $request->validated();

        $role->syncPermissions($data['permissions']);
        $role->update($data);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->noContent();
    }
}
