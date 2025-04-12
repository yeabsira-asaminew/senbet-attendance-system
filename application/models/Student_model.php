<?php
class Student_model extends CI_Model {

    public function add($data) {
        return $this->db->insert('student', $data); // Returns true on success, false on failure
    }
    
    public function get_all_students()
{
    $this->db->select("
        student.*,
        CASE 
            WHEN student.sex = 'Male' THEN 'ወንድ'
            WHEN student.sex = 'Female' THEN 'ሴት'
            ELSE student.sex
        END AS sex_amharic
    ");
    $this->db->from('student');
    // $this->db->where('student.status', '1');
    $query = $this->db->get();
    return $query->result_array();
}

public function get_all_students_with_section_and_department_names()
{
    // Use $this->db->select() with raw SQL for the CASE statements
    $this->db->select('
        student.*,
        section.name AS section_name, 
        department.name AS department_name,
        CASE 
            WHEN student.status = 1 THEN "ንቁ"
            WHEN student.status = 0 THEN "ንቁ ያልሆነ"
        END as status_text,
        CASE 
            WHEN student.sex = "Male" THEN "ወንድ"
            WHEN student.sex = "Female" THEN "ሴት"
        END AS sex_amharic
    ', false); // The `false` parameter prevents CodeIgniter from escaping the CASE statements

    $this->db->from('student');
    $this->db->join('section', 'section.id = student.section_id', 'left');
    $this->db->join('department', 'department.id = student.department', 'left');
    $query = $this->db->get();
    return $query->result_array();
}
    // only active=1 students are fetched
    public function get_students($search = '', $sort_by = 'id', $sort_order = 'asc', $per_page = 10, $page = 0) {
        // Use $this->db->select() with raw SQL for the CASE statement
        $this->db->select('
            student.id,
            student.fname, 
            student.mname, 
            student.lname, 
            CASE 
                WHEN student.sex = "Male" THEN "ወንድ"
                WHEN student.sex = "Female" THEN "ሴት"
            END AS sex_amharic,
            section.name AS section_name, 
            department.name AS department_name
        ', false); // The `false` parameter prevents CodeIgniter from escaping the CASE statement
    
        $this->db->from('student');
        $this->db->join('section', 'section.id = student.section_id', 'left');
        $this->db->join('department', 'department.id = student.department', 'left');
        $this->db->where('student.status', '1');
    
        if ($search) {
            $this->db->group_start(); // Ensure proper grouping of OR conditions
            $this->db->like('student.fname', $search);
            $this->db->or_like('student.mname', $search);
            $this->db->or_like('student.lname', $search);
            //$this->db->or_like('student.sex_amharic', $search);
            $this->db->or_like('section.name', $search);
            $this->db->or_like('department.name', $search);
            $this->db->group_end();
        }
    
        $this->db->order_by($sort_by, $sort_order);
        $this->db->limit($per_page, $page);
    
        return $this->db->get()->result_array();
    }
    

    public function get_students_count($search = '') {
        $this->db->from('student');
        $this->db->join('section', 'section.id = student.section_id', 'left');
        $this->db->join('department', 'department.id = student.department', 'left');
        $this->db->where('student.status', '1');
    
        if ($search) {
            $this->db->group_start(); // Ensure correct OR logic
            $this->db->like('student.fname', $search);
            $this->db->or_like('student.mname', $search);
            $this->db->or_like('student.lname', $search);
            // $this->db->or_like('student.sex', $search);
            $this->db->or_like('section.name', $search);
            $this->db->or_like('department.name', $search);
            $this->db->group_end();
        }
    
        return $this->db->count_all_results();
    }
    

     // only inactive=0 students are fetched
     public function get_inactive_students($search = '', $sort_by = 'id', $sort_order = 'asc', $per_page = 10, $page = 0) {
        // Use $this->db->select() with raw SQL for the CASE statement
        $this->db->select('
            student.id,
            student.fname, 
            student.mname, 
            student.lname, 
            CASE 
                WHEN student.sex = "Male" THEN "ወንድ"
                WHEN student.sex = "Female" THEN "ሴት"
            END AS sex_amharic,
            section.name AS section_name, 
            department.name AS department_name
        ', false); // The `false` parameter prevents CodeIgniter from escaping the CASE statement
    
        $this->db->from('student');
        $this->db->join('section', 'section.id = student.section_id', 'left');
        $this->db->join('department', 'department.id = student.department', 'left');
        $this->db->where('student.status', '0');
    
        if ($search) {
            $this->db->group_start(); // Ensure proper grouping of OR conditions
            $this->db->like('student.fname', $search);
            $this->db->or_like('student.mname', $search);
            $this->db->or_like('student.lname', $search);
            //$this->db->or_like('student.sex_amharic', $search);
            $this->db->or_like('section.name', $search);
            $this->db->or_like('department.name', $search);
            $this->db->group_end();
        }
    
        $this->db->order_by($sort_by, $sort_order);
        $this->db->limit($per_page, $page);
    
        return $this->db->get()->result_array();
    }
    

     public function get_inactive_students_count($search = '') {
        $this->db->from('student');
        $this->db->join('section', 'section.id = student.section_id', 'left');
        $this->db->join('department', 'department.id = student.department', 'left');
        $this->db->where('student.status', '0');
    
        if ($search) {
            $this->db->group_start(); // Ensure correct OR logic
            $this->db->like('student.fname', $search);
            $this->db->or_like('student.mname', $search);
            $this->db->or_like('student.lname', $search);
         //   $this->db->or_like('student.sex', $search);
            $this->db->or_like('section.name', $search);
            $this->db->or_like('department.name', $search);
            $this->db->group_end();
        }
    
        return $this->db->count_all_results();
    }
    

    public function update_student($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('student', $data);
    }
    

    public function deactivate_student($id, $active) {
    $this->db->set([
        'status' => $active,
    ])
    ->where('id', $id)
    ->update('student');

    return $this->db->affected_rows() > 0;
   }

public function activate_student($id, $active){
    $this->db->set('status', $active)
    ->where('id', $id)
    ->update('student');

    return $this->db->affected_rows() > 0;
}

public function get_student_by_id($id) {
    $this->db->select('
     student.*, 
        section.name AS section_name, 
        department.name AS department_name, 
        schedule.day AS schedule_day,
        TIME_FORMAT(schedule.time, "%h:%i %p") AS schedule_time,
    CASE 
        WHEN student.sex = "Male" THEN "ወንድ"
        WHEN student.sex = "Female" THEN "ሴት"
    END AS sex_amharic
', false); // The `false` parameter prevents CodeIgniter from escaping the CASE statement

    $this->db->from('student');
    $this->db->join('section', 'section.id = student.section_id', 'left');
    $this->db->join('department', 'department.id = student.department', 'left');
    $this->db->join('section_schedule', 'section_schedule.section_id = section.id', 'left');
    $this->db->join('schedule', 'schedule.id = section_schedule.schedule_id', 'left');
    $this->db->where('student.id', $id);
    
    $query = $this->db->get();
    $student = $query->row_array();

    if ($student) {
        // Calculate age from DOB
        if (!empty($student['dob'])) {
            $dob = new DateTime($student['dob']);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            $student['age'] = $age;
        } else {
            $student['age'] = 'N/A';
        }

        // Day translations from English to Amharic
        $dayTranslations = [
            "Monday"    => "ሰኞ",
            "Tuesday"   => "ማክሰኞ",
            "Wednesday" => "ረቡዕ",
            "Thursday"  => "ሐሙስ",
            "Friday"    => "አርብ",
            "Saturday"  => "ቅዳሜ",
            "Sunday"    => "እሁድ"
        ];

        // Convert schedule day to Amharic
        if (isset($student['schedule_day'])) {
            $student['schedule_day'] = $dayTranslations[$student['schedule_day']] ?? $student['schedule_day'];
            $student['schedule_datetime'] = $student['schedule_day'] . " " . $student['schedule_time'];
        }
    }

    return $student;
}


public function delete_student($id) {
    // Delete attendances associated with the student
    $this->db->where('student_id', $id);
    $this->db->delete('attendance');
    
    // Delete the student record
    $this->db->where('id', $id);
    return $this->db->delete('student');
}


    public function update_qr_code($student_id, $qr_code) {
        $this->db->where('id', $student_id);
        return $this->db->update('student', ['qr_code' => $qr_code]);
    }
    public function get_student_by_id_row($student_id) {
        return $this->db->get_where('student', ['id' => $student_id])->row();
    }


    public function has_qr($id)
    {
        $this->db->where('id', $id);
        $this->db->where('qr_code IS NOT NULL'); 
        
        $query = $this->db->get('student');
        return $query->num_rows() > 0;
    }
    

// for dashboard charts
public function get_students_by_sex() {
    // Use $this->db->select() with raw SQL for the CASE statement
    $this->db->select('
        CASE 
            WHEN student.sex = "Male" THEN "ወንድ"
            WHEN student.sex = "Female" THEN "ሴት"
        END AS sex_amharic,
        COUNT(*) as count
    ', false); // The `false` parameter prevents CodeIgniter from escaping the CASE statement

    $this->db->group_by('sex_amharic'); // Group by the computed field
    return $this->db->get('student')->result();
}

    public function get_students_by_section() {
        $this->db->select('s.name as section_name, COUNT(*) as count');
        $this->db->from('student st');
        $this->db->join('section s', 'st.section_id = s.id');
        $this->db->group_by('st.section_id');
        return $this->db->get()->result();
    }

    public function get_students_by_department() {
        $this->db->select('d.name as department_name, COUNT(*) as count');
        $this->db->from('student st');
        $this->db->join('department d', 'st.department = d.id');
        $this->db->group_by('st.department');
        return $this->db->get()->result();
    }
/*
    public function get_students_by_status() {
        $this->db->select('status, COUNT(*) as count');
        $this->db->group_by('status');
        return $this->db->get('student')->result();
    }
*/
    public function get_students_by_age_group() {
        $this->db->select("
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 7 THEN 1 ELSE 0 END) as under_7,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 7 AND 18 THEN 1 ELSE 0 END) as between_7_18,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) > 18 THEN 1 ELSE 0 END) as above_18
        ");
        return $this->db->get('student')->row();
    }
  // Get number of students by year
  public function get_students_by_year() {
    // Fetch data from the database
    $this->db->select('YEAR(registration_date) as gregorian_year, COUNT(*) as count');
    $this->db->group_by('YEAR(registration_date)');
    return $this->db->get('student')->result();
}

// Get number of students by month for all years
public function get_students_by_month() {
    $this->db->select('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count');
    $this->db->group_by('YEAR(created_at), MONTH(created_at)');
    return $this->db->get('student')->result();
}

}