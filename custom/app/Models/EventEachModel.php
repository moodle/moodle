<?php
class EventEachModel extends BaseModel
{
    // イベント詳細概要をイベントごとに全件取得
    public function getEvents()
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_event WHERE visible = 1 ORDER BY timestart ASC");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                echo 'データの取得に失敗しました: ' . $e->getMessage();
            }
        } else {
            echo "データの取得に失敗しました";
        }

        return [];
    }

    // イベント詳細概要をイベントごとに単件取得
    public function getEventById($id = null)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_event WHERE id = ? AND visible = 1 ORDER BY timestart ASC");
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                echo 'データの取得に失敗しました: ' . $e->getMessage();
            }
        } else {
            echo "データの取得に失敗しました";
        }

        return [];
    }
}
