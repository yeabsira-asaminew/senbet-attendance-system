<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {
    public function get_years() {
        $this->db->select('DISTINCT(YEAR(registration_date)) AS year');
        $this->db->order_by('year', 'DESC');
        $query = $this->db->get('student');
        return $query->result_array();
    }

    public function get_sections() {
        $this->db->select('id, name');
        $this->db->order_by('id', 'ASC');  // Order by section ID for ascending order
        $query = $this->db->get('section');
        return $query->result_array();
    }

    public function get_student_count_by_section($year) {
        $sections = $this->get_sections();
        $result = [];

        foreach ($sections as $section) {
            $query = $this->db->query("
                SELECT s.name,
                    SUM(CASE WHEN st.sex = 'Male' AND st.status = 1 THEN 1 ELSE 0 END) AS male_count,
                    SUM(CASE WHEN st.sex = 'Female' AND st.status = 1 THEN 1 ELSE 0 END) AS female_count
                FROM student st
                LEFT JOIN section s ON st.section_id = s.id
                WHERE s.id = ? AND YEAR(st.registration_date) = ?
                GROUP BY s.id
            ", array($section['id'], $year));

            $row = $query->row_array();

            $result[] = [
                'section' => $section['name'],
                'male_count' => $row['male_count'] ?? 0,
                'female_count' => $row['female_count'] ?? 0,
                'total_count' => ($row['male_count'] ?? 0) + ($row['female_count'] ?? 0)
            ];
        }

        return $result;
    }

    public function get_monthly_count_by_year($startYear, $endYear) {
        $result = [];
    
        for ($month = 1; $month <= 12; $month++) {
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM student
                WHERE (YEAR(registration_date) = ? OR YEAR(registration_date) = ?)
                  AND MONTH(registration_date) = ?
            ", array($startYear, $endYear, $month));
    
            $row = $query->row_array();
            
            // Convert Gregorian month to Ethiopian month using Andegna
            $gregorianDate = new DateTime("$startYear-$month-1");
            $ethiopianDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);
            $ethiopianMonth = $ethiopianDate->format('F');
    
            $result[] = [
                'month' => $ethiopianMonth,
                'total' => $row['total'] ?? 0
            ];
        }
    
        return $result;
    }

    // Get active and inactive students based on status
    public function get_active_inactive_student_data() {
        $this->db->select('status, sex, 
                           SUM(CASE WHEN sex = "Male" THEN 1 ELSE 0 END) AS male_count, 
                           SUM(CASE WHEN sex = "Female" THEN 1 ELSE 0 END) AS female_count');
        $this->db->from('student');
        $this->db->group_by('status, sex');
        $query = $this->db->get();
        return $query->result_array();
    }


    // Get total student registrations by year for the chart
    public function get_yearly_registration_data() {
        $this->db->select('YEAR(registration_date) AS year, COUNT(*) AS total');
        $this->db->from('student');
        $this->db->group_by('YEAR(registration_date)');
        $query = $this->db->get();

        $result = [];
        foreach ($query->result_array() as $row) {

            $ethiopianYear =  \Andegna\DateTimeFactory::of($row['year'], 1, 1)->format('Y');
            $result[$ethiopianYear] = $row['total'];
        }

        return $result;
    }
    
    public function get_sections_data()
    {
        // Fetch section data with total male, female, and total students (status = 1)
        return $this->db->select('s.name AS section_name, 
                                 COUNT(CASE WHEN st.sex = "Male" THEN 1 END) as male, 
                                 COUNT(CASE WHEN st.sex = "Female" THEN 1 END) as female, 
                                 COUNT(*) as total')
            ->from('student st')
            ->join('section s', 's.id = st.section_id', 'left')
            ->where('st.status', 1) // Only active students
            ->group_by('s.name')
            ->get()
            ->result_array();
    }

    public function get_departments_data()
    {
        // Fetch department data with total male, female, and total students (status = 1)
        return $this->db->select('d.name AS department_name, 
                                 COUNT(CASE WHEN st.sex = "Male" THEN 1 END) as male, 
                                 COUNT(CASE WHEN st.sex = "Female" THEN 1 END) as female, 
                                 COUNT(*) as total')
            ->from('student st')
            ->join('department d', 'd.id = st.department', 'left')
            ->where('st.status', 1) // Only active students
            ->group_by('d.name')
            ->get()
            ->result_array();
    }

    public function get_age_groups_data()
    {
        return $this->db->select("
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 7 THEN 1 ELSE 0 END) as under_7,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 7 AND 18 THEN 1 ELSE 0 END) as between_7_18,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) > 18 THEN 1 ELSE 0 END) as above_18
        ")
            ->from('student ')
            ->where('student.status', 1) // Only active students
            ->get()
            ->row_array();
    }

    public function get_students_by_year()
    {
        // Fetch students by admission year for active students (status = 1)
        return $this->db->select('YEAR(st.registration_date) as year, COUNT(*) as total')
            ->from('student st')
            ->where('st.status', 1) // Only active students
            ->group_by('YEAR(st.registration_date)')
            ->get()
            ->result_array();
    }

    public function get_students_by_sex()
    {
        // Fetch students by sex for active students (status = 1)
        return $this->db->select('st.sex, COUNT(*) as total')
            ->from('student st')
            ->where('st.status', 1) // Only active students
            ->group_by('st.sex')
            ->get()
            ->result_array();
    }

}
