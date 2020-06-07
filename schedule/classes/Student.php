<?php


class Student extends Table
{
    public $user_id=0;
    public $group_id=0;
    public $num_zach='';
    public function validate()
    {
        if (!empty($this->group_id) && !empty($this->num_zach)) {
            return true;
        }
        return false;
    }
}