<?php
require_once "config/Db.php";

class UserModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* ------------------------------------------
        LOGIN
    ------------------------------------------ */
    public function findByLoginPassword(string $login, string $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE login = :login LIMIT 1");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;

        // Si ancien mot de passe non hashe (pour compatibilite)
        if ($user['motDePasse'] === $password) return $user;

        // Verifie le hash
        if (password_verify($password, $user['motDePasse'])) return $user;

        return false;
    }

    /* ------------------------------------------
        LISTE UTILISATEURS + RECHERCHE
    ------------------------------------------ */
    public function getAll($search = null) {
        if ($search) {
            $sql = "
                SELECT * FROM utilisateur 
                WHERE login LIKE :s OR nom LIKE :s OR prenom LIKE :s OR role LIKE :s
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['s' => "%$search%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->pdo->query("SELECT * FROM utilisateur")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE idUtilisateur = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ------------------------------------------
        AJOUT UTILISATEUR
    ------------------------------------------ */
    public function add(array $data): int {

        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur (login, nom, prenom, motDePasse, role)
            VALUES (:login, :nom, :prenom, :motDePasse, :role)
        ");

        $stmt->execute([
            'login' => $data['login'],
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'motDePasse' => password_hash($data['motDePasse'], PASSWORD_DEFAULT),
            'role' => $data['role'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /* ------------------------------------------
        MODIFICATION UTILISATEUR
    ------------------------------------------ */
    public function update(int $id, array $data): bool {

        if (!empty($data['motDePasse'])) {
            $sql = "UPDATE utilisateur 
                    SET login=:login, nom=:nom, prenom=:prenom, motDePasse=:mdp, role=:role 
                    WHERE idUtilisateur=:id";

            $params = [
                'login' => $data['login'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'mdp' => password_hash($data['motDePasse'], PASSWORD_DEFAULT),
                'role' => $data['role'],
                'id' => $id
            ];
        } else {
            $sql = "UPDATE utilisateur 
                    SET login=:login, nom=:nom, prenom=:prenom, role=:role 
                    WHERE idUtilisateur=:id";

            $params = [
                'login' => $data['login'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'role' => $data['role'],
                'id' => $id
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /* ------------------------------------------
        SUPPRESSION
    ------------------------------------------ */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE idUtilisateur = :id");
        return $stmt->execute(['id' => $id]);
    }

    /* ------------------------------------------
        CREATION COMPTE (inscription)
    ------------------------------------------ */
    public function createUser($login, $password, $nom, $prenom, $role="etudiant") {

        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur (login, nom, prenom, motDePasse, role)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $login,
            $nom,
            $prenom,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        ]);
    }

    /* ------------------------------------------
        MOT DE PASSE OUBLIE
    ------------------------------------------ */
    public function getByLogin($login) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userExists($login) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetchColumn() > 0;
    }

}
