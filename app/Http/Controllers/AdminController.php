<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Admin;
use App\Therapists;
use App\User;
use App\Post;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


      protected $guard = 'admin';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.admin');
    }


    public function view_posts(){
      $posts = Post::paginate(6);
      return view('Admin.blog.view_posts',compact('posts'));
    }
    public function create_posts(){

      return view('Admin.blog.create');
    }

    public function check_therapists(){
      $therapists = Therapists::paginate(6);
        return view('admin.therapists',compact('therapists'));
    }

    public function check_admins(){
      $admins = Admin::paginate(6);
        return view('admin.admin-users',compact('admins'));
    }
    public function check_normalusers(){
      $users = User::paginate(6);
        return view('admin.normal-users',compact('users'));
    }

    public function create_users(){
      return view('admin.create_users');
    }

    public function create_therapist_users(){
      return view('admin.create_therapists');
    }

    public function store_therapist_info(Request $request){
      $this->validate($request, [
          'email' => 'required',
          'password' => 'required',
          'name' => 'required',
          'image' => 'image|nullable|max:10000'

      ]);

      //Handle file upload
      if($request->hasFile('image')){
          //Get filename with exetension
          $filenameWithExt = $request->file('image')->getClientOriginalName();
          //Get just file name
          $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
          //Get just ext
          $extension = $request->file('image')->getClientOriginalExtension();
          //Filename to store
          $fileNameToStore = $filename.'_'.time().'.'.$extension;
          //Upload image
          $path = $request->file('image')->storeAs('public/therapists/images', $fileNameToStore);

      }
      else{
          $fileNameToStore = 'noimage.jpg';
      }
      $em = $request->input('email');
      // create post
      $therapist = new Therapists;

      $therapist->name = $request->input('name');
      $therapist->email = $request->input('email');
      $therapist->image = $fileNameToStore;
      $therapist->password = Hash::make($request['password']);
      $therapist->save();


      // $th = Therapists::where('email',$em)->select('id')->first();
      //
      // // $th1 = (string)$th;
      // $th2 = "$th";
      return redirect('/admin/check_therapists')->with('success', 'Therapist User Created');
      // return $th;
    }

    public function UserRole()
    {
        return view('admin.create_users');
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */

}
