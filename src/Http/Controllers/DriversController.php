<?php

namespace glorifiedking\BusTravel\Http\Controllers;

use File;
use glorifiedking\BusTravel\Driver;
use glorifiedking\BusTravel\Operator;
use glorifiedking\BusTravel\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use glorifiedking\BusTravel\ToastNotification;


class DriversController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
        $this->middleware('auth');
    }

    //fetching Drivers route('bustravel.drivers')
    public function index()
    {

      if(auth()->user()->hasAnyRole('BT Administrator'))
        {
          $drivers =Driver::where('operator_id',auth()->user()->operator_id)->get();
        }
      else
        {
           $drivers = Driver::all();
        }


        return view('bustravel::backend.drivers.index', compact('drivers'));
    }

    //creating Driver form route('bustravel.buses.create')
    public function create()
    {
        return view('bustravel::backend.drivers.create');
    }

    // saving a new Driver in the database  route('bustravel.drivers.store')
    public function store(Request $request)
    {
        //validation
        $validation = request()->validate(Driver::$rules);
        if ($request->hasFile('picture')) {
            $path = public_path('drivers');
            // creating logos folder if doesnot exit
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $name = Str::lower(request()->input('name'));
            $resultString = str_replace(' ', '', $name);
            $photoname = $resultString.'_'.time().'.'.request()->picture->getClientOriginalExtension();
            request()->picture->move(public_path('drivers'), $photoname);
        }
            $user_class = config('bustravel.user_model', User::class);
            $user = new $user_class();
            $user->name= request()->input('name');
            $user->email= request()->input('email');
            $user->password=bcrypt('password');
            $user->phone_number = request()->input('phone_number');
            $user->status=1;
            $user->operator_id = auth()->user()->operator_id;
            $user->save();
            $role= Role::where('name','BT Driver')->first();
            $user->syncRoles('BT Driver');
        //saving to the database
        $driver = new Driver();
        $driver->user_id = $user->id;
        $driver->name = request()->input('name');
        $driver->nin = strtoupper(request()->input('nin'));
        $driver->date_of_birth = request()->input('date_of_birth');
        $driver->driving_permit_no = strtoupper(request()->input('driving_permit_no'));
        $driver->picture = $photoname ?? null;
        $driver->phone_number = request()->input('phone_number');
        $driver->address = request()->input('address');
        $driver->status = request()->input('status');
        $driver->save();
        return redirect()->route('bustravel.drivers')->with(ToastNotification::toast('Driver '.$driver->name.' has successfully been saved','Driver Saving'));
    }

    //Bus Edit form route('bustravel.buses.edit')
    public function edit($id)
    {
        $bus_operators = Operator::where('status', 1)->orderBy('name', 'ASC')->get();
        $driver = Driver::find($id);
        $user = config('bustravel.user_model', User::class)::find($driver->user_id);
        if (is_null($driver)) {
            return Redirect::route('bustravel.drivers');
        }
        return view('bustravel::backend.drivers.edit', compact('bus_operators', 'driver','user'));
    }

    //Update Driver route('bustravel.drivers.upadate')
    public function update($id, Request $request)
    {
       $driver = Driver::find($id);
        //validation
        $validation = request()->validate([
        'email'             => 'required|unique:users,email,'.$driver->user_id,
        'name'              => 'required',
        'nin'               => 'required|unique:drivers,nin,'.$id,
        'date_of_birth'     => 'required',
        'driving_permit_no' => 'required|unique:drivers,driving_permit_no,'.$id,
        'phone_number'      => 'required',
        'address'           => 'required',
      ]);

        if ($request->hasFile('newpicture')) {
            $path = public_path('drivers');
            // creating logos folder if doesnot exit
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $name = Str::lower(request()->input('name'));
            $resultString = str_replace(' ', '', $name);
            $photoname = $resultString.'_'.time().'.'.request()->newpicture->getClientOriginalExtension();
            request()->newpicture->move(public_path('drivers'), $photoname);
        }
        //saving to the database
        $user = config('bustravel.user_model', User::class)::find($driver->user_id);
        $user_id=0;
        if(is_null($user))
        {
          $user_class = config('bustravel.user_model', User::class);
          $user1 = new $user_class();
          $user1->name= request()->input('name');
          $user1->email= request()->input('email');
          $user1->password=bcrypt('password');
          $user1->phone_number = request()->input('phone_number');
          $user1->status=1;
          $user1->operator_id = auth()->user()->operator_id;
          $user1->save();
          $role= Role::where('name','BT Driver')->first();
          $user1->syncRoles($role->id);
          $user_id = $user1->id;

        }else{
          $user->name= request()->input('name');
          $user->email= request()->input('email');
          $user->phone_number = request()->input('phone_number');
          $user->save();
          $role= Role::where('name','BT Driver')->first();
          $user->syncRoles($role->id);
          $user_id = $user->id;

        }
        $driver->user_id = $user_id;
        $driver->name = request()->input('name');
        $driver->nin = strtoupper(request()->input('nin'));
        $driver->date_of_birth = request()->input('date_of_birth');
        $driver->driving_permit_no = strtoupper(request()->input('driving_permit_no'));
        $driver->picture = $photoname ?? request()->input('picture');
        $driver->phone_number = request()->input('phone_number');
        $driver->address = request()->input('address');
        $driver->status = request()->input('status');
        $driver->save();
        return redirect()->route('bustravel.drivers.edit', $id)->with(ToastNotification::toast('Driver has successfully been updated','Driver Updating'));
    }

    //Delete Driver
    public function delete($id)
    {
        $driver = Driver::find($id);
        $name = $driver->name;
        $driver->delete();
        $alerts = [
            'bustravel-flash'         => true,
            'bustravel-flash-type'    => 'error',
            'bustravel-flash-title'   => 'Driver Deleting ',
            'bustravel-flash-message' => 'Driver '.$name.' has successfully been Deleted',
        ];

        return Redirect::route('bustravel.drivers')->with(ToastNotification::toast($name. ' has successfully been Deleted','Driver Deleting','error'));
    }
}
