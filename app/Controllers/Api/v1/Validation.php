<?php
namespace App\Controllers\Api\v1;
use CodeIgniter\Controller;
use App\Controllers\Api\v1\Constants;

class Validation extends Controller{
    public static function validateDetails(string $username, string $email, string $name){
        $errors = [];
        // Validate username
        if (empty($username) || strlen($username) < 3 || strlen($username) > 20 || !ctype_alnum($username)) {
            $errors['username'] = Constants::ERR_INVALID_USERNAME;
        }
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = Constants::ERR_INVALID_EMAIL;
        }
        // Validate name
        if (empty($name) || strlen($name) < 3 || strlen($name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $errors['name'] = Constants::ERR_INVALID_NAME;
        }
        return !empty($errors) ? ['status' => Constants::STATUS_FAIL, 'errors' => $errors] : ['status' => Constants::STATUS_SUCCESS];
    }
}
