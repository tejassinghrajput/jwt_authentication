<?php
namespace App\Controllers\Api\v1;
use App\Models\UserModel;
use CodeIgniter\Controller;
class Register extends Controller {
    public function signup() {
        // Function to register a user
        $userModel = new UserModel();
        $data = [];
        $json = $this->request->getJSON();
        // Checking if all the fields are entered
        $username = $json->username ?? null;
        $password = $json->password ?? null;
        $email = $json->email ?? null;
        $name = $json->name ?? null;
        // validating the fields
        $validationResult = $this->check($username, $email, $name);
        if ($validationResult['status'] === 'fail') {
            return $this->response->setJSON($validationResult);
        }
        // checking if email or username entered are already associated with another user.
        $checkemail = $userModel->getUserByEmail($email);
        $checkuser = $userModel->getUserByUsername($username);
        if ($checkemail || $checkuser) {
            $data['status'] = 'fail';
            $data['message'] = 'User already exists.';
            return $this->response->setJSON($data);
        }
        $inserting = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'name' => $name
        ];
        // Instering the details into Database.
        $query = $userModel->saveUser($inserting);
        if ($query) {
            $data['status'] = 'success';
            $data['message'] = 'User inserted successfully.';
        } else {
            $data['status'] = 'fail';
            $data['message'] = 'Unable to add user.';
        }
        return $this->response->setJSON($data);
    }
    public function check($username, $email, $name) {
        // function to validate the details.
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
        return ['status' => 'success'];
    }
}