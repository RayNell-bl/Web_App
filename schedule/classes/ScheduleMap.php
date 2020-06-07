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
    public function arrLessonNums(){
        $res = $this->db->query("SELECT lesson_num_id AS id, name AS value FROM lesson_num");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsScheduleTeacherAndGruppa(Schedule $schedule){
        $plan = (new LessonPlanMap())->findById($schedule->lesson_plan_id);
        $res = $this->db->query("SELECT schedule.schedule_id FROM
        lesson_plan INNER JOIN schedule "
        . "ON
        lesson_plan.lesson_plan_id=schedule.lesson_plan_id "
        . "WHERE (lesson_plan.group_id=$plan->group_id OR lesson_plan.user_id=$plan->user_id) AND "
        . "(schedule.day_id=$schedule->day_id AND
        schedule.lesson_num_id=$schedule->lesson_num_id)");
        if ($res->fetchColumn() > 0) {
            return true;
        }
    return false;
    }

    public function save(Schedule $schedule){
        if ($this->db->exec("INSERT INTO schedule(lesson_plan_id,
        day_id, lesson_num_id, classroom_id)"
        . " VALUES($schedule->lesson_plan_id,$schedule->day_id, $schedule->lesson_num_id, $schedule->classroom_id)") == 1) {
        return true;
    }
    return false;
    }

    public function findByTeacherId($id=null){
        $days = $this->findDays();
        $result = [];
        foreach ($days as $day) {
        $arrDay = [];
        $arrDay['id'] = $day->day_id;
        $arrDay['name'] = $day->name;
        $arrDay['gruppa'] = [];
        $gruppas = $this->findGruppasByDayTeacher($id, $day->day_id);
        foreach ($gruppas as $gruppa) {
            $arrGruppa = [];
            $arrGruppa['name'] = $gruppa->name;
            $arrGruppa['schedule'] = $this->findByGruppasDayTeacher($id, $day->day_id,$gruppa->group_id);
            $arrDay['gruppa'][] = $arrGruppa;
        }
        $result[] = $arrDay;
        }
        return $result;
    }

    public function findDays(){
        $res = $this->db->query("SELECT day_id, name FROM day");
        return $res->fetchAll(PDO::FETCH_OBJ);
    }

    public function findGruppasByDayTeacher($teacherId, $dayId){
        $res = $this->db->query("SELECT DISTINCT
        groups.group_id, groups.name FROM lesson_plan "
        . "INNER JOIN schedule ON
        lesson_plan.lesson_plan_id=schedule.lesson_plan_id "
        . "INNER JOIN groups ON
        lesson_plan.group_id=groups.group_id "
        . "WHERE lesson_plan.user_id=$teacherId
        AND schedule.day_id=$dayId ORDER BY groups.name");
        return $res->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByGruppasDayTeacher($teacherId, $dayId, $gruppaId){
        $res = $this->db->query("SELECT
            schedule.schedule_id,lesson_num.name AS
            lesson_num,subject.name AS subject,classroom.name AS
            classroom FROM lesson_plan "
            . "INNER JOIN schedule ON
            lesson_plan.lesson_plan_id=schedule.lesson_plan_id "
            . "INNER JOIN subject ON
            lesson_plan.subject_id=subject.subject_id INNER JOIN "
            . "lesson_num ON
            schedule.lesson_num_id=lesson_num.lesson_num_id INNER
            JOIN "
            . "classroom ON
            schedule.classroom_id=classroom.classroom_id WHERE "
            . "lesson_plan.user_id=$teacherId AND
            schedule.day_id=$dayId AND
            lesson_plan.group_id=$gruppaId"
            . " ORDER BY schedule.lesson_num_id");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id){
        if ($this->db->exec("DELETE FROM schedule WHERE schedule_id=$id") == 1) {
            return true;
        }
    return false;
    }



    public function findByStudentId($id=null){
        $days = $this->findDays();
        $gruppa = (new GruppaMap())->findById((new StudentMap())->findById($id)->group_id);
        $result=[ 'gruppaName'=>$gruppa->name,];

        foreach ($days as $day){
            $arrDay=[];
            $arrDay['id']= $day->day_id;
            $arrDay['name']= $day->name;
            $arrDay['schedule']=$this->findByGruppaId($gruppa->group_id, $day->day_id);
            $result['schedules'] [] = $arrDay;
        }
        return $result;
        
    }

    public function findByGruppaId($gruppaId, $dayId){
        $res = $this->db->query("SELECT
            schedule.schedule_id,lesson_num.name AS lesson_num,subject.name AS subject,classroom.name AS classroom, Concat(user.lastname,' ', user.firstname,' ',IfNull(user.patronymic, ' ')) FROM lesson_plan "
            . "INNER JOIN schedule ON lesson_plan.lesson_plan_id=schedule.lesson_plan_id "
            ."INNER JOIN teacher ON lesson_plan.user_id=teacher.user_id "
            ."INNER JOIN user ON teacher.user_id =user.user_id "
            . "INNER JOIN subject ON lesson_plan.subject_id=subject.subject_id INNER JOIN "
            . "lesson_num ON schedule.lesson_num_id=lesson_num.lesson_num_id INNER JOIN "
            . "classroom ON schedule.classroom_id=classroom.classroom_id WHERE "
            . "lesson_plan.group_id=$gruppaId AND schedule.day_id=$dayId"
            . " ORDER BY schedule.lesson_num_id"); 
            return $res->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>