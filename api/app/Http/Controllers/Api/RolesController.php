<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Role;
use App\Http\Resources\Role as RoleResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;

class RolesController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->middleware(['permission:manage_roles']);
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request)
    {
        if ($request->has('query')) {
            $query = $request->input('query');
            $roles = $this->roleRepository->searchRoles($request, $query);
        } else {
            $roles = $this->roleRepository->getAllRoles($request);
        }
        return RoleResource::collection($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50'
        ]);
        $role = Role::create($request->only(['name']));
        $permissionIds = $request->input('permissions');
        $role->permissions()->sync($permissionIds);
        return new RoleResource($role);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return new RoleResource($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50'
        ]);
        $role = Role::findOrFail($id);
        $role->update($request->only(['name']));
        $permissionIds = $request->input('permissions');
        $role->permissions()->sync($permissionIds);

        return new RoleResource($role);
    }

    public function destroy($id)
    {
        return $this->roleRepository->deleteRole($id);
    }
}
