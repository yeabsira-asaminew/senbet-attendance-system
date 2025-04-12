<div class="id-card">
  <!-- Watermark Logo -->
  <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Watermark Logo" class="watermark-logo">

  <!-- Header -->
  <div class="id-card-header">
    <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Company Logo" class="logo">
    <h1 class="company-name">ደብረ ታቦር ቅዱስ እግዚአብሄር አብ ጽርሐ ጽዮን ሰ/ት/ቤት</h1>
  </div>

  <!-- Content -->
  <div class="id-card-content">
    <!-- QR Code with Date -->
    <div class="qr-code">
      <img src="<?php echo base_url('uploads/qr_codes/') . $student['qr_code']; ?>"
      alt="<?php echo "የተማሪ " .  $student['fname'] . "QR Code"; ?>" >
      <div class="generated-date">የታተመበት ቀን <?= $ethiopian_current_date; ?></div>
    </div>

    <!-- Student Details -->
    <div class="id-card-details">
      <p><strong>መታወቂያ ቁ.፡</strong> <span id="student-id"><?= $student['id']; ?></span></p>
      <p><strong>ሙሉ ስም:</strong> <span id="full-name"><?= $student['fname'] . ' ' . $student['mname'] . ' ' . $student['lname']; ?></span></p>
      <p><strong>ጾታ:</strong> <span id="department"><?= $student['sex_amharic']; ?></span></p>
      <p><strong>የት/ክፍል:</strong> <span id="section"><?= $student['section_name']; ?></span></p>
      <p><strong>የስራ ክፍል:</strong> <span id="department"><?= $student['department_name']; ?></span></p>
      <p><strong>የምዝገባ ቀን:</strong> <span id="registration_date"><?= $registration_date_in_ethiopian_calendar; ?></span></p>
    </div>
  </div>
</div>