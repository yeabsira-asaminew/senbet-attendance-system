<?php
defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use Andegna\DateTime as EthiopianDateTime;
use Andegna\DateTimeFactory;
use Andegna\Constants;

class Dashboard extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('uid')) {
            redirect('Login');
        } 

        $this->load->model('Student_model', 'Student');
        $this->load->model('Attendance_model', 'attend');
    }

    public function index()
    {
        $user_id = $this->session->userdata('uid');
    
        // Fetch raw data from the model
        $gregorian_data = $this->Student->get_students_by_year();
    
        // Convert Gregorian years to Ethiopian years
        $students_registration = [];
        foreach ($gregorian_data as $row) {
            $gregorian_date = new DateTime($row->gregorian_year . '-01-01'); // Create a DateTime object for the year
            $ethiopian_date = new Andegna\DateTime($gregorian_date); // Convert to Ethiopian date
            $ethiopian_year = $ethiopian_date->format('Y'); // Get the Ethiopian year
    
            $students_registration[] = [
                'year' => $ethiopian_year, // Ethiopian year
                'count' => $row->count // Number of students
            ];
        }
    
        // Fetch other data
        $data['sex_data'] = $this->Student->get_students_by_sex();
        $data['section_data'] = $this->Student->get_students_by_section();
        $data['department_data'] = $this->Student->get_students_by_department();
        $data['age_data'] = $this->Student->get_students_by_age_group();
        $data['students_by_year'] = $students_registration; // Use the converted data
        $data['students_by_month'] = $this->Student->get_students_by_month();
        $data['attendance_data'] = $this->attend->get_attendance_analysis();
    
        // Load the view
        $this->load->view('admin/dashboard', $data);
    }
    
}

