<?php
// controllers/UserController.php

require_once __DIR__ . "/../models/UserModel.php";

class UserController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function login() {
        if (isset($_SESSION['login'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }
        require "views/user/login.php";
    }


    public function verifier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->model->findByLoginPassword($login, $password);
        if ($user) {

            $_SESSION['login'] = $user['login'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['idUtilisateur'] = $user['idUtilisateur'];

            if ($user['role'] === 'admin') {
                header("Location: index.php?action=dashboardAdmin");
            } else {
                header("Location: index.php?action=dashboard");
            }
            exit;
        } else {
            $message = "Login ou mot de passe incorrect.";
            require "views/user/login.php";
        }
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', time() - 42000);
        }
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }

// au cas ou faire controle z 
    public function listerUsers() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php?action=login");
        exit;
    }

    // récupération de la recherche
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;

    // on passe le paramètre au model
    $users = $this->model->getAll($search);

    require "views/user/list.php";
}


    public function ajouterUser() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php?action=login");
        exit;
    }

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $motDePasse = trim($_POST['motDePasse'] ?? '');
        $role = $_POST['role'] ?? 'etudiant';

        if ($login === '' || $motDePasse === '') {
            $message = "❌ Le login et le mot de passe sont obligatoires.";
        } else {
            try {

                $motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);

                $data = [
                    'login' => $login,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'motDePasse' => $motDePasse,
                    'role' => $role
                ];

                $this->model->add($data);

                $_SESSION['message'] = "✅ Utilisateur ajouté avec succès !";
                header("Location: index.php?action=listerUsers");
                exit;

            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        }
    }

    require "views/user/form.php";
}


    public function modifierUser() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: index.php?action=listerUsers");
            exit;
        }

        $user = $this->model->getById($id);
        if (!$user) {
            header("Location: index.php?action=listerUsers");
            exit;
        }

        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $motDePasse = trim($_POST['motDePasse'] ?? ''); 
            $role = $_POST['role'] ?? 'etudiant';

            if ($login === '') {
                $message = "Login obligatoire.";
            } else {
                $data = [
                    'login' => $login,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'motDePasse' => $motDePasse,
                    'role' => $role
                ];
                $this->model->update($id, $data);
                header("Location: index.php?action=listerUsers");
                exit;
            }
        }

        require "views/user/form.php";
    }

    public function supprimerUser() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header("Location: index.php?action=listerUsers");
        exit;
    }

    // ce que j'ai rajouter concernant de crée un compte + mot de passe oublié 

    public function register() {
        require "views/user/register.php";
        }

    public function registerUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (empty($login) || empty($_POST['password'])) {
            $error = "Tous les champs sont obligatoires.";
            require "views/user/register.php";
            return;
        }

        $existingUser = $this->model->getByLogin($login);
        if ($existingUser) {
            $error = "Ce login existe déjà.";
            require "views/user/register.php";
            return;
        }

        $this->model->createUser($login, $password, "etudiant");

        header("Location: index.php?action=login&success=1");
        exit();
        }
    }

    public function importCSV() {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {

        $file = fopen($_FILES['csv']['tmp_name'], "r");
        $added = 0;

        while (($line = fgetcsv($file, 1000, ";")) !== FALSE) {

            list($login, $nom, $prenom, $password) = $line;

            if (!$this->model->userExists($login)) {
                $this->model->createUser($login, $password, $nom, $prenom, "etudiant");
                $added++;
            }
        }

        fclose($file);

        $_SESSION['message'] = "$added étudiants importés.";
        header("Location: index.php?action=listerUsers");
        exit;
    }

    require "views/user/importCSV.php";
}


}
