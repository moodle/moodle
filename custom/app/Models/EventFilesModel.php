<?php

class EventFilesModel extends BaseModel
{
    // IDから講義資料を取得
    public function getEventFiles($eventId = null)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_event_files WHERE event_id = ?");
                $stmt->execute([$eventId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                echo 'データの取得に失敗しました: ' . $e->getMessage();
            }
        } else {
            echo "データの取得に失敗しました";
        }

        return [];
    }
}
