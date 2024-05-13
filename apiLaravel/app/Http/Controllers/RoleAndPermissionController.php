<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
    public function index(){
        $roles=Role::all();
        return $this->jsonResponse($roles, 'Success');
    }

    public function create(){
        
    }
}
