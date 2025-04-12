<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic_model extends CI_Model {

    public function get_sections() {
        return $this->db->get('section')->result_array();
    }

    public function get_departments() {
        return $this->db->get('department')->result_array();
    }


    public function get_schedules() {
        $this->db->select('schedule.id, schedule.day, schedule.time, GROUP_CONCAT(section.name SEPARATOR ", ") as sections');
        $this->db->from('schedule');
        $this->db->join('section_schedule', 'schedule.id = section_schedule.schedule_id', 'left');
        $this->db->join('section', 'section_schedule.section_id = section.id', 'left');
        $this->db->group_by('schedule.id');
        return $this->db->get()->result_array();
    }

    public function add_schedule($data, $sections) {
        if ($this->db->insert('schedule', $data)) {
            $schedule_id = $this->db->insert_id();
    
            foreach ($sections as $section_id) {
                $this->db->insert('section_schedule', ['schedule_id' => $schedule_id, 'section_id' => $section_id]);
            }
            return true; 
        }
        return false; 
    }

    public function update_schedule($id, $data, $section) {
        // Update the schedule
        if ($this->db->where('id', $id)->update('schedule', $data)) {
            $this->db->where('schedule_id', $id)->delete('section_schedule');
    
            foreach ($section as $section_id) {
                $this->db->insert('section_schedule', ['schedule_id' => $id, 'section_id' => $section_id]);
            }
    
            return true; 
        }
    
        return false; 
    }
    
    public function delete_schedule($id) {
        // First, delete section assignments
        $this->db->where('schedule_id', $id)->delete('section_schedule');
    
        return $this->db->where('id', $id)->delete('schedule'); 
    }

    // sections and departments
    public function add_department($data) {
        return $this->db->insert('department', $data) ? $this->db->insert_id() : false;
    }

    public function add_section($data) {
        return $this->db->insert('section', $data) ? $this->db->insert_id() : false;
    }

    public function delete_section($id) {
        // Remove section associations from section_schedule table
        $this->db->where('section_id', $id)->delete('section_schedule');
        // Set section_id to NULL for students in the student table
        $this->db->where('section_id', $id)->set('section_id', NULL)->update('student');
        return $this->db->where('id', $id)->delete('section');
    }

    public function delete_department($id) {
        // Set department to NULL for students in the student table
        $this->db->where('department', $id)->set('department', NULL)->update('student');

        return $this->db->where('id', $id)->delete('department');
    }

    public function update_section($id, $data) {
        
        if ($this->db->where('id', $id)->update('section', $data)) {
    
            return true; 
        }
    
        return false; 
    }

    public function update_department($id, $data) {
        
        if ($this->db->where('id', $id)->update('department', $data)) {
    
            return true; 
        }
    
        return false; 
    }
}


