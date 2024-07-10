<?php

namespace App\Controllers;

use App\Models\User;
use App\Traits\AuthenticationTrait;
use App\Traits\EncryptionTrait;

class UserController extends User
{

    use EncryptionTrait;
    use AuthenticationTrait;

    public function isAuth()
    {
        if ($this->isAuthenticated() != true) {
            header("Location: login.php");
        }
    }

    public function decryptPasswords(array $passwords, string $key): array
    {
        foreach ($passwords as $password) {
            $password['password_value'] = $this->decryptOpenSSL($password['password_value'], $key);
            $decrypted[] = $password;
        }

        return $decrypted;
    }

    public function allUsers(): array
    {
        return $this->all();
    }

    public function userGet(int $id)
    {
        return $this->fetchUser($id);
    }

    public function userCreate(array $data): int
    {

        $data['master_password'] = $this->hashPassword($data['master_password']);

        switch ($this->createUser($data)) {
            case 0:
                header("location: dashboard.php");
                exit();
                break;

            case 1:
                echo "<script>alert('username or email already exists!')</script>";
                $_SERVER = $_SERVER['PHP_SELF'];
                return 1;
                break;

            case 2:
                echo "<script>alert('Something went wrong creating your account, try again later')</script>";
                $_SERVER = $_SERVER['PHP_SELF'];
                return 2;
                break;
        }
    }

    public function userLogin(array $data): int
    {


        switch ($this->login($data)) {

            case 0:

                header("Location: dashboard.php");
                return 0;
                break;

            case 1:
                echo "<script>alert('Username not found!')</script>";
                return 1;
                break;

            case 2:
                echo "<script>alert('Incorrect password!')</script>";
                return 2;
                break;
        }
    }

    public function userUpdate(int $id, array $data): int
    {
        return $this->updateUser($id, $data);
    }

    public function userDelete(int $id): int
    {
        return $this->destroyUser($id);
    }
}
