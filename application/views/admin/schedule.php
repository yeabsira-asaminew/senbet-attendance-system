<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>መርሐግብሮች | ጽርሐ ጽዮን ሰ/ት/ቤት</title>

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/schedule.css'); ?>">

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
                    <h2>መርሐግብሮች መፍጠሪያና ማስተካከያ</h2>

                    <ul class="breadcrumb">
                        <li><a href="<?php echo base_url('dashboard'); ?>">ዳሽቦርድ</a></li>
                        <li><i class="bx bx-chevron-right"></i></li>
                        <li><a class="active" href="<?php echo base_url('academic/schedule'); ?>">መርሐግብሮች</a></li>
                    </ul>
                </div>

                <?php if ($this->session->flashdata('academic_message')): ?>
                <div id="flash-message"
                    class="message-box <?php echo $this->session->flashdata('academic_message')['type']; ?>">
                    <?php echo $this->session->flashdata('academic_message')['text']; ?>
                </div>
                <?php endif; ?>


            </div>

            <div class="unique-form-container">
                <div class="schedule-container">

                    <!-- Add Schedule Form -->
                    <?php if ($this->session->userdata('role') == 'superadmin'): ?>
                    <div class="form-container">
                        <form method="post" action="<?= site_url('academic/add_schedule'); ?>" class="schedule-form">
                            <label for="day">ቀን:</label>
                            <select name="day" id="day" class="schedule-select">
                                <option value="Monday">ሰኞ</option>
                                <option value="Tuesday">ማክሰኞ</option>
                                <option value="Wednesday">ረቡዕ</option>
                                <option value="Thursday">ሐሙስ</option>
                                <option value="Friday">አርብ</option>
                                <option value="Saturday">ቅዳሜ</option>
                                <option value="Sunday">እሁድ</option>
                            </select>

                            <label for="time">ሰዓት:</label>
                            <input type="time" name="time" id="time" class="schedule-input" required>

                            <label>የትምህርት ክፍሎች:</label><br>
                            <div class="checkbox-group">
                                <?php foreach ($sections as $section): ?>
                                <label class="schedule-checkbox">
                                    <input type="checkbox" name="sections[]" value="<?= $section['id'] ?>">
                                    <span class="checkmark"></span> <?= $section['name'] ?>
                                </label><br>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="schedule-button">መርሐግብር መዝግብ</button>
                        </form>
                    </div>
                    <?php endif; ?>


                    <!-- Schedule List -->
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ቁጥር</th>
                                    <th>ቀን</th>
                                    <th>ሰዓት</th>
                                    <th>የት/ክፍል</th>

                                    <?php if ($this->session->userdata('role') == 'superadmin'): ?>
                                    <th>መፈጸሚያዎች</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)): ?>
                                <?php
                                    // Mapping array for English to Amharic day names
                                    $dayTranslations = [
                                        "Monday" => "ሰኞ",
                                        "Tuesday" => "ማክሰኞ",
                                        "Wednesday" => "ረቡዕ",
                                        "Thursday" => "ሐሙስ",
                                        "Friday" => "አርብ",
                                        "Saturday" => "ቅዳሜ",
                                        "Sunday" => "እሁድ"
                                    ];

                                    foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?= $schedule['id'] ?></td>
                                    <td><?= $dayTranslations[$schedule['day']] ?? $schedule['day'] ?></td>
                                    <td><?= $schedule['time'] ?></td>
                                    <td><?= $schedule['sections'] ?></td>


                                    <!-- only superadmins has the previlege -->
                                    <?php if ($this->session->userdata('role') == 'superadmin'): ?>
                                    <td>
                                        <a href="javascript:void(0);" class="edit-btn"
                                            onclick="openModal(<?= $schedule['id'] ?>, '<?= $schedule['day'] ?>', '<?= $schedule['time'] ?>', '<?= $schedule['sections'] ?>')">
                                            አርትዕ
                                        </a> |
                                        <a href="<?= site_url('academic/delete_schedule/' . $schedule['id']) ?>"
                                            onclick="return confirm('እርግጠኛ ነዎት ይሄን መርሐግብር መሰረዝ ይፈልጋሉ?')"
                                            class="delete-btn">
                                            ሰርዝ
                                        </a>
                                    </td>
                                    <?php endif; ?>

                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="100%" style="text-align: center;">
                                        <h2>ምንም መርሐግብር አልተገኘም.</h2>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Edit Modal -->
                    <div id="editModal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closeModal()">&times;</span>
                            <h2>መርሐግብር ማስተካከያ</h2>


                            <form id="editForm" method="post" action="<?= site_url('academic/update_schedule'); ?>/"
                                onsubmit="this.action += document.getElementById('editId').value;">
                                <input type="hidden" name="id" id="editId">
                                <label for="day">ቀን:</label>
                                <select name="day" id="editDay" class="schedule-select">
                                    <option value="Monday">ሰኞ</option>
                                    <option value="Tuesday">ማክሰኞ</option>
                                    <option value="Wednesday">ረቡዕ</option>
                                    <option value="Thursday">ሐሙስ</option>
                                    <option value="Friday">አርብ</option>
                                    <option value="Saturday">ቅዳሜ</option>
                                    <option value="Sunday">እሁድ</option>
                                </select>

                                <label for="time">ሰዓት:</label>
                                <input type="time" name="time" id="editTime" class="schedule-input" required>

                                <label>የትምህርት ክፍሎች:</label><br>
                                <div class="checkbox-group">
                                    <?php foreach ($sections as $section): ?>
                                    <label class="schedule-checkbox" id="editSections">
                                        <input type="checkbox" name="sections[]" value="<?= $section['id'] ?>">
                                        <span class="checkmark"></span> <?= $section['name'] ?>
                                    </label><br>
                                    <?php endforeach; ?>
                                </div>

                                <button type="submit" class="schedule-button">አርትዕ </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="<?= base_url('assets/js/script.js'); ?>"></script>
    <script>
    //schedule
    // Select the modal
    const modal = document.getElementById('editModal');

    function openModal(id, day, time, sections) {
        document.getElementById('editId').value = id; // Set the ID
        document.getElementById('editDay').value = day;
        document.getElementById('editTime').value = time;

        // Clear and set sections
        const checkboxes = document.querySelectorAll('#editSections input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = sections.includes(checkbox.value);
        });

        modal.style.display = "flex";
    }
    // Function to close modal
    function closeModal() {
        modal.style.display = "none";
    }

    // Close modal if user clicks outside the modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

    // Ensure modal is hidden on page load
    document.addEventListener("DOMContentLoaded", function() {
        modal.style.display = "none";
    });
    </script>
</body>

</html>