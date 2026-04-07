<?php
namespace App;

class Admins extends Users {

    public function __construct(array $data, \PDO $pdo){
        parent::__construct($data, $pdo); // hydrate via Users
    }

    public function getAllUsers(): array {
        $sql = 'SELECT * FROM users';
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOneUser(string $uuid): array {
        $sql = 'SELECT * FROM users
                INNER JOIN date_users ON users.uuid_user = date_users.id_user
                WHERE users.uuid_user = :uuid';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':uuid' => $uuid]);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllUsersAndDateWaiting(): array {
    $sql = 'SELECT u.uuid_user, u.nom, u.prenom, u.pseudo, u.admin,
                   d.date_inscription, d.date_derniere_visite
            FROM users u
            LEFT JOIN date_users d ON d.id_user = u.uuid_user
            WHERE u.admin = 0';
    $sth = $this->pdo->query($sql);
    return $sth->fetchAll(\PDO::FETCH_ASSOC);
}

    public function getAllUsersAndDate(): array {
        $sql = 'SELECT * FROM users
                INNER JOIN date_users ON users.uuid_user = date_users.id_user';
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateHabilitation(string $uuid, int $habilitation): void {
        $sql = 'UPDATE users SET admin = :admin WHERE uuid_user = :uuid';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':admin' => $habilitation, ':uuid' => $uuid]);
    }

    public function deleteUser(string $uuid): void {
        $this->pdo->beginTransaction();

        try {
            $deleteConges = $this->pdo->prepare('DELETE FROM conges WHERE uuid_user = :uuid');
            $deleteConges->execute([':uuid' => $uuid]);

            $deleteDateUser = $this->pdo->prepare('DELETE FROM date_users WHERE id_user = :uuid');
            $deleteDateUser->execute([':uuid' => $uuid]);

            $deleteInscriptions = $this->pdo->prepare('DELETE FROM inscription_creneau WHERE id_user = :uuid');
            $deleteInscriptions->execute([':uuid' => $uuid]);

            $deleteUser = $this->pdo->prepare('DELETE FROM users WHERE uuid_user = :uuid');
            $deleteUser->execute([':uuid' => $uuid]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
