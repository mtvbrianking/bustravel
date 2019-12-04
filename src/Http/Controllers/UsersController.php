<?php
namespace glorifiedking\BusTravel\Http\Controllers;

use Illuminate\Routing\Controller;
use glorifiedking\BusTravel\User;
use glorifiedking\BusTravel\Operator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use File;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
        $this->middleware('auth');
    }
   //fetching permissions
    public function permissions()
    {
        $permissions =Permission::all();
        return view('bustravel::backend.users.permissions.index',compact('permissions'));
    }
    // save Permission
    public function storepermissions(Request $request)
    {
      //validation
      //saving to the database
      $permission = new Permission;
      $permission->name = request()->input('name');
      $permission->guard_name = request()->input('guard_name')??'web';
      $permission->save();
      $alerts = [
        'bustravel-flash' => true,
        'bustravel-flash-type' => 'success',
        'bustravel-flash-title' => 'Permission Saving',
        'bustravel-flash-message' => 'Permission '.$permission->name.' has successfully been saved'
    ];
      return redirect()->route('bustravel.users.permissions')->with($alerts);
    }
    //Upadte Permission
    public function updatepermissions($id, Request $request)
    {
      //saving to the database
      $permission = Permission::find(request()->input('id'));
      $permission->name = request()->input('name');
      $permission->guard_name = request()->input('guard_name')??'web';
      $permission->save();
      $alerts = [
        'bustravel-flash' => true,
        'bustravel-flash-type' => 'success',
        'bustravel-flash-title' => 'Permission Updating',
        'bustravel-flash-message' => 'Permission '.$permission->name.' has successfully been Updated'
    ];
      return redirect()->route('bustravel.users.permissions')->with($alerts);
    }

    //Delete Permission
    public function deletepermissions($id)
    {
        $permission=Permission::find($id);
        $name =$permission->name;
        $permission->delete();
        $alerts = [
            'bustravel-flash' => true,
            'bustravel-flash-type' => 'error',
            'bustravel-flash-title' => 'Permission Deleted',
            'bustravel-flash-message' => "Permission '.$name.' has successfully been deleted"
        ];
        return Redirect::route('bustravel.users.permissions')->with($alerts);
    }

    //fetching Roles
     public function roles()
     {
         $roles =Role::all();
         return view('bustravel::backend.users.roles.index',compact('roles'));
     }

     public function createroles()
    {
      $permissions = Permission::all();//Get all permissions
      return view('bustravel::backend.users.roles.create',compact('permissions') );
    }
     // save Role
     public function storeroles(Request $request)
     {
       //validation
       //saving to the database
       $role = new Role;
       $role->name = request()->input('name');
       $role->guard_name = request()->input('guard_name')??'web';
       $role->save();
       $role->syncPermissions();
       $permissions = $request->input('permissions')??0;
       if($permissions!=0)
       {
         foreach($permissions as $permission_id)
        {
        $permission_role=Permission::find($permission_id);
        $role->givePermissionTo($permission_role);
        }
       }

       $alerts = [
         'bustravel-flash' => true,
         'bustravel-flash-type' => 'success',
         'bustravel-flash-title' => 'Role Saving',
         'bustravel-flash-message' => 'Role '.$role->name.' has successfully been saved'
     ];
       return redirect()->route('bustravel.users.roles')->with($alerts);
     }
     public function editroles($id)
      {
        $role = Role::find($id);
        $permissions =Permission::all();
        if (is_null($role))
        {
         return Redirect::route('bustravel.users.roles');
        }
        return view('bustravel::backend.users.roles.edit', compact('role','permissions'));
      }
     //Upadte Role
     public function updateroles($id, Request $request)
     {
       //saving to the database
       $role=Role::find($id);
       $role->name = request()->input('name');
       $role->guard_name = request()->input('guard_name')??'web';
       $role->save();
       $role->syncPermissions();
       $permissions = $request->input('permissions')??0;
       if($permissions!=0)
       {
         foreach($permissions as $permission_id)
        {
        $permission_role=Permission::find($permission_id);
        $role->givePermissionTo($permission_role);
        }
       }
       $alerts = [
         'bustravel-flash' => true,
         'bustravel-flash-type' => 'success',
         'bustravel-flash-title' => 'Role Updating',
         'bustravel-flash-message' => 'Role '.$role->name.' has successfully been Updated'
     ];
       return redirect()->route('bustravel.users.roles.edit',$id)->with($alerts);
     }

     //Delete Role
     public function deleteroles($id)
     {
         $role=Role::find($id);
         $name =$role->name;
         $role->delete();
         $alerts = [
             'bustravel-flash' => true,
             'bustravel-flash-type' => 'error',
             'bustravel-flash-title' => 'Role Deleted',
             'bustravel-flash-message' => 'Role '.$name.' has successfully been deleted'
         ];
         return Redirect::route('bustravel.users.roles')->with($alerts);
     }

     //fetching Roles
      public function users()
      {
          $users =config('bustravel.user_model',User::class)::all();
          return view('bustravel::backend.users.users.index',compact('users'));
      }
      public function createusers()
     {
       $roles = Role::all();//Get all permissions
       $operators =Operator::where('status',1)->get();
       return view('bustravel::backend.users.users.create',compact('roles','operators') );
     }
      // save Role
      public function storeusers(Request $request)
      {
        //validation
        //saving to the database
        $validation = request()->validate([
          'name' => 'required|max:255|unique:users',
          'email' => 'required|email|max:255|unique:users',
          'password' => 'required|min:7|confirmed',
          'password_confirmation' => 'required|same:password'
        ]);
        $user_class = config('bustravel.user_model',User::class);
        $user = new $user_class;
        $user->name =request()->input('name');
        $user->email =request()->input('email');
        $user->password =bcrypt(request()->input('password'));
        $user->phone_number =request()->input('phone_number');
        $user->status =request()->input('status');
        $user->operator_id =request()->input('operator_id')??0;
        $user->save();
        $user->assignRole(request()->input('role'));

        $alerts = [
          'bustravel-flash' => true,
          'bustravel-flash-type' => 'success',
          'bustravel-flash-title' => 'User Saving',
          'bustravel-flash-message' => 'User '.$user->name.' has successfully been saved'
      ];
        return redirect()->route('bustravel.users')->with($alerts);
      }
      public function editusers($id)
       {
         $roles = Role::all();//Get all permissions
         $operators =Operator::where('status',1)->get();
         $user = config('bustravel.user_model',User::class)::find($id);
         if (is_null($user))
         {
          return Redirect::route('bustravel.users');
         }
         return view('bustravel::backend.users.users.edit', compact('roles','operators','user'));
       }
      //Upadte Role
      public function updateusers($id, Request $request)
      {
        //saving to the database
        if(request()->input('newpassword')=="")
        {
          $validation = request()->validate([
            'name' => 'required|max:255|unique:users,name,'.$id,
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            //'password' => 'required|min:7|confirmed',
            //'password_confirmation' => 'required|same:password'
          ]);
          $user =  config('bustravel.user_model',User::class)::find($id);
          $user->name =request()->input('name');
          $user->email =request()->input('email');
          $user->phone_number =request()->input('phone_number');
          $user->status =request()->input('status');
          $user->operator_id =request()->input('operator_id')??0;
          $user->save();
          $user->syncRoles(request()->input('role'));
        }else {
          $validation = request()->validate([
            'name' => 'required|max:255|unique:users,name,'.$id,
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'newpassword' => 'required|min:7|confirmed',
            'password_confirmation' => 'required|same:newpassword'
          ]);
          $user =  config('bustravel.user_model',User::class)::find($id);
          $user->name =request()->input('name');
          $user->email =request()->input('email');
          $user->password =bcrypt(request()->input('password'));
          $user->phone_number =request()->input('phone_number');
          $user->status =request()->input('status');
          $user->operator_id =request()->input('operator_id')??0;
          $user->save();
          $user->syncRoles(request()->input('role'));
        }

        $alerts = [
          'bustravel-flash' => true,
          'bustravel-flash-type' => 'success',
          'bustravel-flash-title' => 'User Updating',
          'bustravel-flash-message' => 'User '.$user->name.' has successfully been Updated'
      ];
        return redirect()->route('bustravel.users.edit',$id)->with($alerts);
      }

      //Delete Role
      public function deleteusers($id)
      {
          $user=config('bustravel.user_model',User::class)::find($id);
          $name =$user->name;
          $user->delete();
          $alerts = [
              'bustravel-flash' => true,
              'bustravel-flash-type' => 'error',
              'bustravel-flash-title' => 'User Deleted',
              'bustravel-flash-message' => 'User '.$name.' has successfully been deleted'
          ];
          return Redirect::route('bustravel.users')->with($alerts);
      }
}