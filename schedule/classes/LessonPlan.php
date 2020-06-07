<?php


class LessonPlan extends Table
{
    public $lesson_plan_id=0;
    public $group_id=0;
    public $subject_id=0;
    public $user_id=0;
    public function validate()
    {
        try {
            if (!empty($this->group_id) && !empty($this->subject_id) && !empty($this->user_id)) {
                return true;
            } 
            else {
                throw new Exception('Не переданны все параметры');
            }
        } 
        catch (Exception $ex) {
            return false;
        }
    }
}