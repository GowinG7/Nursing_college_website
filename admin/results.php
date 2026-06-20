<?php
require_once __DIR__ . '/../includes/db.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* ================================
   DEBUG MODE (TURN OFF IN PROD)
================================ */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ================================
   EXPORT TEMPLATE
================================ */
if (isset($_GET['template'])) {

    $bid = (int) $_GET['template'];

    $subs = $conn->query("
        SELECT * FROM result_subjects 
        WHERE batch_id=$bid 
        ORDER BY sort_order
    ");

    if (!$subs) {
        die("Subject fetch failed: " . $conn->error);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'symbol_no',
        'roll_no',
        'student_name',
        'dob',
        'father_name',
        'mother_name'
    ];

    while ($r = $subs->fetch_assoc()) {
        $label = ($r['subject_code'] ? $r['subject_code'] . ' - ' : '') . $r['subject_name'];
        $headers[] = $label . ' TH';
        $headers[] = $label . ' PR';
    }

    $sheet->fromArray($headers, null, 'A1');

    $sheet->fromArray(
        ['BPK2025001', '01', 'Sample Student', '2003-01-01', 'Father', 'Mother'],
        null,
        'A2'
    );

    if (ob_get_length())
        ob_end_clean();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="results_template_batch_' . $bid . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

/* ================================
   PAGE INIT
================================ */
$page_title = "Results";
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

/* ================================
   GRADE FUNCTION
================================ */
function gradeFor($pct)
{
    if ($pct >= 90)
        return ['A+', 4.0];
    if ($pct >= 80)
        return ['A', 3.6];
    if ($pct >= 70)
        return ['B+', 3.2];
    if ($pct >= 60)
        return ['B', 2.8];
    if ($pct >= 50)
        return ['C+', 2.4];
    if ($pct >= 40)
        return ['C', 2.0];
    return ['F', 0.0];
}

/* ================================
   CREATE BATCH
================================ */
if (($_POST['action'] ?? '') === 'create_batch') {

    $program = trim($_POST['program']);
    $batch = trim($_POST['batch_year']);
    $sem = trim($_POST['semester']);
    $ey = trim($_POST['exam_year']);
    $ename = trim($_POST['exam_name'] ?: 'Final');

    $codes = $_POST['code'] ?? [];
    $names = $_POST['sname'] ?? [];
    $credits = $_POST['credit'] ?? [];
    $fulls = $_POST['full'] ?? [];
    $passes = $_POST['pass'] ?? [];

    $stmt = $conn->prepare("
        INSERT INTO result_batches(program,batch_year,semester,exam_year,exam_name)
        VALUES(?,?,?,?,?)
    ");

    $stmt->bind_param('sssss', $program, $batch, $sem, $ey, $ename);
    $stmt->execute();

    $batch_id = $conn->insert_id;

    $subStmt = $conn->prepare("
        INSERT INTO result_subjects
        (batch_id,sort_order,subject_code,subject_name,credit,full_marks,pass_marks)
        VALUES(?,?,?,?,?,?,?)
    ");

    foreach ($names as $i => $sn) {

        $sn = trim($sn);
        if ($sn === '')
            continue;

        $code = trim($codes[$i] ?? '');
        $cr = (float) ($credits[$i] ?? 0);
        $fm = (int) ($fulls[$i] ?? 100);
        $pm = (int) ($passes[$i] ?? 40);

        $subStmt->bind_param('iissdii', $batch_id, $i, $code, $sn, $cr, $fm, $pm);
        $subStmt->execute();
    }

    header("Location: results.php?batch=$batch_id&created=1");
    exit;
}

/* ================================
   UPLOAD EXCEL (FULL FIXED)
================================ */
if (($_POST['action'] ?? '') === 'upload_excel') {

    $bid = (int) $_POST['batch_id'];

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
        $err = "Upload failed: file missing.";
    } else {

        try {

            // FETCH SUBJECTS
            $subs = [];
            $q = $conn->query("
                SELECT * FROM result_subjects 
                WHERE batch_id=$bid 
                ORDER BY sort_order
            ");

            if (!$q) {
                throw new Exception("DB error: " . $conn->error);
            }

            while ($r = $q->fetch_assoc()) {
                $subs[] = $r;
            }

            if (count($subs) === 0) {
                throw new Exception("No subjects found for this batch.");
            }

            $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            if (count($rows) < 2) {
                throw new Exception("Excel file is empty.");
            }

            array_shift($rows); // remove header

            $conn->begin_transaction();

            $imported = 0;
            $skipped = 0;

            foreach ($rows as $row) {

                if (empty($row[0]))
                    continue;

                $symbol = trim($row[0]);
                $roll = trim($row[1] ?? '');
                $name = trim($row[2] ?? '');

                if ($name === '') {
                    $skipped++;
                    continue;
                }

                // SAFE DATE
                $dob = null;
                if (!empty($row[3])) {
                    $ts = strtotime($row[3]);
                    if ($ts)
                        $dob = date('Y-m-d', $ts);
                }

                $father = trim($row[4] ?? '');
                $mother = trim($row[5] ?? '');

                $ins = $conn->prepare("
                    INSERT INTO result_students
                    (batch_id,symbol_no,roll_no,student_name,dob,father_name,mother_name)
                    VALUES(?,?,?,?,?,?,?)
                ");

                $ins->bind_param('issssss', $bid, $symbol, $roll, $name, $dob, $father, $mother);
                $ins->execute();

                $sid = $conn->insert_id;

                $col = 6;
                $totFull = 0;
                $totObt = 0;
                $gpaSum = 0;
                $crSum = 0;
                $allPass = true;

                foreach ($subs as $sub) {

                    $th = (float) ($row[$col] ?? 0);
                    $pr = (float) ($row[$col + 1] ?? 0);
                    $col += 2;

                    $total = $th + $pr;

                    $pct = ($sub['full_marks'] > 0)
                        ? ($total / $sub['full_marks']) * 100
                        : 0;

                    [$grade, $gp] = gradeFor($pct);

                    $isPass = ($total >= $sub['pass_marks']) ? 1 : 0;
                    if (!$isPass)
                        $allPass = false;

                    $mk = $conn->prepare("
                        INSERT INTO result_marks
                        (student_id,subject_id,theory,practical,total,grade,gpa,is_pass)
                        VALUES(?,?,?,?,?,?,?,?)
                    ");

                    $mk->bind_param(
                        'iiddssdi',
                        $sid,
                        $sub['id'],
                        $th,
                        $pr,
                        $total,
                        $grade,
                        $gp,
                        $isPass
                    );

                    if (!$mk->execute()) {
                        throw new Exception("Mark insert failed: " . $mk->error);
                    }

                    $totFull += $sub['full_marks'];
                    $totObt += $total;
                    $gpaSum += $gp * (float) $sub['credit'];
                    $crSum += (float) $sub['credit'];
                }

                $pct = $totFull > 0 ? ($totObt / $totFull) * 100 : 0;
                [$finalGrade] = gradeFor($pct);

                $finalGpa = $crSum > 0 ? round($gpaSum / $crSum, 2) : 0;
                $status = $allPass ? 'Pass' : 'Fail';

                $upd = $conn->prepare("
                    UPDATE result_students
                    SET total_full=?, total_obtained=?, percentage=?, gpa=?, grade=?, status=?
                    WHERE id=?
                ");

                $upd->bind_param(
                    'idddssi',
                    $totFull,
                    $totObt,
                    $pct,
                    $finalGpa,
                    $finalGrade,
                    $status,
                    $sid
                );

                $upd->execute();

                $imported++;
            }

            $conn->commit();
            $msg = "Imported $imported students. Skipped $skipped.";

        } catch (Throwable $e) {
            $conn->rollback();
            $err = "IMPORT ERROR: " . $e->getMessage();
        }
    }
}

/* ================================
   DELETE BATCH
================================ */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM result_batches WHERE id=$id");

    header("Location: results.php?deleted=1");
    exit;
}

/* ================================
   FETCH BATCHES
================================ */
$batches = $conn->query("
    SELECT b.*,
    (SELECT COUNT(*) FROM result_students s WHERE s.batch_id=b.id) AS students
    FROM result_batches b
    ORDER BY b.published_at DESC
");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Result Batches</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newBatch">
        New Batch
    </button>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if ($err): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($err) ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Batch</th>
                    <th>Semester</th>
                    <th>Exam</th>
                    <th>Students</th>
                    <th>Published</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($b = $batches->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($b['program']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b['batch_year']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b['semester']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b['exam_name'] . ' ' . $b['exam_year']) ?>
                        </td>
                        <td>
                            <?= (int) $b['students'] ?>
                        </td>
                        <td>
                            <?= !empty($b['published_at']) ? date('M d, Y', strtotime($b['published_at'])) : '-' ?>
                        </td>

                        <td class="text-end">

                            <!-- TEMPLATE -->
                            <a class="btn btn-sm btn-outline-success" href="?template=<?= (int) $b['id'] ?>">
                                Template
                            </a>

                            <!-- UPLOAD BUTTON -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#uploadModal<?= (int) $b['id'] ?>">
                                Upload
                            </button>

                            <!-- DELETE -->
                            <a class="btn btn-sm btn-outline-danger" href="?delete=<?= (int) $b['id'] ?>"
                                onclick="return confirm('Delete this batch permanently?')">
                                Delete
                            </a>

                        </td>
                    </tr>

                    <!-- ================= UPLOAD MODAL ================= -->
                    <div class="modal fade" id="uploadModal<?= (int) $b['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <form method="POST" enctype="multipart/form-data">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Excel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" name="action" value="upload_excel">
                                        <input type="hidden" name="batch_id" value="<?= (int) $b['id'] ?>">

                                        <label class="form-label">Select Excel File</label>
                                        <input type="file" name="file" class="form-control" required>

                                        <small class="text-muted d-block mt-2">
                                            Must match template format.
                                        </small>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            Upload
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>