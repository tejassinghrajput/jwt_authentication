<?php
namespace App\Controllers\Api\v1;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Controllers\Api\v1\Validation;
use App\Controllers\Api\v1\Constants;

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
        $validationResult = Validation::validateDetails($username, $email, $name);
        if ($validationResult['status'] === Constants::STATUS_FAIL) {
            return $this->response->setJSON($validationResult);
        }
        // checking if email or username entered are already associated with another user
        $checkemail = $userModel->getUserByEmail($email);
        $checkuser = $userModel->getUserByUsername($username);
        if ($checkemail || $checkuser) {
            $data['status'] = Constants::STATUS_FAIL;
            $data['message'] = 'User already exists.';
            return $this->response->setJSON($data);
        }
        $inserting = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'name' => $name
        ];
        // Inserting the details into Database
        $query = $userModel->saveUser($inserting);
        if ($query) {
            $data['status'] = Constants::STATUS_SUCCESS;
            $data['message'] = 'User inserted successfully.';
        } else {
            $data['status'] = Constants::STATUS_FAIL;
            $data['message'] = 'Unable to add user.';
        }
        return $this->response->setJSON($data);
    }
}
