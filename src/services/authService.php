<?php

session_start();
include 'database/db.php';

class Auth
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    public function register($firstName, $lastName, $email, $password, $address, $city, $houseNumber, $zipCode, $country)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO user (first_name, last_name, email_address, password, address, city, house_number, zip_code, country, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            die("Error preparing statement: " . $this->pdo->error);
        }

        $executeResult = $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $address, $city, $houseNumber, $zipCode, $country, 0]);

        if (!$executeResult) {
            die("Error executing statement: " . $stmt->error);
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE email_address = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user === false) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id_user'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email_address'],
                'is_admin' => $user['is_admin'],
            ];
            return true;
        }

        return false;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public function getLoggedInUserData() {
        $userId = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id_user = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function getLoggedInUserName()
    {
        return $this->isLoggedIn() ? $_SESSION['user']['name'] : null;
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: /login');
    }
}