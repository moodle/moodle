<?php
class SurveyCustomFieldModel extends BaseModel
{
    // 各イベントごとのアンケートカスタムフィールドを取得
    public function getSurveyCustomFieldByEventId($eventId = null)
    {
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM mdl_survey_customfield WHERE event_id = ? AND is_delete = 0 ORDER BY sort ASC");
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
    // カスタムフィールド登録
    public function insertEventCustomField($eventId, $fieldName, $name, $sort, $fieldType, $fieldOptions) {}
}
