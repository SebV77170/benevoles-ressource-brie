<?php
namespace Calendar;

class Event {

    private $id;
    private $name;
    private $description;
    private $start;
    private $end;

    // 🔧 propriétés manquantes (hydratées depuis la requête SQL)
    private $id_in_day;     // int|null
    private $cat_creneau;   // string|int|null
    private $public;        // 0/1|null

    public function getId(): int { return (int)$this->id; }
    public function getName(): string { return (string)$this->name; }
    public function getDescription(): string { return $this->description ?? ''; }
    public function getStart(): \DateTime { return new \DateTime($this->start); }
    public function getEnd(): \DateTime { return new \DateTime($this->end); }

    // Getters optionnels pour les nouvelles colonnes
    public function getIdInDay(): ?int { return isset($this->id_in_day) ? (int)$this->id_in_day : null; }
    public function getCatCreneau() { return $this->cat_creneau ?? null; }
    public function isPublic(): bool { return !empty($this->public); }

    public function setName(string $name){ $this->name = $name; }
    public function setDescription(string $description){ $this->description = $description; }
    public function setStart(string $start){ $this->start = $start; }
    public function setEnd(string $end){ $this->end = $end; }
}
