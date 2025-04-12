<?php
defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Andegna\DateTime as EthiopianDateTime;
use Andegna\DateTimeFactory;
use Andegna\Constants;

class Student extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('uid')) {
            redirect('login');
        }
        require_once(APPPATH . 'third_party/phpqrcode/qrlib.php');
        $this->load->model('Student_model', 'Student');
        $this->load->model('Academic_model');
        $this->load->model('Attendance_model');
    }

    public function add_student()
    {
        // Load sections and departments for the dropdowns
        $data['sections'] = $this->Academic_model->get_sections();
        $data['departments'] = $this->Academic_model->get_departments();

        $this->load->view('admin/add-student', $data);
    }

    public function add()
    {
        $data['sections'] = $this->Academic_model->get_sections();
        $data['departments'] = $this->Academic_model->get_departments();
        // Check if the form has been submitted
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('fname', 'First Name', 'required');
            $this->form_validation->set_rules('mname', 'Middle Name', 'required');
            $this->form_validation->set_rules('lname', 'Last Name', 'required');
            $this->form_validation->set_rules('christian_name', 'Christian Name', 'required');
            $this->form_validation->set_rules('repentance_father', 'Repentance Father', 'required');
            $this->form_validation->set_rules('God_father', 'God Father', 'required');
            $this->form_validation->set_rules('sex', 'Sex', 'required');
            $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
            $this->form_validation->set_rules('pob', 'Place of Birth', 'required');
            $this->form_validation->set_rules('phone1', 'Phone 1', 'required');
            $this->form_validation->set_rules('phone2', 'Phone 2');
            $this->form_validation->set_rules('occupation', 'Occupation', 'required');
            $this->form_validation->set_rules('section_id', 'Section ID', 'required');
            $this->form_validation->set_rules('department', 'Department', 'required');
            $this->form_validation->set_rules('registration_date', 'Registration Date', 'required');
            // Run validation
            if ($this->form_validation->run() === TRUE) {

                try {
                    // Convert Ethiopian dates to Gregorian before saving
                    $dob_eth = $this->input->post('dob');
                    $registration_date_eth = $this->input->post('registration_date');

                    $dob_greg = Andegna\DateTimeFactory::of(
                        (int)explode('/', $dob_eth)[2],
                        (int)explode('/', $dob_eth)[1],
                        (int)explode('/', $dob_eth)[0]
                    )->toGregorian()->format('Y-m-d');

                    $registration_date_greg = Andegna\DateTimeFactory::of(
                        (int)explode('/', $registration_date_eth)[2],
                        (int)explode('/', $registration_date_eth)[1],
                        (int)explode('/', $registration_date_eth)[0]
                    )->toGregorian()->format('Y-m-d');
                } catch (\Andegna\Exception\InvalidDateException $e) {
                    // Set flash data for invalid date error
                    $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'ያስገቡት ቀን ልክ አይደለም። እባክዎ ያስተካክሉ!']);
                    redirect('student/add_student');
                    return;
                }


                // Student photo
                $photo = null; // Set default as null

                if (!empty($_FILES['photo']['name'])) {
                    $this->load->library('upload');
                    $config['upload_path'] = 'uploads/photos/';
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['max_size'] = 10000; // 10MB max size
                    $config['file_name'] = 'Stud-' . date('YmdHms') . '-' . rand(1, 999999);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('photo')) {
                        $uploaded = $this->upload->data();
                        $photo = $uploaded['file_name']; // Save the uploaded file name
                    }
                }

                // Prepare data for insertion
                $data = [
                    'fname' => $this->input->post('fname'),
                    'mname' => $this->input->post('mname'),
                    'lname' => $this->input->post('lname'),
                    'christian_name' => $this->input->post('christian_name'),
                    'repentance_father' => $this->input->post('repentance_father'),
                    'God_father' => $this->input->post('God_father'),
                    'sex' => $this->input->post('sex'),
                    'dob' => $dob_greg,
                    'pob' => $this->input->post('pob'),
                    'phone1' => $this->input->post('phone1'),
                    'phone2' => $this->input->post('phone2'),
                    'occupation' => $this->input->post('occupation'),
                    'section_id' => $this->input->post('section_id'),
                    'department' => $this->input->post('department'),
                    'registration_date' => $registration_date_greg,
                    'photo' => $photo ?: 'default_photo.jpg',  // Use a default image if no file is uploaded
                ];

                // Insert data into the database
                if ($this->Student->add($data)) {
                    // Success message
                    $this->session->set_flashdata('student_message', ['type' => 'success', 'text' => 'ተማሪው በተሳካ ሁኔታ ተመዝግቧል!']);
                    redirect('student/list');
                } else {
                    // Error message
                    $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'ተማሪውን መመዝገብ አልተቻለም። እባክዎ በድጋሚ ይሞክሩ!']);
                    redirect('student/add_student');
                    return;
                }
            } else {
                // Validation failed
                $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'ፎርም ላይ የተሞሉ መረጃዎች ላይ ስህተት አለ ፣ እባክዎ አስተካክለው ይሞክሩ!']);
                redirect('student/add_student');
                return;
            }
        }

        // Load the view with validation errors and input data and sections and departments loaded from the db
        $data = array_merge($data, $this->input->post());
        $this->load->view('admin/add-student', $data);
    }


    public function list()
    {
        $search = $this->input->get('search');
        $sort_by = $this->input->get('sort_by') ?: 'id';
        $sort_order = $this->input->get('sort_order') ?: 'asc';
        $limit = $this->input->get('limit') ?: 10; // Default to 10 rows per page
        $offset = $this->input->get('offset') ?: 0;

        // Calculate the current page
        $page = ($offset / $limit) + 1;

        $data['students'] = $this->Student->get_students($search, $sort_by, $sort_order, $limit, $offset);
        $data['total_rows'] = $this->Student->get_students_count($search);
        $data['search'] = $search;
        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['page'] = $page;

        // Pass the current limit value to the view
        $data['per_page'] = $limit;

        $this->load->view('admin/list-student', $data);
    }

    // list deactivated employees
    public function list_inactive()
    {

        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $search = $this->input->get('search');
        $sort_by = $this->input->get('sort_by') ?: 'id';
        $sort_order = $this->input->get('sort_order') ?: 'asc';
        $limit = $this->input->get('limit') ?: 10; // Default to 10 rows per page
        $offset = $this->input->get('offset') ?: 0;

        // Calculate the current page
        $page = ($offset / $limit) + 1;

        $data['students'] = $this->Student->get_inactive_students($search, $sort_by, $sort_order, $limit, $offset);
        $data['total_rows'] = $this->Student->get_inactive_students_count($search);
        $data['search'] = $search;
        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['page'] = $page;

        // Pass the current limit value to the view
        $data['per_page'] = $limit;

        $this->load->view('admin/list-inactive-student', $data);
    }

    public function view($id)
    {
        $student = $this->Student->get_student_by_id($id);

        if (!empty($student['dob'])) {
            $gregorianDate = new DateTime($student['dob']);
            $ethiopianDate = new Andegna\DateTime($gregorianDate);

            $student['dob'] = $ethiopianDate->format('F d' . '፣ Y ዓ.ም');
        } else {
            $student['dob'] = 'ዕለቱ አልተመዘገበም';
        }


        if (!empty($student['registration_date'])) {
            $gregorianDateCreated = new DateTime($student['registration_date']);
            $ethiopianDateCreated = new Andegna\DateTime($gregorianDateCreated);
            $student['registration_date'] = $ethiopianDateCreated->format('F d' . '፣ Y ዓ.ም');
        } else {
            $student['registration_date'] = 'ዕለቱ አልተመዘገበም';
        }

        $data['student'] =  $student;
        $data['student_id'] = $id;
        $data['attendance'] = $this->Attendance_model->get_attendance_summary($id);
        $data['schedule'] = $this->Attendance_model->get_schedule_attendance($id);


        $this->load->view('admin/view-student', $data);
    }

    public function edit($id)
    {
        $data['student'] = $this->Student->get_student_by_id($id);
        $data['sections'] = $this->Academic_model->get_sections();
        $data['departments'] = $this->Academic_model->get_departments();

        $this->load->view('admin/edit-student', $data);
    }

    public function update($id)
    {
        $this->form_validation->set_rules('fname', 'First Name', 'required');
        $this->form_validation->set_rules('mname', 'Middle Name', 'required');
        $this->form_validation->set_rules('lname', 'Last Name', 'required');
        $this->form_validation->set_rules('christian_name', 'Christian Name', 'required');
        $this->form_validation->set_rules('repentance_father', 'Repentance Father', 'required');
        $this->form_validation->set_rules('God_father', 'God Father', 'required');
        $this->form_validation->set_rules('sex', 'Sex', 'required');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
        $this->form_validation->set_rules('pob', 'Place of Birth', 'required');
        $this->form_validation->set_rules('phone1', 'Phone 1', 'required');
        $this->form_validation->set_rules('phone2', 'Phone 2');
        $this->form_validation->set_rules('occupation', 'Occupation', 'required');
        $this->form_validation->set_rules('section_id', 'Section ID', 'required');
        $this->form_validation->set_rules('department', 'Department', 'required');
        $this->form_validation->set_rules('registration_date', 'Registration Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, reload the form with existing Student data
            $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'ፎርም ላይ የተሞሉ መረጃዎች ላይ ስህተት አለ ፣ እባክዎ አስተካክለው ይሞክሩ!']);
            $data['student'] = $this->Student->get_Student_by_id($id);
            $data['sections'] = $this->Academic_model->get_sections();
            $data['departments'] = $this->Academic_model->get_departments();

            $this->load->view('admin/edit-student', $data);
        } else {


            try {
                // Convert Ethiopian dates to Gregorian before saving
                $dob_eth = $this->input->post('dob');
                $registration_date_eth = $this->input->post('registration_date');

                $dob_greg = Andegna\DateTimeFactory::of(
                    (int)explode('/', $dob_eth)[2],
                    (int)explode('/', $dob_eth)[1],
                    (int)explode('/', $dob_eth)[0]
                )->toGregorian()->format('Y-m-d');

                $registration_date_greg = Andegna\DateTimeFactory::of(
                    (int)explode('/', $registration_date_eth)[2],
                    (int)explode('/', $registration_date_eth)[1],
                    (int)explode('/', $registration_date_eth)[0]
                )->toGregorian()->format('Y-m-d');
            } catch (\Andegna\Exception\InvalidDateException $e) {
                // Set flash data for invalid date error
                $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'ያስገቡት ቀን ልክ አይደለም። እባክዎ ያስተካክሉ!']);

                $data['student'] = $this->Student->get_Student_by_id($id);
                $data['sections'] = $this->Academic_model->get_sections();
                $data['departments'] = $this->Academic_model->get_departments();

                $this->load->view('admin/edit-student', $data);
                return;
            }

            // Fetch existing Student record
            $existing_Student = $this->Student->get_Student_by_id($id);
            $photo = $existing_Student['photo'] ?? 'default_photo.jpg'; // Default if no image exists

            // Handle Student photo upload
            if (!empty($_FILES['photo']['name'])) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/photos/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size'] = 10000; // 10MB max size
                $config['file_name'] = 'Stud-' . date('YmdHms') . '-' . rand(1, 999999);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('photo')) {
                    $uploaded = $this->upload->data();
                    $photo = $uploaded['file_name']; // Save new file name
                }
            }

            // Prepare data for update
            $data = [
                'fname' => $this->input->post('fname'),
                'mname' => $this->input->post('mname'),
                'lname' => $this->input->post('lname'),
                'christian_name' => $this->input->post('christian_name'),
                'repentance_father' => $this->input->post('repentance_father'),
                'God_father' => $this->input->post('God_father'),
                'sex' => $this->input->post('sex'),
                'dob' => $dob_greg,
                'pob' => $this->input->post('pob'),
                'phone1' => $this->input->post('phone1'),
                'phone2' => $this->input->post('phone2'),
                'occupation' => $this->input->post('occupation'),
                'section_id' => $this->input->post('section_id'),
                'department' => $this->input->post('department'),
                'registration_date' => $registration_date_greg,
                'photo' => $photo
            ];

            $updated = $this->Student->update_student($id, $data);

            // Update Student data
            if ($updated) {
                $this->session->set_flashdata('student_message', ['type' => 'success', 'text' => 'የተማሪው መረጃ በተሳካ ሁኔታ ተስተካክሏል!']);
            } else {
                $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'የተማሪው መረጃ አልተስተካከለም። እባክዎ በድጋሚ ይሞክሩ!']);
                $data['student'] = $this->Student->get_Student_by_id($id);
                $data['sections'] = $this->Academic_model->get_sections();
                $data['departments'] = $this->Academic_model->get_departments();

                $this->load->view('admin/edit-student', $data);
                return;
            }

            // Redirect to Student list
            redirect('student/list');
        }
    }


    public function deactivate_student($id)
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $active = '0';

        if ($this->Student->deactivate_Student($id, $active)) {
            // Success message
            $this->session->set_flashdata('student_message', ['type' => 'success', 'text' => 'የተማሪው ትምህርት በተሳካ ሁኔታ ተቋርጧል!']);
        } else {
            // Error message
            $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'የተማሪው ትምህርት አልተቋረጠም. እባክዎ በድጋሚ ይሞክሩ!']);
        }

        redirect('student/list');
    }

    public function activate_student($id)
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $active = '1';

        if ($this->Student->activate_student($id, $active)) {
            // Success message
            $this->session->set_flashdata('student_message', ['type' => 'success', 'text' => 'የተማሪው ትምህርት በተሳካ ሁኔታ ነቅቷል!']);
        } else {
            // Error message
            $this->session->set_flashdata('student_message', ['type' => 'error', 'text' => 'የተማሪው ትምህርት አልነቃም። እባክዎ በድጋሚ ይሞክሩ!']);
        }

        redirect('student/list_inactive');
    }

    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        // Check if the ID is valid
        if (empty($id)) {
            $this->session->set_flashdata('student_message', [
                'type' => 'error',
                'text' => 'የተማሪው መታወቂያ ቁጥር አልተገኘም!'
            ]);
            redirect('student/list_inactive');
        }

        // Attempt to delete the student
        $result = $this->Student->delete_student($id);

        if ($result) {
            // Success message
            $this->session->set_flashdata('student_message', [
                'type' => 'success',
                'text' => 'የተማሪው መረጃዎች በተሳካ ሁኔታ ተሰርዟል!'
            ]);
        } else {
            // Error message
            $this->session->set_flashdata('student_message', [
                'type' => 'error',
                'text' => 'የተማሪው መረጃዎች አልተሰረዘም. እባክዎ በድጋሚ ይሞክሩ!'
            ]);
        }

        // Redirect to the student list page
        redirect('student/list_inactive');
    }

    // Generate QR code for a student
    public function generate_qr($student_id)
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $student = $this->Student->get_student_by_id($student_id);

        if (!$student) {
            echo "ተማሪው የመረጃ ቋት ውስጥ አልተገኘም!";
            return; // Exit if student not found
        }

        // Ensure the uploads directory exists
        $upload_dir = FCPATH . 'uploads/qr_codes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate unique QR content
        $student_id = $student['id'];
        $encrypted_id = encrypt_id($student_id); // Encrypt the ID

        // Generate unique file name
        $file_name = 'Stud-' . $student['id'] . '-QR-' . date('YmdHis') . '-' . rand(10000, 99999) . '.png';
        $file_path = $upload_dir . $file_name;

        // Delete old QR code file (if it exists)
        if (!empty($student['qr_code'])) {
            $old_qr_path = $upload_dir . $student['qr_code'];
            if (file_exists($old_qr_path)) {
                unlink($old_qr_path);
            }
        }

        // Generate QR code
        QRcode::png($encrypted_id, $file_path, QR_ECLEVEL_L, 10);

        // Ensure the file is actually created
        if (!file_exists($file_path)) {
            $this->session->set_flashdata('qr_message', [
                'type' => 'error',
                'text' => 'QR Code መፍጠር አልተቻለም። እባክዎ በድጋሚ ይሞክሩ!'
            ]);
            redirect('student/list');
            return;
        }

        // Update the database with the new QR code file name
        $update_qr = $this->Student->update_qr_code($student['id'], $file_name);

        if ($update_qr) {
            $this->session->set_flashdata('qr_message', [
                'type' => 'success',
                'text' => "ለተማሪ " . $student['fname'] . " " . $student['lname'] . " QR Code በተሳካ ሁኔታ ተፈጥሯል!"
            ]);
        } else {
            $this->session->set_flashdata('qr_message', [
                'type' => 'error',
                'text' => 'QR Code መፍጠር አልተቻለም። እባክዎ በድጋሚ ይሞክሩ!'
            ]);
        }

        redirect('student/list');
    }

    // Generate ID card for a specific student
    public function generate_id($student_id)
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $student = $this->Student->get_student_by_id($student_id);
        $data['student'] = $student;

        $gregorianCurrentDate = new DateTime(date('Y-m-d'));
        $ethiopianCurrentDate = new Andegna\DateTime($gregorianCurrentDate);

        $gregorianRegistrationDate = new DateTime($student['registration_date']);
        $registration_date_in_ethiopian_calendar = new Andegna\DateTime($gregorianRegistrationDate);

        $data['ethiopian_current_date'] = $ethiopianCurrentDate->format('d/m/Y ዓ.ም');
        $data['registration_date_in_ethiopian_calendar'] =  $registration_date_in_ethiopian_calendar->format('d/m/Y ዓ.ም');

        // Load the view with student data and Ethiopian date
        $this->load->view('admin/id-card-modal', $data);
    }

    // excel
    public function export_students()
    {
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setting the headers
        $sheet->setCellValue('A1', 'መ.ቁ.');
        $sheet->setCellValue('B1', 'ስም');
        $sheet->setCellValue('C1', 'የአባት ስም');
        $sheet->setCellValue('D1', 'የአያት ስም');
        $sheet->setCellValue('E1', 'የክርስትና ስም');
        $sheet->setCellValue('F1', 'የንስሃ አባት ስም');
        $sheet->setCellValue('G1', 'ጾታ');
        $sheet->setCellValue('H1', 'የትውልድ ዘመን');
        $sheet->setCellValue('I1', 'የትውልድ ቦታ');
        $sheet->setCellValue('J1', 'ስልክ ቁ.');
        $sheet->setCellValue('K1', 'ስልክ ቁ.');
        $sheet->setCellValue('L1', 'የት/ክፍል');
        $sheet->setCellValue('M1', 'የስራ ክፍል');
        $sheet->setCellValue('M1', 'የተመዘገበበት ቀን');
        $sheet->setCellValue('M1', 'ሁኔታ');


        $students = $this->Student->get_all_students_with_section_and_department_names();

        // Populate the spreadsheet with student data
        $row = 2; // Start from the second row since the first row is for headers
        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $student['id']);
            $sheet->setCellValue('B' . $row, $student['fname']);
            $sheet->setCellValue('C' . $row, $student['mname']);
            $sheet->setCellValue('D' . $row, $student['lname']);
            $sheet->setCellValue('E' . $row, $student['christian_name']);
            $sheet->setCellValue('F' . $row, $student['repentance_father']);
            $sheet->setCellValue('G' . $row, $student['sex_amharic']);
            $sheet->setCellValue('H' . $row, $student['dob']);
            $sheet->setCellValue('I' . $row, $student['pob']);
            $sheet->setCellValue('J' . $row, $student['phone1']);
            $sheet->setCellValue('K' . $row, $student['phone2']);
            $sheet->setCellValue('L' . $row, $student['section_name']);
            $sheet->setCellValue('M' . $row, $student['department_name']);
            $sheet->setCellValue('L' . $row, $student['created_at']);
            $sheet->setCellValue('M' . $row, $student['status_text']);
            $row++;
        }

        // Save the file and output
        $writer = new Xlsx($spreadsheet);
        $filename = 'የተማሪዎች-ዝርዝር.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
