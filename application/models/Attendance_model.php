<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_model extends CI_Model
{

    public function get_student($student_id)
    {
        return $this->db->get_where('student', ['id' => $student_id])->row();
    }

    public function get_schedules_for_day($section_id, $day)
    {
        $this->db->select('schedule.time');
        $this->db->from('section_schedule');
        $this->db->join('schedule', 'schedule.id = section_schedule.schedule_id');
        $this->db->where('section_schedule.section_id', $section_id);
        $this->db->where('schedule.day', $day);
        return $this->db->get()->result();
    }

    // Get all sections with schedules for the current day
    public function get_sections_with_schedules_for_day($current_day)
    {
        $this->db->distinct();
        $this->db->select('section_schedule.section_id, schedule.time');
        $this->db->from('schedule');
        $this->db->join('section_schedule', 'schedule.id = section_schedule.schedule_id');
        $this->db->where('schedule.day', $current_day);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_absent_students($section_id, $current_date)
    {
        $this->db->select('id');
        $this->db->from('student');
        $this->db->where('section_id', $section_id);
        $this->db->where('last_attendance_date !=', $current_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function record_attendance($student_id, $status, $date)
    {
        // Insert attendance record
        $data = [
            'student_id' => $student_id,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s') // Store date and time
        ];
        $this->db->insert('attendance', $data);

        // Update the last_attendance_date flag
        $this->db->where('id', $student_id);
        $this->db->update('student', ['last_attendance_date' => $date]);
    }

    // Get attendance summary (Total, Present, Absent)
    public function get_attendance_summary($student_id)
    {
        $this->db->select("COUNT(id) as total, 
                           SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present, 
                           SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent");
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('attendance');
        return $query->row_array();
    }

    // Get schedule-based attendance
    public function get_schedule_attendance($student_id)
    {
        // Day translations
        $dayTranslations = [
            "Monday"    => "ሰኞ",
            "Tuesday"   => "ማክሰኞ",
            "Wednesday" => "ረቡዕ",
            "Thursday"  => "ሐሙስ",
            "Friday"    => "አርብ",
            "Saturday"  => "ቅዳሜ",
            "Sunday"    => "እሁድ"
        ];

        // Fetch section_id for the student
        $this->db->select('section_id');
        $this->db->where('id', $student_id);
        $student = $this->db->get('student')->row_array();

        if (!$student) return [];

        // Fetch schedule days for the section
        $this->db->select('schedule.day');
        $this->db->from('schedule');
        $this->db->join('section_schedule', 'section_schedule.schedule_id = schedule.id');
        $this->db->where('section_schedule.section_id', $student['section_id']);
        $query = $this->db->get();
        $schedules = $query->result_array();

        // Map attendance to schedule days
        $attendanceData = [];
        foreach ($schedules as $schedule) {
            $day = $schedule['day'];

            // Get attendance records for this day
            $this->db->select("SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present, 
                               SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent");
            $this->db->where('student_id', $student_id);
            $this->db->where("DAYNAME(created_date)", $day);
            $dayQuery = $this->db->get('attendance')->row_array();

            // Translate day to Amharic
            $translatedDay = $dayTranslations[$day] ?? $day;

            // Store translated attendance data
            $attendanceData[$translatedDay] = [
                'present' => $dayQuery['present'] ?? 0,
                'absent' => $dayQuery['absent'] ?? 0
            ];
        }

        return $attendanceData;
    }

    public function get_attendance_analysis()
    {
        $this->db->select("
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN created_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND status = 'Present' THEN 1 ELSE 0 END) as present_last_month,
            SUM(CASE WHEN created_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND status = 'Absent' THEN 1 ELSE 0 END) as absent_last_month,
            SUM(CASE WHEN created_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND status = 'Present' THEN 1 ELSE 0 END) as present_last_week,
            SUM(CASE WHEN created_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND status = 'Absent' THEN 1 ELSE 0 END) as absent_last_week
        ");
        return $this->db->get('attendance')->row();
    }


    // fetching the attendance for listing
    public function get_attendances($date)
    {
        $this->db->select('
     a.*, st.*, s.name as section_name,
    CASE 
        WHEN a.status = "present" THEN "ተገኝቷል"
        WHEN a.status = "absent" THEN "ቀሪ"
    END AS status_text
', false);
        $this->db->from('attendance a');
        $this->db->join('student st', 'a.student_id = st.id');
        $this->db->join('section s', 'st.section_id = s.id');
        $this->db->where('a.created_date', $date);

        $query = $this->db->get();
        return $query->result_array();
    }


    public function delete($id)
    {
        $this->db->delete('attendance', ['id' => $id]);
    }
}
