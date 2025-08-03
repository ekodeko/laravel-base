<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::with(['roles:id,name', 'children'])->orderBy('order')->get();
    }

    public function store(Request $request)
    {
        $menu = Menu::create($request->only('title', 'icon', 'route', 'order', 'parent_id', 'permission_name'));
        if ($request->roles) {
            $menu->roles()->sync($request->roles);
        }
        return $menu->load('roles');
    }

    public function update(Request $request, Menu $menu)
    {
        $menu->update($request->only('title', 'icon', 'route', 'order', 'parent_id', 'permission_name'));
        if ($request->roles) {
            $menu->roles()->sync($request->roles);
        }
        return $menu->load('roles');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['message' => 'Menu deleted']);
    }

    public function rolesPermissions()
    {
        return [
            'roles' => Role::select('id', 'name')->get(),
            'permissions' => Permission::select('name')->get()
        ];
    }
}
