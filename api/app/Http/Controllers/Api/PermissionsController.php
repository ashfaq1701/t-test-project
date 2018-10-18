<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Permission;
use App\Http\Resources\Permission as PermissionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;

class PermissionsController extends Controller
{
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->middleware(['permission:manage_permissions']);
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        if ($request->has('query')) {
            $query = $request->input('query');
            $permissions = $this->permissionRepository->searchPermissions($query);
        } else {
            $permissions = Permission::all();
        }
        return PermissionResource::collection($permissions);
    }
}
