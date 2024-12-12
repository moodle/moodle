<?php
class EventVideosModel extends BaseModel
{
    // IDから講義動画を取得
    public function getEventVideos($eventId = null)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_event_videos WHERE event_id = ?");
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
