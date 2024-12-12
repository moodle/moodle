<?php
class EventPaymentUserModel extends BaseModel
{
    // 各イベントごとの定員合計取得
    public function getCapacitySum($eventId)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT SUM(count) FROM mdl_event_payment_user WHERE event_id = ?");
                $stmt->execute([$eventId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return (int) $result['SUM(count)'];
            } catch (\PDOException $e) {
                echo 'データの取得に失敗しました: ' . $e->getMessage();
            }
        } else {
            echo "データの取得に失敗しました";
        }

        return [];
    }
}
