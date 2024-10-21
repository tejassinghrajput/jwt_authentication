<?php
namespace App\Controllers\Api\v1;

class Constants {
    const ADMIN_EMAIL = 'sadmin@shipglobal.in';
    const DEFAULT_PASSWORD = '12345';
    const ERROR_UNAUTHORIZED_ACCESS = 'Unauthorized access.';
    const ERROR_DELETE_ADMIN_CREDENTIAL = 'Unable to delete admin credential.';
    const ERROR_USER_NOT_FOUND = 'User does not exist. Please choose another.';
    const ERROR_USERNAME_EXISTS = 'Username already exists. Please choose another.';
    const ERROR_EMAIL_EXISTS = 'Email already exists. Please choose another.';
    const SUCCESS_USER_ADDED = 'User added successfully';
    const SUCCESS_USER_UPDATED = 'User updated successfully';
    const ERROR_USER_NOT_UPDATED = 'User not updated';
    const ERROR_USER_NOT_DELETED = 'User not deleted';
    const ERR_INVALID_USERNAME = 'Username must be 3-20 characters long and alphanumeric.';
    const ERR_INVALID_EMAIL = 'Invalid email format.';
    const ERR_INVALID_NAME = 'Name must be 3-50 characters long and contain only letters.';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const ERROR_INVALID_REQUEST = 'Invalid request. Please check your input.';
    const ERROR_SERVER_ERROR = 'An unexpected error occurred. Please try again later.';
    const ERROR_INVALID_TOKEN = 'Invalid token provided.';
    const ERROR_ACCESS_DENIED = 'Access denied.';
}
