<?php
use \Firebase\JWT\JWT;

if(!function_exists('generate_jwt')){
    function generate_jwt($data) {
        $key=getenv('JWT_SECRET');
        $issuedAt=time();
        $expirationTime=$issuedAt + 3600;
        $payload =[
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $data
        ];
        return JWT::encode($payload,$key,'HS256');
    }
}
if(!function_exists('validate_jwt')){
    function validate_jwt($token){
        $key=getenv('JWT_SECRET');
        try{
            $decoded=JWT::decode($token,new \Firebase\JWT\Key($key,'HS256'));
            return (array) $decoded->data;
        }
        catch (Exception $e) {
            return null;
        }
    }
}