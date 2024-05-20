<?php

 function getRolePermission($role){
    return $role->permissions->pluck('id')->toArray();
}

?>