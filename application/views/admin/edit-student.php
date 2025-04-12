<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>ተማሪ አርታዒ | ጽርሐ ጽዮን ሰ/ት/ቤት</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
</head>

<body>

    <?php include APPPATH . 'views/admin/includes/sidebar.php'; ?>

    <!-- CONTENT -->
    <section id="content">
        <?php include APPPATH . 'views/admin/includes/topbar.php'; ?>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>የተማሪ መረጃዎች አርትዕ</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="<?php echo base_url('dashboard'); ?>">ዳሽቦርድ</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li> <a>የተማሪዎች ዝርዝር</a> </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="<?php echo base_url('student/edit'); ?>">ተማሪ አርታዒ</a>
                        </li>
                    </ul>
                </div>
                <!-- error and success message-->
                <?php if ($this->session->flashdata('student_message')): ?>
                <div id="flash-message"
                    class="message-box <?php echo $this->session->flashdata('student_message')['type']; ?>">
                    <?php echo $this->session->flashdata('student_message')['text']; ?>
                </div>
                <?php endif; ?>
            </div>


            <div class="unique-form-container">

                <?php echo form_open_multipart("student/update/" . $student['id']); ?>
                <div class="unique-form-grid">
                    <div class="unique-form-group">
                        <label for="fname">ስም <span class="required">*</span></label>
                        <input type="text" name="fname" id="fname" value="<?php echo $student['fname']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="mname">የአባት ስም <span class="required">*</span></label>
                        <input type="text" id="mname" name="mname" value="<?php echo $student['mname']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="lname">የአያት ስም <span class="required">*</span></label>
                        <input type="text" id="lname" name="lname" value="<?php echo $student['lname']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="christian_name">የክርስትና ስም <span class="required">*</span></label>
                        <input type="text" id="christian_name" name="christian_name"
                            value="<?php echo $student['christian_name']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="repentance_father">የንሰሃ አባት ስም <span class="required">*</span></label>
                        <input type="text" id="repentance_father" name="repentance_father"
                            value="<?php echo $student['repentance_father']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="God_father">የክርስትና አባት ስም <span class="required">*</span></label>
                        <input type="text" id="God_father" name="God_father"
                            value="<?php echo $student['God_father']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="sex">ጾታ <span class="required">*</span></label>
                        <select name="sex" id="sex">
                            <option value="">--ምረጥ--</option>
                            <option value="Male" <?php echo ($student['sex'] == 'Male') ? 'selected' : ''; ?>>ወንድ
                            </option>
                            <option value="Female" <?php echo ($student['sex'] == 'Female') ? 'selected' : ''; ?>>ሴት
                            </option>
                        </select>
                    </div>

                    <div class="unique-form-group">
                        <label for="dob">የትውልድ ዘመን <span class="required">*</span></label>
                        <input type="text" id="dob" name="dob" value="<?php echo date('d/m/Y', strtotime($student['dob'])); ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="pob">የትውልድ ቦታ <span class="required">*</span></label>
                        <input type="text" id="pob" name="pob" value="<?php echo $student['pob']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="phone1">ስልክ ቁጥር <span class="required">*</span></label>
                        <input type="tel" id="phone1" name="phone1" value="<?php echo $student['phone1']; ?>" required>
                    </div>

                    <div class="unique-form-group">
                        <label for="phone2">ተጨማሪ ስልክ ቁጥር </label>
                        <input type="tel" id="phone2" name="phone2" value="<?php echo $student['phone2']; ?>">
                    </div>

                    <div class="unique-form-group">
                        <label for="occupation">ስራ <span class="required">*</span></label>
                        <input type="text" id="occupation" name="occupation"
                            value="<?php echo $student['occupation']; ?>">
                    </div>

                    <div class="unique-form-group">
                        <label for="section_id">የትምህርት ክፍል <span class="required">*</span></label>
                        <select id="section_id" name="section_id">
                            <option value="">--ምረጥ--</option>
                            <?php if (!empty($sections)): ?>
                            <?php foreach ($sections as $section): ?>
                            <option value="<?php echo $section['id']; ?>"
                                <?php echo ($section['id'] == $student['section_id']) ? 'selected' : ''; ?>>
                                <?php echo $section['name']; ?>
                            </option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option disabled>ምንም የትምህርት ክፍል አልተገኘም</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="unique-form-group">
                        <label for="department">የስራ ክፍል <span class="required">*</span></label>
                        <select name="department" id="department">
                            <option value="">--ምረጥ--</option>
                            <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                            <option value="<?php echo $department['id']; ?>"
                                <?php echo ($department['id'] == $student['department']) ? 'selected' : ''; ?>>
                                <?php echo $department['name']; ?>
                            </option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option disabled>ምንም የስራ ክፍል አልተገኘም</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="unique-form-group">
                        <label for="registration_date">የምዝገባ ቀን <span class="required">*</span></label>
                        <input type="text" id="registration_date" name="registration_date" value="<?php echo date('d/m/Y', strtotime($student['registration_date'])); ?>" required>
                    </div>
            
                    <div class="unique-form-group">
                        <label for="photo">የተማሪው ምስል(የተፈቀደው ከፍተኛ 10MB) (የአሁን ምስል:
                            <?php echo $student['photo']; ?>): <span class="required">*</span></label>
                        <input type="file" name="photo" id="photo">
                    </div>


                    <div class="unique-form-actions">
                        <button type="submit"
                            style="background-color: #007bff; color: #fff; border: none; padding: 10px 20px; font-size: 14px; border-radius: 4px; cursor: pointer; margin: 10px auto; display: block;">
                            አርትዕ
                        </button>
                    </div>

                </div>


        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
</body>

<script src="<?php echo base_url('assets/js/script.js'); ?>"></script>
<script>
    function formatEthiopianDateInput(input) {
        input.addEventListener('input', function (e) {
            let value = e.target.value;

            // Remove non-numeric characters
            value = value.replace(/\D/g, '');

            // Add forward slashes after day and month
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            if (value.length > 5) {
                value = value.slice(0, 5) + '/' + value.slice(5, 9); // Year is 4 digits
            }

            // Validate day, month, and year
            if (value.length >= 2) {
                const day = parseInt(value.slice(0, 2), 10);
                if (day > 30 || day === 0) {
                    // Replace invalid day with '01'
                    value = '01' + value.slice(2);
                }
            }

            if (value.length >= 5) {
                const month = parseInt(value.slice(3, 5), 10);
                if (month > 13 || month === 0) {
                    // Replace invalid month with '01'
                    value = value.slice(0, 3) + '01' + value.slice(5);
                }
            }

            if (value.length >= 10) {
                const year = parseInt(value.slice(6, 10), 10);
                if (year === 0) {
                    // Replace invalid year with '1990'
                    value = value.slice(0, 6) + '1990';
                }
            }

            // Update the input value
            e.target.value = value;
        });

        input.addEventListener('keydown', function (e) {
            // Allow backspace, delete, and arrow keys
            if (e.key === 'Backspace' || e.key === 'Delete' || e.key.startsWith('Arrow')) {
                return;
            }

            // Block non-numeric characters
            if (/\D/.test(e.key)) {
                e.preventDefault();
            }

            // Auto-jump to the next field
            const value = e.target.value;
            if (value.length === 2 || value.length === 5) {
                e.target.value += '/';
            }
        });
    }

    // Apply the formatting to both date inputs
    const dobInput = document.getElementById('dob');
    const registrationDateInput = document.getElementById('registration_date');

    formatEthiopianDateInput(dobInput);
    formatEthiopianDateInput(registrationDateInput);
</script>
</html>