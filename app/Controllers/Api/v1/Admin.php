<?php
namespace App\Controllers\Api\v1;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Controllers\Api\v1\Validation; 
use App\Controllers\Api\v1\Constants;
class Admin extends Controller{
    public function __construct() {
        helper('jwt_helper');
    }
    private function validateJWT()
    {
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return null;
        }
        $token = explode(' ', $authHeader)[1] ?? null;
        if (!$token) {
            return null;
        }
        return validate_jwt($token);
    }
    public function checkadmin() {
        $decodedData = $this->validateJWT();
        if (!$decodedData) {
            return false;
        }
        $email = $decodedData['email'] ?? null;
        // Check if the user's email matches the admin email
        if ($email !== Constants::ADMIN_EMAIL && !empty($email)) {
            return false;
        }
        return true;
    }
    public function index(){
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'failed', 'info' => Constants::ERROR_UNAUTHORIZED_ACCESS]);
        }
        return $this->response->setJSON(['login_status' => 'true', 'message' => 'Admin has logged in successfully.']);
    }
    public function adminupdate($id){
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_UNAUTHORIZED_ACCESS]);
        }
        $userModel = new UserModel();
        $json = $this->request->getJSON();
        $username = $json->username ?? null;
        $email = $json->email ?? null;
        $name = $json->name ?? null;
        $password = $json->password ?? null;
        $validationResult = $this->validatedetails($username, $email, $name);
        if ($validationResult['status'] === 'fail') {
            return $this->response->setJSON($validationResult);
        }
        $res = [];
        $existingUser = $userModel->getUserByUsername($username);
        $existingUser1 = $userModel->getUserByEmail($email);
        $currentuser = $userModel->getUserById($id);
        if (!$currentuser) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_USER_NOT_FOUND]);
        }
        if ($existingUser && $existingUser['id'] != $id) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_USERNAME_EXISTS]);
        }
        if ($existingUser1 && $existingUser1['id'] != $id) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_EMAIL_EXISTS]);
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];
        $update = $userModel->updateUser($id, $data);
        if ($update) {
            $res['status'] = 'success';
            $res['info'] = Constants::SUCCESS_USER_UPDATED;
        } else {
            $res['status'] = 'fail';
            $res['info'] = Constants::ERROR_USER_NOT_UPDATED;
        }
        return $this->response->setJSON($res);
    }
    public function viewadmin(){
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_UNAUTHORIZED_ACCESS]);
        }
        $userModel = new UserModel();
        $check = $userModel->getAllUsers();
        foreach ($check as $key => $user) {
            if ($user['username'] === 'admin') {
                unset($check[$key]);
            } else {
                $randomText = bin2hex(random_bytes(5));
                $check[$key]['id'] = $user['id'] . '_' . $randomText;
            }
        }
        return $this->response->setJSON(array_values($check));
    }
    public function admindelete($id){
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_UNAUTHORIZED_ACCESS]);
        }
        if ($id == '46') {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_DELETE_ADMIN_CREDENTIAL]);
        }
        $userModel = new UserModel();
        $check = $userModel->removeUser($id);
        $res = [];
        if ($check) {
            $res['status'] = 'success';
            $res['info'] = 'User deleted successfully';
        } else {
            $res['status'] = 'fail';
            $res['info'] = Constants::ERROR_USER_NOT_DELETED;
        }
        return $this->response->setJSON($res);
    }
    public function adminadd()
    {
        if (!$this->checkadmin()) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_UNAUTHORIZED_ACCESS]);
        }
        $userModel = new UserModel();
        $json = $this->request->getJSON();
        $username = $json->username ?? null;
        $email = $json->email ?? null;
        $name = $json->name ?? null;
        $password = Constants::DEFAULT_PASSWORD;
        $validationResult = Validation::validateDetails($username, $email, $name);
        if ($validationResult['status'] === 'fail') {
            return $this->response->setJSON($validationResult);
        }
        $res = [];
        $existingUser = $userModel->getUserByUsername($username);
        $existingUser1 = $userModel->getUserByEmail($email);
        if ($existingUser) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_USERNAME_EXISTS]);
        }
        if ($existingUser1) {
            return $this->response->setJSON(['status' => 'fail', 'info' => Constants::ERROR_EMAIL_EXISTS]);
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];
        $update = $userModel->saveUser($data);
        if ($update) {
            $res['status'] = 'success';
            $res['info'] = Constants::SUCCESS_USER_ADDED;
        } else {
            $res['status'] = 'fail';
            $res['info'] = 'User not added';
        }
        return $this->response->setJSON($res);
    }
    public function validatedetails($username, $email, $name)
    {
        $errors = [];
        // Validate username
        if (empty($username) || strlen($username) < 3 || strlen($username) > 20 || !ctype_alnum($username)) {
            $errors['username'] = 'Username must be 3-20 characters long and alphanumeric.';
        }
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }
        // Validate name
        if (empty($name) || strlen($name) < 3 || strlen($name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $errors['name'] = 'Name must be 3-50 characters long and contain only letters.';
        }
        if (!empty($errors)) {
            return ['status' => 'fail', 'errors' => $errors];
        }
        return ['status' => 'success'];
    }
}
