<?php
namespace App\Controllers\Api\v1;
use App\Models\UserModel;
use CodeIgniter\Controller;
class Admin extends Controller
{
    public function __construct()
    {
        helper('jwt_helper');
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
    public function checkadmin(){
        $response=[];
        $decodedData=$this->validateJWT();
        if(!$decodedData){
            return false; //if token is not validated, it will return false
        }
        $email=isset($decodedData['email']) ? $decodedData['email'] : null;
        if($email!='sadmin@shipglobal.in' && !empty($email)){ // checking if the current user is admin or not by checkig its email id which is encoded in JWT token
            return false;
        }
        return true; // if token is validated then it will return true.
    }
    public function index() // Admin dashboard
    {
        if(!$this->checkadmin()){
            return $this->response->setJSON(['status' => 'failed', 'info' => 'you are not admin.']);
        }
        $data['login_status'] = 'true';
        $data['message'] = 'Admin has logged in successfully.';
        return $this->response->setJSON($data);
    }
    public function adminupdate($id) // Update user through admin by using the user's ID
    {
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => 'Unauthorized access.']);
        }
        $userModel = new UserModel();
        $json = $this->request->getJSON();
        // fetching the updated data entered by admin
        $username = $json->username ?? null;
        $email = $json->email ?? null;
        $name = $json->name ?? null;
        $password = $json->password ?? null;
        $validationResult = $this->validatedetails($username, $email, $name); // validating the details entered by admin
        if ($validationResult['status'] === 'fail') {
            return $this->response->setJSON($validationResult);
        }
        $res = [];
        $existingUser = $userModel->getUserByUsername($username);
        $existingUser1 = $userModel->getUserByEmail($email);
        $currentuser=$userModel->getUserById($id);
        if(!$currentuser){
            //checking if user actually exists or not//
            $res['status'] = 'fail';
            $res['info'] = 'User does not exists. Please choose another.';
            return $this->response->setJSON($res);
        }
        //Checking if username or email already associated with another user.
        if ($existingUser && $existingUser['id'] != $id) {
            $res['status'] = 'fail';
            $res['info'] = 'Username already exists. Please choose another.';
            return $this->response->setJSON($res);
        }
        if ($existingUser1 && $existingUser1['id'] != $id) {
            $res['status'] = 'fail';
            $res['info'] = 'Email already exists. Please choose another.';
            return $this->response->setJSON($res);
        }
        // Updating user.
        $password=password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'username' => $username,
            'name'     => $name,
            'email'    => $email,
            'password' => $password
        ];
        $update = $userModel->updateUser($id, $data);
        if ($update) {
            $res['status'] = 'success';
            $res['info'] = 'User updated successfully';
        } else {
            $res['status'] = 'fail';
            $res['info'] = 'User not updated';
        }
        return $this->response->setJSON($res);
    }
    public function viewadmin()
    {
        // Used to display all users.
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => 'Unauthorized access.']);
        }
        $userModel = new UserModel();
        $userModel = new UserModel();
        $userModel = new UserModel();
        $check = $userModel->getAllUsers();
        foreach ($check as $key => $user) {
            // to prevent from fetching admin details at view dashboard
            if ($user['username'] === 'admin') {
                unset($check[$key]);
            } else {
                // to add extra value to Id of the user to improve security
                $randomText = bin2hex(random_bytes(5));
                $check[$key]['id'] = $user['id'] . '_' . $randomText;
            }
        }
        return $this->response->setJSON(array_values($check));
    }
    public function admindelete($id)
    // used to delete user through admin
    {
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => 'Unauthorized access.']);
        }
        if($id=='46'){
            return $this->response->setJSON(['status' => 'fail', 'info' => 'Unable to delete admin credential.']);
        }
        $userModel = new UserModel();
        $check = $userModel->removeUser($id);
        $res = [];
        if ($check) {
            $res['status'] = 'success';
            $res['info'] = 'User deleted successfully';
        } else {
            $res['status'] = 'fail';
            $res['info'] = 'User not deleted';
        }
        return $this->response->setJSON($res);
    }
    public function adminadd(){
        // Used to add user through admin.
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => 'Unauthorized access.']); 
        }
        $userModel = new UserModel();
        $json = $this->request->getJSON();
        $username = $json->username ?? null;
        $email = $json->email ?? null;
        $name = $json->name ?? null;
        $password = '12345'; // providing a default password for every new user when admin adds a new user.
        // validating te details
        $validationResult = $this->validatedetails($username, $email, $name);
        if ($validationResult['status'] === 'fail') {
            return $this->response->setJSON($validationResult);
        }
        $res = [];
        // checking if email or username already exists
        $existingUser = $userModel->getUserByUsername($username);
        $existingUser1 = $userModel->getUserByEmail($email);
        if ($existingUser) {
            $res['status'] = 'fail';
            $res['info'] = 'Username already exists. Please choose another.';
            return $this->response->setJSON($res);
        }
        if ($existingUser1) {
            $res['status'] = 'fail';
            $res['info'] = 'Email already exists. Please choose another.';
            return $this->response->setJSON($res);
        }
        // encrypting the password
        $password=password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'username' => $username,
            'name'     => $name,
            'email'    => $email,
            'password' => $password
        ];
        // adding the user into database
        $update = $userModel->saveUser($data);
        if ($update) {
            $res['status'] = 'success';
            $res['info'] = 'User added successfully';
        } else {
            $res['status'] = 'fail';
            $res['info'] = 'User not added';
        }
        return $this->response->setJSON($res);
    }
    public function validatedetails($username, $email, $name)
    {
        // Function for validation of details entered by  admin.
        $errors = [];
        if (empty($username) || strlen($username) < 3 || strlen($username) > 20 || !ctype_alnum($username)) {
            $errors['username'] = 'Username must be 3-20 characters long and alphanumeric.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }
        if (empty($name) || strlen($name) < 3 || strlen($name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $errors['name'] = 'Name must be 3-50 characters long and contain only letters.';
        }
        if (!empty($errors)) {
            return ['status' => 'fail', 'errors' => $errors];
        }
        // returing the status of validation if successfull
        return ['status' => 'success'];
    }
}