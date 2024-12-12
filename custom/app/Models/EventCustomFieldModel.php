<?php
class EventCustomFieldModel extends BaseModel
{
    // 各イベントごとのカスタムフィールドを取得
    public function getEventsCustomFieldByEventId($eventId = null)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_event_customfield WHERE event_id = ? AND is_delete = 0 ORDER BY sort ASC");
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
