<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Academic_model');

        if (!$this->session->userdata('uid')) {
            redirect('login');
        } 
    }

    public function schedule() {
        // Fetch schedules and sections
        $schedules = $this->Academic_model->get_schedules();
        $sections = $this->Academic_model->get_sections();
    
        // Adjust time to Ethiopian time (GMT-3)
        foreach ($schedules as &$schedule) {
            // Convert the time to a timestamp, subtract 6 hours, and format it back to a time string
            $time = date('h:i A', strtotime($schedule['time']) - 6 * 3600);
    
            // Replace AM with ቀን and PM with ማታ
            $time = str_replace(['AM', 'PM'], ['ቀን', 'ማታ'], $time);
    
            $schedule['time'] = $time;
        }
    
        $data['schedules'] = $schedules;
        $data['sections'] = $sections;
    
        $this->load->view('admin/schedule', $data);
    }


    public function add_schedule() {
        // Get input data
        $day = $this->input->post('day');
        $time = $this->input->post('time');
        $sections = $this->input->post('sections'); // Get selected sections
    
        // Validate input
        if (empty($day) || empty($time) || empty($sections)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'ሁሉንም መረጃዎች ማስገባት ያስፈልጋል!'
            ]);
            redirect('academic/schedule'); 
            return;
        }
        // Adjust time to GMT+3 before saving
        $gmt_time = date('H:i:s', strtotime($time) + 6 * 3600); // Add 6 hours

        $data = [
            'day' => $day,
            'time' => $gmt_time
        ];
    
        if ($this->Academic_model->add_schedule($data, $sections)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ታክሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልታከለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }
    
        redirect('academic/schedule');
    }
    
    public function update_schedule($id = null) {
        // Ensure ID is provided
        if (empty($id)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የመርሐግብሩ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('academic/schedule');
            return;
        }
    
        // Get input data
        $day = $this->input->post('day');
        $time = $this->input->post('time');
        $sections = $this->input->post('sections');
    
        // Validate input
        if (empty($day) || empty($time) || empty($sections)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'ሁሉንም መረጃዎች ማስገባት ያስፈልጋል!'
            ]);
            redirect('academic/schedule');
            return;
        }

      // Adjust time to GMT+3 before saving
      $gmt_time = date('H:i:s', strtotime($time) + 6 * 3600); // Add 6 hours

      $data = [
          'day' => $day,
          'time' => $gmt_time
      ];
    
        if ($this->Academic_model->update_schedule($id, $data, $sections)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ተሻሽሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልተሻሻለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/schedule');
    }

    public function delete_schedule($id = null) {
         // Ensure ID is provided
         if (empty($id)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የመርሐግብሩ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('academic/schedule');
            return;
        }

        if ($this->Academic_model->delete_schedule($id)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ተሰርዟል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልተሰረዘም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/schedule');
    }

    // sections and departments
 
 public function department_and_section() {
    $data['sections'] = $this->Academic_model->get_sections();
    $data['departments'] = $this->Academic_model->get_departments(); 

    $this->load->view('admin/department-and-section', $data);
}

public function save_department() {

    $this->form_validation->set_rules('name', 'Name', 'required');

    $data['sections'] = $this->Academic_model->get_sections();
    $data['departments'] = $this->Academic_model->get_departments(); 

    // Run Validation
    if ($this->form_validation->run() == FALSE) {

        $this->load->view('admin/department-and-section', $data);
    } else {
        $name = $this->input->post('name');
        $data = [
            'name' => $name, 
        ];
        // Attempt to save the department
        if ($this->Academic_model->add_department($data)) { // Corrected model method
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'የስራ ክፍሉ በተሳካ ሁኔታ ታክሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የስራ ክፍሉ አልታከለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/department_and_section');
    }
}

public function save_section() {

    $this->form_validation->set_rules('name', 'Name', 'required');

    $data['sections'] = $this->Academic_model->get_sections();
    $data['departments'] = $this->Academic_model->get_departments(); 

    // Run Validation
    if ($this->form_validation->run() == FALSE) {

        $this->load->view('admin/department-and-section', $data);
    } else {
        $name = $this->input->post('name');

         $data = [
            'name' => $name, 
        ];

        if ($this->Academic_model->add_section($data)) { // Corrected model method
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'የትምህርት ክፍሉ በተሳካ ሁኔታ ታክሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት ክፍሉ አልታከለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/department_and_section');
    }
}

public function delete_department($id) {
$result = $this->Academic_model->delete_department($id);
    if ($result) {
        $this->session->set_flashdata('academic_message', [
            'type' => 'success', 
            'text' => 'የስራ ክፍሉ በተሳካ ሁኔታ ተሰርዟል!'
        ]);
    } else {
        $this->session->set_flashdata('academic_message', [
            'type' => 'error', 
            'text' => 'የስራ ክፍሉ አልተሰረዘም። እባኮትን እንደገና ይሞክሩ!'
        ]);

    }

    redirect('academic/department_and_section');
}

public function delete_section($id) {
    $result = $this->Academic_model->delete_section($id);
        if ($result) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'የትምህርት ክፍሉ በተሳካ ሁኔታ ተሰርዟል!'
            ]);
            
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት  ክፍሉ አልተሰረዘም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }
    
        redirect('academic/department_and_section');
    }
 
    public function update_section($id = null) {
        // Ensure ID is provided
        if (empty($id)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት ክፍሉ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('academic/department_and_section');
            return;
        }

        $data = [
            'name' => $this->input->post('name') 
        ];

        if ($this->Academic_model->update_section($id, $data)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'የትምህርት ክፍሉ በተሳካ ሁኔታ ተሻሽሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት ክፍሉ አልተሻሻለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/department_and_section');
    }
    
    public function update_department($id = null) {
        // Ensure ID is provided
        if (empty($id)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት ክፍሉ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('academic/department_and_section');
            return;
        }
    
        $data = [
            'name' => $this->input->post('name') 
        ];
    
        if ($this->Academic_model->update_department($id, $data)) {
            $this->session->set_flashdata('academic_message', [
                'type' => 'success', 
                'text' => 'የትምህርት ክፍሉ በተሳካ ሁኔታ ተሻሽሏል!'
            ]);
        } else {
            $this->session->set_flashdata('academic_message', [
                'type' => 'error', 
                'text' => 'የትምህርት ክፍሉ አልተሻሻለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('academic/department_and_section');
    }
}