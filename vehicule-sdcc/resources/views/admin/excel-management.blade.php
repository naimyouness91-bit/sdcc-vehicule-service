@extends('layouts.app')

@section('title', 'Excel Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Excel File Management</h1>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" id="downloadBtn">
                        <i class="fas fa-download me-2"></i>Download Excel
                    </button>
                    <button type="button" class="btn btn-success" id="uploadBtn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload me-2"></i>Upload Changes
                    </button>
                </div>
            </div>

            <!-- Lock Status Alert -->
            <div id="lockStatus" class="alert alert-info d-none" role="alert">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-lock me-2"></i>
                        <span id="lockMessage"></span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="extendLockBtn">Extend Lock</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="releaseLockBtn">Release Lock</button>
                    </div>
                </div>
            </div>

            <!-- File Info Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="totalRequests">-</h4>
                                    <p class="mb-0">Total Requests</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="fileSize">-</h4>
                                    <p class="mb-0">File Size</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-excel fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="lastModified">-</h4>
                                    <p class="mb-0">Last Modified</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="backupCount">-</h4>
                                    <p class="mb-0">Backups Available</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-save fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Request Status Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div id="statusChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Backups</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="backupsTable">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Date</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Backups will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">How to Edit Excel File Safely</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-download text-primary me-2"></i>Step 1: Download</h5>
                            <p>Click the "Download Excel" button to get the current version of the Excel file.</p>
                            
                            <h5><i class="fas fa-lock text-warning me-2"></i>Step 2: Acquire Lock</h5>
                            <p>The system will automatically acquire a lock to prevent other users from making simultaneous changes.</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-edit text-success me-2"></i>Step 3: Edit Locally</h5>
                            <p>Edit the downloaded Excel file using Excel or compatible software. Maintain the original structure and column order.</p>
                            
                            <h5><i class="fas fa-upload text-info me-2"></i>Step 4: Upload Changes</h5>
                            <p>Upload your modified file. The system will validate it and create a backup before applying changes.</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Always maintain the original column structure and order. Do not add or remove columns, and ensure all required fields are filled.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Excel Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xlsx,.xls" required>
                        <div class="form-text">Only .xlsx and .xls files are accepted. Maximum file size: 10MB.</div>
                    </div>
                    
                    <div id="validationResults" class="d-none">
                        <h6>Validation Results:</h6>
                        <div id="validationErrors" class="alert alert-danger d-none"></div>
                        <div id="validationWarnings" class="alert alert-warning d-none"></div>
                        <div id="validationSuccess" class="alert alert-success d-none"></div>
                        
                        <div id="dataPreview" class="mt-3 d-none">
                            <h6>Data Preview (First 5 rows):</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="previewTable">
                                    <thead>
                                        <tr id="previewHeader"></tr>
                                    </thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="validateBtn">Validate</button>
                <button type="button" class="btn btn-success d-none" id="applyChangesBtn">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Backup Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restoreModalLabel">Restore Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this backup?</p>
                <p><strong>Backup:</strong> <span id="restoreFilename"></span></p>
                <p class="text-warning">A backup of the current file will be created before restoring.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn">Restore Backup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-approved { background-color: #00B050; color: white; }
    .status-pending { background-color: #FFC000; color: black; }
    .status-rejected { background-color: #FF0000; color: white; }
    .status-cancelled { background-color: #FF0000; color: white; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentLockInfo = null;
let uploadData = null;

$(document).ready(function() {
    loadFileInfo();
    loadLockStatus();
    loadBackups();
    
    // Auto-refresh lock status every 30 seconds
    setInterval(loadLockStatus, 30000);
    
    // Download button
    $('#downloadBtn').click(function() {
        window.location.href = '/excel/download';
    });
    
    // Upload button handlers
    $('#validateBtn').click(validateUpload);
    $('#applyChangesBtn').click(applyChanges);
    
    // Lock management
    $('#extendLockBtn').click(extendLock);
    $('#releaseLockBtn').click(releaseLock);
});

function loadFileInfo() {
    $.get('/api/excel/info')
        .done(function(data) {
            $('#fileSize').text(formatBytes(data.size));
            $('#lastModified').text(new Date(data.modified_at * 1000).toLocaleString());
            
            if (data.exists && data.is_valid) {
                loadDataStats();
            }
        })
        .fail(function() {
            showError('Failed to load file information');
        });
}

function loadDataStats() {
    $.get('/api/excel/stats')
        .done(function(data) {
            $('#totalRequests').text(data.total_requests);
            
            // Create status chart
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.by_status),
                    datasets: [{
                        data: Object.values(data.by_status),
                        backgroundColor: ['#00B050', '#FFC000', '#FF0000', '#FF6B6B']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        })
        .fail(function() {
            showError('Failed to load data statistics');
        });
}

function loadLockStatus() {
    $.get('/excel/lock/status')
        .done(function(data) {
            if (data.is_locked) {
                showLockStatus(data.lock_info);
            } else {
                hideLockStatus();
            }
        })
        .fail(function() {
            showError('Failed to check lock status');
        });
}

function showLockStatus(lockInfo) {
    currentLockInfo = lockInfo;
    const lockStatus = $('#lockStatus');
    const lockMessage = $('#lockMessage');
    
    lockStatus.removeClass('d-none alert-info alert-warning');
    
    if (lockInfo.user_id === '{{ Auth::id() }}') {
        lockStatus.addClass('alert-info');
        lockMessage.text(`You have the file locked for: ${lockInfo.reason} (expires in ${Math.floor(lockInfo.time_remaining / 60)} minutes)`);
        $('#extendLockBtn, #releaseLockBtn').show();
    } else {
        lockStatus.addClass('alert-warning');
        lockMessage.text(`File is locked by user ${lockInfo.user_id} for: ${lockInfo.reason}`);
        $('#extendLockBtn, #releaseLockBtn').hide();
    }
}

function hideLockStatus() {
    $('#lockStatus').addClass('d-none');
    currentLockInfo = null;
}

function acquireLock() {
    $.post('/excel/lock/acquire', { reason: 'Excel editing' })
        .done(function(data) {
            if (data.success) {
                showSuccess('Lock acquired successfully');
                loadLockStatus();
            } else {
                showError(data.message);
            }
        })
        .fail(function() {
            showError('Failed to acquire lock');
        });
}

function extendLock() {
    $.post('/excel/lock/extend')
        .done(function(data) {
            if (data.success) {
                showSuccess('Lock extended successfully');
                loadLockStatus();
            } else {
                showError(data.message);
            }
        })
        .fail(function() {
            showError('Failed to extend lock');
        });
}

function releaseLock() {
    $.post('/excel/lock/release')
        .done(function(data) {
            if (data.success) {
                showSuccess('Lock released successfully');
                hideLockStatus();
            } else {
                showError(data.message);
            }
        })
        .fail(function() {
            showError('Failed to release lock');
        });
}

function validateUpload() {
    const formData = new FormData($('#uploadForm')[0]);
    
    $.ajax({
        url: '/excel/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                uploadData = data.data;
                showValidationResults(data);
                $('#applyChangesBtn').removeClass('d-none');
            } else {
                showValidationErrors(data);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showValidationErrors(response || { message: 'Upload failed' });
        }
    });
}

function showValidationResults(data) {
    const results = $('#validationResults');
    const errors = $('#validationErrors');
    const warnings = $('#validationWarnings');
    const success = $('#validationSuccess');
    const preview = $('#dataPreview');
    
    results.removeClass('d-none');
    errors.addClass('d-none');
    warnings.addClass('d-none');
    success.addClass('d-none');
    preview.addClass('d-none');
    
    if (data.message) {
        success.removeClass('d-none').text(data.message);
    }
    
    if (data.warnings && data.warnings.length > 0) {
        warnings.removeClass('d-none').html('<strong>Warnings:</strong><ul>' + 
            data.warnings.map(w => `<li>${w}</li>`).join('') + '</ul>');
    }
    
    if (data.data && data.data.preview) {
        showDataPreview(data.data.preview);
    }
}

function showValidationErrors(data) {
    const results = $('#validationResults');
    const errors = $('#validationErrors');
    
    results.removeClass('d-none');
    errors.addClass('d-none');
    
    const errorList = data.errors || [data.message];
    errors.removeClass('d-none').html('<strong>Errors:</strong><ul>' + 
        errorList.map(e => `<li>${e}</li>`).join('') + '</ul>');
    
    $('#applyChangesBtn').addClass('d-none');
}

function showDataPreview(preview) {
    const previewDiv = $('#dataPreview');
    const headerRow = $('#previewHeader');
    const tbody = $('#previewBody');
    
    if (preview.length > 0) {
        // Create headers
        const headers = Object.keys(preview[0]);
        headerRow.html(headers.map(h => `<th>${h}</th>`).join(''));
        
        // Create rows
        tbody.html(preview.map(row => 
            '<tr>' + headers.map(h => `<td>${row[h] || ''}</td>`).join('') + '</tr>'
        ).join(''));
        
        previewDiv.removeClass('d-none');
    }
}

function applyChanges() {
    if (!uploadData) {
        showError('No data to apply');
        return;
    }
    
    $.post('/excel/apply-changes', { data: uploadData })
        .done(function(data) {
            if (data.success) {
                showSuccess('Changes applied successfully! Backup created: ' + data.backup_filename);
                $('#uploadModal').modal('hide');
                loadFileInfo();
                loadBackups();
                releaseLock();
            } else {
                showError(data.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showError(response?.message || 'Failed to apply changes');
        });
}

function loadBackups() {
    $.get('/excel/backups')
        .done(function(data) {
            if (data.success) {
                displayBackups(data.backups);
                $('#backupCount').text(data.backups.length);
            }
        })
        .fail(function() {
            showError('Failed to load backups');
        });
}

function displayBackups(backups) {
    const tbody = $('#backupsTable tbody');
    tbody.empty();
    
    backups.forEach(function(backup) {
        const row = `
            <tr>
                <td>${backup.filename}</td>
                <td>${backup.timestamp}</td>
                <td>${formatBytes(backup.size)}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="restoreBackup('${backup.filename}')">
                        <i class="fas fa-undo"></i> Restore
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function restoreBackup(filename) {
    $('#restoreFilename').text(filename);
    $('#restoreModal').modal('show');
}

$('#confirmRestoreBtn').click(function() {
    const filename = $('#restoreFilename').text();
    
    $.post('/excel/backups/restore', { backup_filename: filename })
        .done(function(data) {
            if (data.success) {
                showSuccess('Backup restored successfully');
                $('#restoreModal').modal('hide');
                loadFileInfo();
                loadBackups();
            } else {
                showError(data.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showError(response?.message || 'Failed to restore backup');
        });
});

// Utility functions
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showSuccess(message) {
    // You can implement a toast notification here
    alert('Success: ' + message);
}

function showError(message) {
    // You can implement a toast notification here
    alert('Error: ' + message);
}
</script>
@endpush
