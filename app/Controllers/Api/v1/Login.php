<?php
namespace App\Controllers\Api\v1;
use App\Models\UserModel;
use CodeIgniter\Controller;
class Login extends Controller {
    public function __construct(){
        helper('jwt_helper');
    }
    public function index() {
        // function to validate login
        $json = $this->request->getJSON();
        $password = $json->password ?? null;
        $email=$json->email ?? null;
        // checking if the details entered by user or admin is null
        if($email == null || $password == null){
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Email']);
        }
        $usermodel = new UserModel();
        $user = $usermodel->getUserByEmail($email);
        if ($user) {
            // Login mechanism here.
            $username=$user['username'];
            if (password_verify($password, $user['password'])) {
                // if credentials recieved are of admin.
                if($email=='sadmin@shipglobal.in'){
                    $x=['email'=>$email];
                    $token=generate_jwt($x);
                    return $this->response->setJSON([
                        'login_status' => 'true',
                        'info' => $username . ' has logged in successfully.',
                        'token' => $token
                    ]);
                }
                else{
                    // if credentials recieved are of user.
                    $x=['email'=>$email];
                    $token=generate_jwt($x);
                    return $this->response->setJSON([
                        'login_status' => 'true',
                        'info' => $username . ' has logged in successfully.',
                        'token' => $token
                    ]);
            }
            } else {
                // if the password is wrong.
                return $this->response->setJSON([
                    'error' => 'Invalid password.'
                ]);
            }
        } else {
            // If user does not exists.
            return $this->response->setJSON([
                'error' => 'User does not exist.'
            ]);
        }
    }
    public function checkuser(){
        $response=[];
        $decodedData=$this->validateJWT();
        if(!$decodedData){
            return $this->response->setJSON(['status'=>'false','info'=>'Invalid token']); //if token is not validated, it will return false
        }
        $email=isset($decodedData['email']) ? $decodedData['email'] : null;
        return $this->response->setJSON(['status'=>'true','info'=>'User has logged in.']); // if token is validated then it will return true.
    }
    private function validateJWT()
    {
        // Validating the JWT token here
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return null;
        }
        $token = explode(' ', $authHeader)[1] ?? null;
        if (!$token) {
            return null;
        }
        //If token gets validated, it will return the decoded data
        $decodedData = validate_jwt($token);
        return $decodedData;
    }
}
