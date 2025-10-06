<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Grim\Utils\ResultLogger;
$logger = new ResultLogger();
$results = $logger->listResults();
function getResultDetail($id) {
        $logger = new Grim\Utils\ResultLogger();
        $result = $logger->getResult($id);
        if (!$result) return '<div class="alert alert-danger">Result not found.</div>';
        $json = json_encode(json_decode($result['result_json']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return '<pre style="max-height:400px;overflow:auto;">' . htmlspecialchars($json) . '</pre>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GRIM Web Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
                body { background: #f8f9fa; }
                .navbar-brand { font-weight: bold; letter-spacing: 2px; }
                .table-hover tbody tr:hover { background: #e9ecef; cursor: pointer; }
                .modal-lg { max-width: 80vw; }
        </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GRIM Dashboard</a>
        <span class="navbar-text text-light">Advanced Security Scanner</span>
    </div>
</nav>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">Scan History</h2>
            <?php if (empty($results)): ?>
                <div class="alert alert-warning">No scan results found.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="resultsTable">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Target</th><th>Time</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr data-id="<?= htmlspecialchars($row['id']) ?>">
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['target']) ?></td>
                            <td><?= htmlspecialchars($row['scan_time']) ?></td>
                            <td><button class="btn btn-sm btn-primary view-detail" data-id="<?= htmlspecialchars($row['id']) ?>">View</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p class="text-muted">Tip: Use the CLI for advanced scans, exports, and scheduling. This dashboard will be expanded with more features soon.</p>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Scan Result Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resultDetail">
                <!-- Details loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-detail').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            fetch('result_detail.php?id=' + encodeURIComponent(id))
                .then(r => r.text())
                .then(html => {
                    document.getElementById('resultDetail').innerHTML = html;
                    var modal = new bootstrap.Modal(document.getElementById('resultModal'));
                    modal.show();
                });
        });
    });
});
</script>
</body>
</html>
