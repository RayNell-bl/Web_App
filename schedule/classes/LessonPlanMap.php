<?php


class LessonPlanMap extends BaseMap
{
	public function existsPlan(LessonPlan $plan){
       $res = $this->db->query("SELECT lesson_plan_id FROM lesson_plan "
           . "WHERE group_id = $plan->group_id AND
subject_id = $plan->subject_id AND user_id = $plan->user_id");
       if ($res->fetchColumn() > 0) {
           return true;
       }
       return false;
   }


    public function save(LessonPlan $plan){
        if ($this->db->exec("INSERT INTO lesson_plan(group_id,subject_id, user_id)"
                . " VALUES($plan->group_id,$plan->subject_id,$plan->user_id)") == 1) {
            return true;
        }
        return false;
    }


    public function findByTeacherId($id=null){
        if ($id) {
            $res = $this->db->query("SELECT
lesson_plan.lesson_plan_id, groups.name AS gruppa,
subject.name AS subject, "
                . "subject.hours FROM lesson_plan
INNER JOIN groups ON
lesson_plan.group_id=groups.group_id "
                . "INNER JOIN subject ON
lesson_plan.subject_id=subject.subject_id WHERE
lesson_plan.user_id=$id");
            return $res->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    public function  findTeachers($ofset=0, $limit=30){
        $res = $this->db->query("SELECT user.user_id,CONCAT(user.lastname,' ', user.firstname, ' ',
user.patronymic) AS fio,"
            . " otdel.name AS otdel,
COUNT(lesson_plan.subject_id) AS
count_plan,SUM(subject.hours) AS sum_hours "
            . "FROM user INNER JOIN teacher ON
user.user_id=teacher.user_id INNER JOIN otdel "
            . "ON teacher.otdel_id=otdel.otdel_id
LEFT OUTER JOIN lesson_plan ON
teacher.user_id=lesson_plan.user_id "
            . "LEFT OUTER JOIN subject ON
lesson_plan.subject_id=subject.subject_id GROUP BY
user.user_id "
            . "LIMIT $ofset, $limit");
        return $res->fetchAll(PDO::FETCH_OBJ);
    }



    public function delete($id){
        if ($this->db->exec("DELETE FROM lesson_plan WHERE lesson_plan_id=$id") == 1) {
            return true;
        }
        return false;
    }

    public function arrPlanByTeacherId($id=null){
        if ($id) {
            $res = $this->db->query("SELECT
            lesson_plan.lesson_plan_id AS id, CONCAT(groups.name,' -> ',subject.name) AS value"
            . " FROM lesson_plan INNER JOIN
            groups ON lesson_plan.group_id=groups.group_id "
            . "INNER JOIN subject ON
            lesson_plan.subject_id=subject.subject_id "
            . "WHERE lesson_plan.user_id=$id
            ORDER BY groups.name, subject.name");
            return $res->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];  
    }

    public function findById($id=null){
        if ($id) {
            $res = $this->db->query("SELECT lesson_plan_id,
            group_id, subject_id, user_id FROM lesson_plan WHERE
            lesson_plan_id=$id");
            return $res->fetchObject('LessonPlan');
            }
            return false;
    }
}
?>