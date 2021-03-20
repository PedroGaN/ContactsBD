<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $response = [];

        $data = $request->getContent();

        $data = json_decode($data);

        if($data){

            $checkUser = User::where('email',$data->email)->first();

            if($checkUser){
                $response[] = [
                    "status" => "email"
                ];

                return response($response);
            }

            $user = new User();

            $user->name = $data->name;
            $user->email = $data->email;
            $user->password = Hash::make($data->password);
            $user->api_token = self::api_token();

            if($data->last_name){
                $user->last_name = $data->last_name;
            }

            if($data->phone_number){
                $user->phone_number = $data->phone_number;
            }

            if($data->user_image){
                $user->user_image = $data->user_image;
            }
            
            try{

                $user->save();
                $new_user = User::find($user->id);
                $new_user->contacts_info = json_decode($new_user->contacts_info);

                Log::info($new_user);
                
                $response[] = [
                    "user" => $new_user,
                    "status" => "OK"
                ];

            }catch(\Exception $e){
                $response = $e->getMessage();
            }
            
        }else{
            $response = "missing_parameters";
        }

        return response($response);
    }

    public function loginUser(Request $request) 
    {

        $response = [];

        $data = $request->getContent();

        $data = json_decode($data);

        if($data){

            $user = User::where('email', $data->email)->first();
            if($user){

                if(Hash::check($data->password,$user->password)){
                    
                    $user->api_token = self::api_token();
                    
                    try{
                        $user->save();

                        $user->contacts_info = json_decode($user->contacts_info);

                        $response[] = [
                            "user" => $user,
                            "status" => "OK"
                        ];

                    }catch(\Exception $e){
                        $response = $e->getMessage();
                    }

                }else{
                    $response[] = [
                        "api_key" => $user->api_token,
                        "status" => "password"
                    ];
                }
            }else{
                $response[] = [
                    "status" => "user"
                ];
            }

        }else{
            $response = "Incorrect Data";
        }

        return response($response);

    }

    public function logoutUser(Request $request) 
    {

        $response = [];

        $data = $request->getContent();

        $data = json_decode($data);

        if($data){

            $user = User::find($data->id);
            if($user){

                $user->api_token = NULL;
                
                try{
                    $user->save();

                    $response = "OK";

                }catch(\Exception $e){
                    $response = $e->getMessage();
                }
            }else{
                $response = "error";
            }

        }else{
            $response = "data_error";
        }

        return response($response);

    }

    public function updateUser(Request $request)
    {
        $response = [];

        $data = $request->getContent();

        $data = json_decode($data);	

		if($data){

            $user = User::find($data->id);

			if($user){

                if(Hash::check($data->password,$user->password)){

                    if(isset($data->name))
                        $user->name = $data->name;
                    if(isset($data->last_name))
                        $user->last_name = $data->last_name;
                    if(isset($data->email))
                        $user->email = $data->email;
                    if(isset($data->new_password) && $data->new_password != "")
                        $user->password = Hash::make($data->password);
                    
                    try{

                        $user->save();

                        $user->contacts_info = json_decode($user->contacts_info);

                        $response[] = [
                            "user" => $user,
                            "status" => "OK"
                        ];

                   }catch(\Exception $e){
                        $response = $e->getMessage();
                    }
                }else{
                    $response[] = [
                        "status" => "auth"
                    ];
                }
			}else{
				$response = "Incorrect Data";
			}
		}else{
            $response[] = [
                "status" => "user"
            ];
		}

		return response($response);
    }

    public function addContacts(Request $request)
    {
        $response = "";

        $data = $request->getContent();

        $data = json_decode($data);

        if($data){

		    $user = User::find($data->id);

            if($user){
                if(isset($data->contacts_info))
                $user->contacts_info = $data->contacts_info;

                try{

                    $user->save();

                    $response = $user;

               }catch(\Exception $e){
                    $response = $e->getMessage();
                }
            }else{
                $response[] = [
                    "status" => "user"
                ];
            }
        }else{
            $response[] = [
                "status" => "user"
            ];
        }
        return response($response);
    }

    public function deleteUser(Request $request)
    {

        $response = [];


        $data = $request->getContent();

        $data = json_decode($data);

        if($data){

            $user = User::find($data->id);

			if($user){

                if(Hash::check($data->password,$user->password)){
                    
                    try{

                        $user->delete();

                        $response = "OK";

                   }catch(\Exception $e){
                        $response = $e->getMessage();
                    }
                }else{
                    $response = "auth";
                }
			}else{
				$response = "Incorrect Data";
			}
		}else{
            $response = "user";

		}
        return response($response);
    }

    public function fetchUsers()
    {

        $response = "";

        //$users = User::all()->toArray();
        $users = User::all();
        foreach($users as $user){

            $user["contacts_info"] = json_decode($user["contacts_info"]);
        }
        $response = $users;

        return response($response);
    }

    public function api_token(){
        $api_token = '';

        $characters = array_merge(range('A','Z'), range(10,30));
    
        for($i = 1; $i <= 50; $i++){
            $api_token .= $characters[rand(0,(sizeof($characters)-1))];
        }

        return $api_token;
    }
}
