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
        $sql = 'SELECT * FROM users
                INNER JOIN date_users ON users.uuid_user = date_users.id_user
                WHERE users.admin = 0';
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
}
