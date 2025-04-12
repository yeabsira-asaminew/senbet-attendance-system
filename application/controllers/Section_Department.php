<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Section_Department extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Section_Department_model');

        if (!$this->session->userdata('uid')) {
            redirect('login');
        } 
    }


    public function index() {
        $data['schedules'] = $this->Schedule_model->get_schedules();
        $data['sections'] = $this->Schedule_model->get_sections(); // Fetch sections

        $this->load->view('admin/schedule', $data);
    }

    public function add_schedule() {
        // Get input data
        $day = $this->input->post('day');
        $time = $this->input->post('time');
        $sections = $this->input->post('sections'); // Get selected sections
    
        // Validate input
        if (empty($day) || empty($time) || empty($sections)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'ሁሉንም መረጃዎች ማስገባት ያስፈልጋል!'
            ]);
            redirect('schedule'); 
            return;
        }
    
        $data = [
            'day' => $day,
            'time' => $time
        ];
    
        if ($this->Schedule_model->add_schedule($data, $sections)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ታክሏል!'
            ]);
        } else {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልታከለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }
    
        redirect('schedule');
    }
    
    public function update_schedule($id = null) {
        // Ensure ID is provided
        if (empty($id)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'የመርሐግብሩ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('schedule');
            return;
        }
    
        // Get input data
        $day = $this->input->post('day');
        $time = $this->input->post('time');
        $sections = $this->input->post('sections');
    
        // Validate input
        if (empty($day) || empty($time) || empty($sections)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'ሁሉንም መረጃዎች ማስገባት ያስፈልጋል!'
            ]);
            redirect('schedule');
            return;
        }
    
        $data = [
            'day' => $day,
            'time' => $time
        ];
    
        if ($this->Schedule_model->update_schedule($id, $data, $sections)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ተሻሽሏል!'
            ]);
        } else {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልተሻሻለም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        redirect('schedule');
    }
    

    public function delete_schedule($id = null) {
         // Ensure ID is provided
         if (empty($id)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'የመርሐግብሩ መለያ ቁጥር ልክ አይደለም!'
            ]);
            redirect('schedule');
            return;
        }

        if ($this->Schedule_model->delete_schedule($id)) {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'success', 
                'text' => 'መርሐግብሩ በተሳካ ሁኔታ ተሰርዟል!'
            ]);
        } else {
            $this->session->set_flashdata('schedule_message', [
                'type' => 'error', 
                'text' => 'መርሐግብሩ አልተሰረዘም። እባኮትን እንደገና ይሞክሩ!'
            ]);
        }

        
        redirect('schedule');
    }

    
}
?>