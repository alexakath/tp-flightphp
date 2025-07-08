<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*, c.nom AS client_nom, tp.nom AS type_pret_nom
            FROM pret p
            JOIN client c ON p.client_id = c.id
            JOIN types_pret tp ON p.type_pret_id = tp.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*, c.nom AS client_nom, tp.nom AS type_pret_nom
            FROM pret p
            JOIN client c ON p.client_id = c.id
            JOIN types_pret tp ON p.type_pret_id = tp.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO pret (client_id, type_pret_id, montant, duree, date_debut, statut)
            VALUES (?, ?, ?, ?, ?, 'en_attente')
        ");
        $stmt->execute([
            $data->client_id,
            $data->type_pret_id,
            $data->montant,
            $data->duree,
            $data->date_debut
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET statut = ? WHERE id = ?");
        $stmt->execute([$data->statut, $id]);
    }
}