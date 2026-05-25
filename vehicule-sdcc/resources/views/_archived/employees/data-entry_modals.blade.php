<!-- ARCHIVED: admin/data-entry/modals.blade.php (original content) -->
<!-- Original content archived for employees modals. Restore to resources/views/admin/data-entry/modals.blade.php to re-enable. -->

/* Content archived from original file. */

<!-- ======================= DATA ENTRY MODALS (ARCHIVE) ======================= -->
<style>
	/* Modal Overlay */
	.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index:1000; animation: fadeIn 0.3s ease; }
	.modal-overlay.active { display:flex; align-items:center; justify-content:center; }
	@keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
	.modal-container { background: white; border-radius: 12px; max-width: 500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: slideUp 0.3s ease; }
	@keyframes slideUp { from { transform: translateY(50px); opacity:0 } to { transform: translateY(0); opacity:1 } }
	.modal-header { background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%); color:white; padding:20px; border-radius:12px 12px 0 0; display:flex; justify-content:space-between; align-items:center; }
	.modal-header h2 { margin:0; font-size:18px; font-weight:600 }
	.modal-close { background:none; border:none; color:white; font-size:24px; cursor:pointer; padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:50%; transition:background 0.3s }
	.modal-close:hover { background: rgba(255,255,255,0.2); }
	.modal-body { padding:25px }
	.modal-body .form-group { margin-bottom:18px }
	.modal-body label { display:block; margin-bottom:8px; font-weight:600; color:#333; font-size:13px }
	.modal-body input, .modal-body select, .modal-body textarea { width:100%; padding:10px 12px; border:1.5px solid #e0e0e0; border-radius:6px; font-family:inherit; font-size:13px; transition:border-color 0.3s }
	.modal-body input:focus, .modal-body select:focus, .modal-body textarea:focus { outline:none; border-color:#4CAF50; box-shadow:0 0 0 3px rgba(76,175,80,0.1) }
	.modal-body textarea { resize:vertical; min-height:80px }
	.modal-footer { padding:15px 25px 25px; display:flex; gap:10px; justify-content:flex-end }
	.btn-cancel, .btn-submit { padding:10px 20px; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.3s }
	.btn-cancel { background:#f0f0f0; color:#666 }
	.btn-cancel:hover { background:#e0e0e0 }
	.btn-submit { background: linear-gradient(135deg,#4CAF50,#66BB6A); color:white; flex:1 }
	.btn-submit:hover { transform: translateY(-2px); box-shadow:0 4px 12px rgba(76,175,80,0.3) }
	.btn-submit:disabled { opacity:0.6; cursor:not-allowed }
	.form-row { display:grid; grid-template-columns:1fr 1fr; gap:15px }
	.form-row.full { grid-template-columns:1fr }
	.error-message { color:#d32f2f; font-size:12px; margin-top:5px }
	.success-message { background:#c8e6c9; color:#2e7d32; padding:12px; border-radius:6px; margin-bottom:15px; font-size:12px }
	.spinner { display:inline-block; width:12px; height:12px; border:2px solid rgba(255,255,255,0.3); border-top-color:white; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:8px }
	@keyframes spin { to { transform: rotate(360deg) } }
</style>

<!-- ==================== ADD EMPLOYEE MODAL (ARCHIVED) ==================== -->
<div id="addEmployeeModal" class="modal-overlay">
	<div class="modal-container">
		<div class="modal-header">
			<h2><i class="fas fa-user-plus"></i> Ajouter un Employé</h2>
			<button class="modal-close" onclick="closeModal('addEmployeeModal')">&times;</button>
		</div>
		<div class="modal-body">
			<form id="addEmployeeForm">
				@csrf
				<div id="employeeSuccessMsg" class="success-message" style="display: none;"></div>
                
				<div class="form-row">
					<div class="form-group">
						<label for="employee_name">Nom complet *</label>
						<input type="text" id="employee_name" name="name" required placeholder="Ex: Ali Bennani">
						<div class="error-message" id="employee_name_error"></div>
					</div>
					<div class="form-group">
						<label for="employee_email">Email *</label>
						<input type="email" id="employee_email" name="email" required placeholder="Ex: ali@sdcc.ma">
						<div class="error-message" id="employee_email_error"></div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="employee_service">Service *</label>
						<input type="text" id="employee_service" name="service" required placeholder="Ex: Commerciale">
						<div class="error-message" id="employee_service_error"></div>
					</div>
					<div class="form-group">
						<label for="employee_role">Rôle *</label>
						<select id="employee_role" name="role" required>
							<option value="">-- Sélectionner --</option>
							<option value="employee">Employé</option>
							<option value="admin">Administrateur</option>
						</select>
						<div class="error-message" id="employee_role_error"></div>
					</div>
				</div>

				<div class="form-group form-row full">
					<label for="employee_password">Mot de passe *</label>
					<input type="password" id="employee_password" name="password" required placeholder="Minimum 6 caractères">
					<div class="error-message" id="employee_password_error"></div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn-cancel" onclick="closeModal('addEmployeeModal')">Annuler</button>
			<button class="btn-submit" id="submitEmployeeBtn" onclick="submitEmployeeForm()">
				<span class="spinner" style="display: none;"></span>
				<span class="submit-text">Ajouter</span>
			</button>
		</div>
	</div>
</div>

<!-- Note: vehicle/kilometrage modals remain in original modals file (only employee modal archived here) -->

