<?php
// Function to get holidays from API
function getHolidays($year) {
    $apiUrl = "https://hari-libur-api.vercel.app/api?year=".$year;
    
    try {
        $response = file_get_contents($apiUrl);
        $holidaysData = json_decode($response, true);
        
        $nationalHolidays = [];
        foreach ($holidaysData as $holiday) {
            if ($holiday['is_national_holiday']) {
                $holidayDate = $holiday['event_date'];
                $nationalHolidays[$holidayDate] = $holiday['event_name'];
            }
        }
        
        return $nationalHolidays;
    } catch (Exception $e) {
        error_log("Error fetching holidays: ".$e->getMessage());
        return [];
    }
}

// Get holidays for the selected year
$selectedYear = date('Y', strtotime($bulan.'-01'));
$liburNasional = getHolidays($selectedYear);

// Function to check if date is holiday or Sunday
function isHoliday($date, $liburNasional) {
    $dayOfWeek = date('N', strtotime($date));
    return isset($liburNasional[$date]) || $dayOfWeek == 7; // Sunday is day 7
}

// Get holidays for the current month only
$selectedMonth = date('m', strtotime($bulan.'-01'));
$liburBulanIni = array_filter($liburNasional, function($date) use ($selectedMonth) {
    return date('m', strtotime($date)) == $selectedMonth;
}, ARRAY_FILTER_USE_KEY);

// Initialize summary variables
$totalTerlambatSeconds = 0;
$totalLemburSeconds = 0;
$totalWorkDays = 0;
$totalCuti = 0;
$totalTugasLuar = 0;

// Calculate summary data
foreach ($dataKalender as $date => $data) {
    // Count work days (has either check-in or check-out)
    if (!empty($data['jam_masuk']) || !empty($data['jam_pulang'])) {
        $totalWorkDays++;
    }
    
    // Calculate lateness ONLY if there's no special status
    if (empty($data['status_khusus'])) {
        if (!empty($data['jam_masuk']) && !empty($data['jam_masuk_shift'])) {
            $jamMasuk = str_replace(['<span style="color:red;">', '</span>'], '', $data['jam_masuk']);
            $jamMasukShift = $data['jam_masuk_shift'];
            
            $masukTime = strtotime($jamMasuk);
            $shiftTime = strtotime($jamMasukShift);
            
            if ($masukTime > $shiftTime) {
                $lateSeconds = $masukTime - $shiftTime;
                $totalTerlambatSeconds += $lateSeconds;
            }
        }
    }
    
    // Calculate overtime
    if (!empty($data['alasan_lembur']) && !empty($data['lembur_masuk']) && !empty($data['lembur_pulang'])) {
        // Create DateTime objects for more reliable date handling
        $lemburMasuk = DateTime::createFromFormat('H:i:s', $data['lembur_masuk']);
        $lemburPulang = DateTime::createFromFormat('H:i:s', $data['lembur_pulang']);
        
        // If end time is earlier than start time (crossing midnight)
        if ($lemburPulang < $lemburMasuk) {
            $lemburPulang->add(new DateInterval('P1D')); // Add 1 day to end time
        }
        
        // Calculate duration
        $interval = $lemburMasuk->diff($lemburPulang);
        $lemburSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        
        // Only count if duration is positive and reasonable (less than 16 hours)
        if ($lemburSeconds > 0 && $lemburSeconds <= 57600) {
            $totalLemburSeconds += $lemburSeconds;
        }
    }
    
    // Count leave and out-of-office assignments
    if (!empty($data['status_khusus'])) {
        $status = strtolower($data['status_khusus']);
        if (strpos($status, 'cuti') !== false) {
            $totalCuti++;
        } elseif (strpos($status, 'tugas luar') !== false || strpos($status, 'dinas luar') !== false) {
            $totalTugasLuar++;
        }
    }
}

// Function to convert seconds to hours:minutes:seconds
function secondsToTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
?>

<style>
        .kalender-cell {
            width: 160px;
            height: 130px;
            padding: 4px;
            font-size: 12px;
            text-align: left;
            vertical-align: top;
            border: 1px solid #dee2e6;
            position: relative;
        }

        .shift-box {
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            margin-bottom: 4px;
            display: block;
            text-align: center;
            text-transform: capitalize;
        }

        .shift-office { background-color: #07b8b2; }
        .shift-office1 { background-color: #07b8b2; }
        .shift-office2 { background-color: #07b8b2; }
        .shift-pagibangsal { background-color: #07b8b2; }
        .shift-pagi { background-color: #00ffbf; }
        .shift-siang { background-color: #ffbf00; }
        .shift-malam { background-color: #00bfff; }
        .shift-midle { background-color: #ff6699; }

        .jam-masuk-late { color: red; font-weight: bold; }
        .jam-label { font-weight: bold; }

        .lembur-section {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #ccc;
        }

        .lembur-title {
            font-weight: bold;
            color: #0066cc;
        }

        .lembur-alasan {
            font-size: 11px;
            color: #666;
            font-style: italic;
            background-color: #ffffcc;
            padding: 2px;
            border-radius: 3px;
        }

        .terlambat {
            color: red;
            font-weight: bold;
            margin-top: 2px;
        }

        .status-khusus {
            margin-top: 4px;
            padding: 2px;
            background-color: #ffcc00;
            border-radius: 3px;
            font-weight: bold;
            text-align: center;
        }

        .jam-container {
            margin-bottom: 2px;
        }

        /* Holiday styling */
        .libur-nasional {
            background-color:#fff5f5 !important;
        }
        .minggu {
            background-color:#fff5f5 !important;
        }
        
        /* Date label for holidays */
        .date-label {
            position: absolute;
            top: 2px;
            right: 2px;
            font-weight: bold;
        }
        
        .holiday-name {
            font-size: 10px;
            color: #000;
            margin-top: 5px;
            font-style: italic;
            position: absolute;
            bottom: 5px;
            left: 5px;
            right: 5px;
            text-align: center;
            width: 95%;
        }
        
        /* Select2 styling */
        .select2-container {
            width: 300px !important;
        }
        .select2-selection {
            height: 38px !important;
            padding-top: 4px !important;
        }
        
        /* Holiday list styling */
        .holiday-list {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .holiday-list h5 {
            margin-bottom: 15px;
            color: #333;
        }
        .holiday-item {
            display: flex;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }

        .holiday-date {
            font-size: 10pt;
            font-weight: bold;
            width: 100px;
        }

        .holiday-event {
            flex-grow: 1;
            font-size: 10pt;
            color: #e60000;
            font-style: italic;
            padding: 2px;
            border-radius: 3px;
        }

        .summary-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .summary-box {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .summary-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .summary-value {
            font-size: 14pt;
            font-weight: bold;
            color: #0066cc;
        }

        .summary-terlambat {
            color: #e60000;
        }

        .summary-lembur {
            color: #009900;
        }

        .summary-cuti {
            color: #ff9900;
        }

        .summary-tugas {
            color: #9900cc;
        }
    </style>

<form method="post" action="<?= base_url('admin/kalenderpegawai/lihat') ?>" class="mb-4 d-flex gap-2 align-items-center">
    <select name="pegawai_pin" class="form-select select2" required>
        <option value="">Pilih Pegawai</option>
        <?php foreach ($pegawai as $p): ?>
            <option value="<?= htmlspecialchars($p['pegawai_pin']) ?>" <?= ($pegawaiTerpilih && $pegawaiTerpilih['pegawai_pin'] == $p['pegawai_pin']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['pegawai_nama']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" class="form-control" style="width: 200px;" required>

    <button type="submit" class="btn btn-primary">Tampilkan</button>
</form>

<?php if (!empty($dataKalender)): ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Senin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Kamis</th>
                    <th>Jumat</th>
                    <th>Sabtu</th>
                    <th>Minggu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minggu as $mingguKe): ?>
                    <tr>
                        <?php foreach ($mingguKe as $tgl): 
                            $data = $dataKalender[$tgl] ?? null;
                            $isHoliday = isHoliday($tgl, $liburNasional);
                            $dayOfWeek = date('N', strtotime($tgl));
                            $cellClass = $isHoliday ? 'libur-nasional' : '';
                            $cellClass .= $dayOfWeek == 7 ? ' minggu' : '';
                            
                            $holidayName = $liburNasional[$tgl] ?? '';
                            
                            $shiftClass = '';
                            $lateSeconds = 0;
                            $showAttendance = false;
                            
                            if (!empty($data['shift'])) {
                                switch(strtolower($data['shift'])) {
                                    case 'office': $shiftClass = 'shift-office'; break;
                                    case 'office 1': $shiftClass = 'shift-office1'; break;
                                    case 'office 2': $shiftClass = 'shift-office2'; break;
                                    case 'pagi bangsal': $shiftClass = 'shift-pagibangsal'; break;
                                    case 'pagi': $shiftClass = 'shift-pagi'; break;
                                    case 'siang': $shiftClass = 'shift-siang'; break;
                                    case 'malam': $shiftClass = 'shift-malam'; break;
                                    case 'midle': $shiftClass = 'shift-midle'; break;
                                }
                                
                                $data['shift'] = ucfirst(strtolower($data['shift']));
                            }
                            
                            $showAttendance = 
                                !empty($data['shift']) || 
                                !empty($data['status_khusus']) || 
                                !empty($data['jam_masuk']) || 
                                !empty($data['jam_pulang']) || 
                                !empty($data['lembur_masuk']) || 
                                !empty($data['lembur_pulang']) || 
                                !empty($data['alasan_lembur']);
                            
                            // Calculate lateness only if no special status
                            if (empty($data['status_khusus']) && !empty($data['jam_masuk']) && !empty($data['jam_masuk_shift'])) {
                                $jamMasuk = str_replace(['<span style="color:red;">', '</span>'], '', $data['jam_masuk']);
                                $jamMasukShift = $data['jam_masuk_shift'];
                                
                                $masukTime = strtotime($jamMasuk);
                                $shiftTime = strtotime($jamMasukShift);
                                
                                if ($masukTime > $shiftTime) {
                                    $lateSeconds = $masukTime - $shiftTime;
                                }
                            }
                        ?>
                        <td class="kalender-cell <?= $cellClass ?>" <?= $holidayName ? 'title="'.htmlspecialchars($holidayName).'"' : '' ?>>
                            <div class="date-label"><?= date('j', strtotime($tgl)) ?></div><br>
                            
                            <?php if ($data && $showAttendance): ?>
                                <!-- Shift Information -->
                                <?php if (!empty($data['shift']) || !empty($data['jam_masuk']) || !empty($data['jam_pulang'])): ?>
                                    <div class="shift-box <?= $shiftClass ?>"><?= htmlspecialchars($data['shift'] ?: '-') ?></div>
                                <?php endif; ?>
                                
                                <!-- Jam Masuk/Pulang -->
                                <div class="jam-container">
                                    <span class="jam-label">&bull; IN:</span> 
                                    <?= !empty($data['jam_masuk']) ? 
                                        ($lateSeconds > 0 ? '<span class="jam-masuk-late">'.htmlspecialchars(strip_tags($data['jam_masuk'])).'</span>' : htmlspecialchars(strip_tags($data['jam_masuk']))) 
                                        : '-' ?>
                                </div>
                                <div class="jam-container">
                                    <span class="jam-label">&bull; OUT:</span> 
                                    <?= !empty($data['jam_pulang']) ? htmlspecialchars($data['jam_pulang']) : '-' ?>
                                </div>
                                
                                <!-- Late Information - Only show if no special status -->
                                <?php if ($lateSeconds > 0 && empty($data['status_khusus'])): ?>
                                    <div class="terlambat">Terlambat: <?= secondsToTime($lateSeconds) ?></div><br>
                                <?php endif; ?>
                                
                                <!-- Lembur Section -->
                                <?php if (!empty($data['alasan_lembur'])): ?>
                                    <?php
                                        $lemburDuration = 0;
                                        if (!empty($data['lembur_masuk']) && !empty($data['lembur_pulang'])) {
                                            // Tanggal hari ini (asumsi lembur dimulai hari ini)
                                            $today = date('Y-m-d');
                                            
                                            // Jika jam pulang lebih kecil dari jam masuk (melewati tengah malam)
                                            if (strtotime($data['lembur_pulang']) < strtotime($data['lembur_masuk'])) {
                                                // Tambahkan 1 hari ke tanggal pulang
                                                $lemburMasuk = strtotime($today . ' ' . $data['lembur_masuk']);
                                                $lemburPulang = strtotime(date('Y-m-d', strtotime($today . ' +1 day')) . ' ' . $data['lembur_pulang']);
                                            } else {
                                                // Normal case (tidak melewati tengah malam)
                                                $lemburMasuk = strtotime($today . ' ' . $data['lembur_masuk']);
                                                $lemburPulang = strtotime($today . ' ' . $data['lembur_pulang']);
                                            }
                                            
                                            if ($lemburPulang > $lemburMasuk) {
                                                $lemburDuration = $lemburPulang - $lemburMasuk;
                                            }
                                        }
                                    ?>
                                    <div class="lembur-section">
                                        <div class="lembur-title">Lembur: <?= $lemburDuration > 0 ? secondsToTime($lemburDuration) : '0:00:00' ?></div>
                                        <div class="jam-container">
                                            <span class="jam-label">&bull; Lembur In:</span> 
                                            <?= !empty($data['lembur_masuk']) ? htmlspecialchars($data['lembur_masuk']) : '-' ?>
                                        </div>
                                        <div class="jam-container">
                                            <span class="jam-label">&bull; Lembur Out:</span> 
                                            <?= !empty($data['lembur_pulang']) ? htmlspecialchars($data['lembur_pulang']) : '-' ?>
                                        </div>
                                        <?php if (!empty($data['alasan_lembur'])): ?>
                                            <div class="lembur-alasan"><?= htmlspecialchars($data['alasan_lembur']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Khusus -->
                                <?php if (!empty($data['status_khusus'])): ?>
                                    <div class="status-khusus"><?= nl2br(strip_tags($data['status_khusus'], '<br>')) ?></div>

                                <?php endif; ?>
                            <?php endif; ?>
                            <br>
                            <?php if ($holidayName): ?>
                                <div class="holiday-name"><?= htmlspecialchars($holidayName) ?></div>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Boxes -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">Total Hari Kerja</div>
            <div class="summary-value"><?= $totalWorkDays ?> hari</div>
        </div>
        
        <?php if ($totalTerlambatSeconds > 0): ?>
        <div class="summary-box">
            <div class="summary-title">Total Keterlambatan</div>
            <div class="summary-value summary-terlambat"><?= secondsToTime($totalTerlambatSeconds) ?></div>
        </div>
        <?php endif; ?>
        
        <?php if ($totalLemburSeconds > 0): ?>
        <div class="summary-box">
            <div class="summary-title">Total Lembur</div>
            <div class="summary-value summary-lembur"><?= secondsToTime($totalLemburSeconds) ?></div>
        </div>
        <?php endif; ?>
        
        <?php if ($totalCuti > 0): ?>
        <div class="summary-box">
            <div class="summary-title">Total Cuti</div>
            <div class="summary-value summary-cuti"><?= $totalCuti ?> hari</div>
        </div>
        <?php endif; ?>
        
        <?php if ($totalTugasLuar > 0): ?>
        <div class="summary-box">
            <div class="summary-title">Total Tugas Luar</div>
            <div class="summary-value summary-tugas"><?= $totalTugasLuar ?> hari</div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Daftar Hari Libur -->
    <div class="holiday-list">
        <h5>Daftar Hari Libur Bulan <?= date('F Y', strtotime($bulan.'-01')) ?></h5>
        
        <?php if (!empty($liburBulanIni)): ?>
            <?php foreach ($liburBulanIni as $date => $event): ?>
                <div class="holiday-item">
                    <div class="holiday-date"><?= date('d F Y', strtotime($date)) ?></div>
                    <div class="holiday-event"><?= htmlspecialchars($event) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada hari libur nasional pada bulan ini.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih Pegawai",
            allowClear: true,
            width: '100%'
        });
    });
</script>