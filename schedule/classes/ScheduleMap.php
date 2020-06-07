<?php


class ScheduleMap extends BaseMap
{
	public function existsScheduleByLessonPlanId($idPlan){
        $res = $this->db->query("SELECT schedule_id FROM schedule
        WHERE lesson_plan_id = $idPlan");
        if ($res->fetchColumn() > 0) {
        return true;
        }
             return false;
        }

    public function findDayById($id=null){
        if ($id) {
            $res = $this->db->query("SELECT name FROM day WHERE day_id = $id");
            return $res->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }
}
?>