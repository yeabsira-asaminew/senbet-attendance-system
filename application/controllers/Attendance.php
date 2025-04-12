<?php
defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . 'vendor/autoload.php';

use Andegna\DateTime as EthiopianDateTime;
use Andegna\DateTimeFactory;
use Andegna\Constants;

class Attendance extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('uid')) {
            redirect('login');
        }

        $this->load->model('Attendance_model');
        $this->load->model('Student_model');
    }

    public function scanner()
    {
        $this->load->view('admin/scan_qr');
    }

    public function record()
    {
        $encrypted_id = $this->input->post('student_id'); // Get the encrypted ID from the scanned QR code
        $student_id = decrypt_id($encrypted_id); // Decrypt the ID

        if (!$student_id) {
            $response = [
                'status' => 'error',
                'message' => '❌ የተማሪው መታወቂያ ቁጥር ልክ አይደለም!'
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // Proceed with the existing attendance recording logic using the decrypted ID
        $student = $this->Attendance_model->get_student($student_id);

        if ($student) {
            // Check if the student is inactive (status = 0)
            if ($student->status == 0) {
                $response = [
                    'status' => 'error',
                    'student_id' => $student_id,
                    'message' => '❌ የተማሪው ትምህርት ተቋርጧል(Inactive Student)'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            $section_id = $student->section_id;
            $current_day = date('l'); // Get current day (e.g., "Sunday")
            $current_time = strtotime(date('H:i:s')); // Convert current time to timestamp
            $current_date = date('Y-m-d'); // Get current date

            // Check if attendance has already been recorded today
            if ($student->last_attendance_date == $current_date) {
                $response = [
                    'status' => 'error',
                    'student_id' => $student_id,
                    'message' => '⚠️ የእለቱ የተማሪው አቴንዳንስ ቀድሞ ተመዝግቧል!'
                ];
            } else {
                // Get all schedules for the student's section on the current day
                $schedules = $this->Attendance_model->get_schedules_for_day($section_id, $current_day);

                if (!empty($schedules)) {
                    $attendance_recorded = false;

                    foreach ($schedules as $schedule) {
                        $schedule_time = strtotime($schedule->time);
                        $start_recording_time = $schedule_time - 1800; // 30 minutes before
                        $end_recording_time = $schedule_time + 3600; // 1 hour after
                        $end_absent_time = $schedule_time + 4800;
                        // Check if the current time is within the allowed attendance window
                        if ($current_time >= $start_recording_time && $current_time <= $end_recording_time) {
                            // Record attendance
                            $this->Attendance_model->record_attendance($student_id, 'present', $current_date);
                            $response = [
                                'status' => 'success',
                                'student_id' => $student_id,
                                'message' => '✅ የተማሪው አቴንዳንስ በተሳካ ሁኔታ ተመዝግቧል!'
                            ];
                            $attendance_recorded = true;
                            break;
                        } elseif ($current_time > $end_recording_time && $current_time <= $end_absent_time) {
                            // Record attendance
                            $this->Attendance_model->record_attendance($student_id, 'absent', $current_date);
                            $response = [
                                'status' => 'success',
                                'student_id' => $student_id,
                                'message' => '✅ የተማሪው አቴንዳንስ ቀሪ ተብሎ ተመዝግቧል!'
                            ];
                            $attendance_recorded = true;
                            break;
                        }
                    }

                    if (!$attendance_recorded) {
                        $response = [
                            'status' => 'error',
                            'student_id' => $student_id,
                            'message' => '⚠️ ከመርሐግብሩ ቀደም ብለዋል አልያም ዘግይተዋል'
                        ];
                    }
                } else {
                    $response = [
                        'status' => 'error',
                        'student_id' => $student_id,
                        'message' => '⚠️ ተማሪው መርሐግብሩ ዛሬ አይደለም!'
                    ];
                }
            }
        } else {
            $response = [
                'status' => 'error',
                'student_id' => $student_id,
                'message' => '❌ ተማሪው መረጃ ቋት ውስጥ አልተገኘም!'
            ];
        }

        // Return JSON response
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function record_absent_for_all()
    {
        $current_day = date('l'); // Get current day (e.g., "Sunday")
        $current_date = date('Y-m-d'); // Get current date
        $current_time = strtotime(date('H:i:s'));

        // Get all sections with schedules for the current day
        $sections_with_schedules = $this->Attendance_model->get_sections_with_schedules_for_day($current_day);

        if (!empty($sections_with_schedules)) {
            foreach ($sections_with_schedules as $section) {
                $section_id = $section->section_id;

                $schedule_time = strtotime($section->time);
                $end_recording_time = $schedule_time + 3600; // 1 hour after
                $end_absent_time = $schedule_time + 4800;
                // Check if the current time is within the absent attendance window
                if ($current_time > $end_recording_time && $current_time <= $end_absent_time) {
                    // Get all students in the section who haven't recorded attendance today
                    $absent_students = $this->Attendance_model->get_absent_students($section_id, $current_date);

                    if (!empty($absent_students)) {
                        foreach ($absent_students as $student) {
                            // Record attendance as absent for each student
                            $this->Attendance_model->record_attendance($student->id, 'absent', $current_date);
                        }
                        $response = [
                            'status' => 'success',
                            'message' => "✅ ያልመጡ ተማሪዎች አቴንዳንስ 'ቀሪ' ሆኖ ተመዝግቧል!"
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => "⚠️ የሁሉም ተማሪዎች አቴንዳንስ ቀድሞ ተመዝግቧል!"
                        ];
                    }
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => "⚠️ የመርሐግብሩ አቴንዳንስ መመዝገቢያ ጊዜ ገደብ ተጠናቋል አልያም ለመጠናቀቅ ጥቂት ይቀረዋል!"
                    ];
                }
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => '⚠️ ዛሬ ምንም የትምህርት መርሐግብር የለም ወይም የመርሐግብሩ ጊዜ አልፏል!'
            ];
        }

        // Load the view with the response
        $data['response'] = $response;
        $this->load->view('admin/scan_qr', $data);
        // $this->load->view('admin/scan_qr', $response);
    }


    public function list()
    {
        // Get the Ethiopian date from the input
        $selectedDate = $this->input->get('date');

        // If no date is provided, use the current Ethiopian date
        if (empty($selectedDate)) {
            // Get the current Gregorian date
            $gregorianDate = new DateTime();

            // Convert the Gregorian date to Ethiopian
            $ethiopianDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);

            // Format the Ethiopian date as "d/m/Y"
            $selectedDate = $ethiopianDate->format('d/m/Y');
        }

        try {
            // Split the Ethiopian date into day, month, and year
            $selectedDateParts = explode('/', $selectedDate);
            if (count($selectedDateParts) !== 3) {
                throw new \Andegna\Exception\InvalidDateException("Invalid date format");
            }

            // Create an Ethiopian date object using DateTimeFactory
            $ethiopianDate = \Andegna\DateTimeFactory::of(
                (int)$selectedDateParts[2], // Year
                (int)$selectedDateParts[1], // Month
                (int)$selectedDateParts[0]  // Day
            );

            // Convert Ethiopian date to Gregorian
            $gregorianDate = $ethiopianDate->toGregorian()->format('Y-m-d');
        } catch (\Andegna\Exception\InvalidDateException $e) {
            // Handle invalid date input
            $this->session->set_flashdata('attendance_message', [
                'type' => 'error',
                'text' => 'ያስገቡት ቀን ልክ አይደለም። እባክዎ ያስተካክሉ!'
            ]);
            redirect('attendance/list');
            return;
        }

        // Fetch attendances for the selected Gregorian date
        $data['attendances'] = $this->Attendance_model->get_attendances($gregorianDate);

        // Convert Gregorian dates in the attendance data to Ethiopian for display
        foreach ($data['attendances'] as &$attendance) {
            if (!empty($attendance['created_date'])) {
                $gregorianDate = new DateTime($attendance['created_date']);
                $ethiopianDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);
                $attendance['ethiopian_date'] = $ethiopianDate->format('F d፣ Y ዓ.ም'); // Format as "Month Day, Year"

                $gregorianTime = new DateTime($attendance['created_at']);
                $ethiopianTime = \Andegna\DateTimeFactory::fromDateTime($gregorianTime);
                $time = $ethiopianTime->format('h:i A');
                $attendance['ethiopian_time'] = $time;
            } else {
                $attendance['ethiopian_date'] = 'ዕለቱ አልተመዘገበም'; // "Date not recorded"
            }
        }

        // Pass the selected Ethiopian date and attendances to the view
        $data['selected_date'] = $selectedDate;
        $this->load->view('admin/list-attendance', $data);
    }

    // delete attendace 
    public function delete_attendance($id)
    {
        $attendance = $this->Attendance_model->getAttend($id);

        if (empty($attendance)) {
            $this->session->set_flashdata('attendDel_error', 'Something went wrong, attendance cannot be found!');
            redirect('hr/Attendance/list');
            return;
        }
        $this->leave->record_leave([
            'emp_id' => $attendance['emp_id'],
            'leave_type' => "Emergency",
            'start_date' => date('Y-m-d', strtotime($attendance['date'])),
            'end_date' => date('Y-m-d', strtotime($attendance['date'])),
        ]);

        $this->Attendance_model->delete_single_attendance($id);

        $this->session->set_flashdata('attendDel_success', 'Attendance is deleted successfully!');
        redirect('hr/Attendance/list');
    }
}
