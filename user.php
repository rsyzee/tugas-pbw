<?php

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
$messageType = '';
$redirect = '';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

ini_set('display_errors', 0);

if (!isset($_SESSION['username'])) {
	header("location:login.php");
}

include 'upload-handler.php';

if (!isset($uploadFsDir) || !is_string($uploadFsDir) || $uploadFsDir === '') {
	$uploadFsDir = __DIR__ . '/img';
}

$currentUserStmt = $pdo->prepare("SELECT role FROM user WHERE username = :username");
$currentUserStmt->execute([':username' => $_SESSION['username']]);
$currentUserData = $currentUserStmt->fetch();
$currentUserRole = $currentUserData['role'] ?? 'user';

$_SESSION['role'] = $currentUserRole;

function canManageUser($currentRole, $targetRole) {
	if ($currentRole === 'superadmin') {
		return true;
	}

	if ($currentRole === 'admin' && $targetRole === 'user') {
		return true;
	}

	return false;
}

function canAssignRole($currentRole, $roleToAssign) {
	if ($currentRole === 'superadmin') {
		return true;
	}

	if ($currentRole === 'admin' && $roleToAssign === 'user') {
		return true;
	}
	return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$role = $_POST['role'] ?? 'user';
	$foto = '';

	if (!canAssignRole($currentUserRole, $role)) {
		$message = 'Anda tidak memiliki izin untuk membuat user dengan role tersebut!';
		$messageType = 'error';
		$action = 'add';
	} else {
		$checkStmt = $pdo->prepare("SELECT id FROM user WHERE username = :username");
		$checkStmt->execute([':username' => $username]);
		if ($checkStmt->fetch()) {
			$message = 'Username sudah digunakan!';
			$messageType = 'error';
			$action = 'add';
		} else {
			$nama_foto = $_FILES['foto']['name'] ?? '';
			if ($nama_foto !== '') {
				$cek_upload = upload_foto($_FILES['foto']);
				if ($cek_upload['status']) {
					$foto = $cek_upload['message'];
				} else {
					$message = $cek_upload['message'];
					$messageType = 'error';
					$action = 'add';
				}
			}

			if (!empty($username) && !empty($password) && $messageType !== 'error') {
				$hashedPassword = hash('sha256', $password);
				$sql = "INSERT INTO user (username, password, foto, role) VALUES (:username, :password, :foto, :role)";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':username' => $username,
					':password' => $hashedPassword,
					':foto' => $foto,
					':role' => $role
				]);
				$redirect = 'admin.php?page=user&msg=added';
			} else {
				if ($messageType !== 'error') {
					$message = 'Username dan Password wajib diisi!';
					$messageType = 'error';
					$action = 'add';
				}
			}
		}
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
	$id = $_POST['id'];
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$foto_lama = $_POST['foto_lama'] ?? '';
	$newRole = $_POST['role'] ?? 'user';

	$targetStmt = $pdo->prepare("SELECT username, role FROM user WHERE id = :id");
	$targetStmt->execute([':id' => $id]);
	$targetUser = $targetStmt->fetch();
	$targetRole = $targetUser['role'] ?? 'user';
	$isOwnAccount = ($targetUser['username'] === $_SESSION['username']);

	if (!$isOwnAccount && !canManageUser($currentUserRole, $targetRole)) {
		$message = 'Anda tidak memiliki izin untuk mengedit user ini!';
		$messageType = 'error';
		$redirect = 'admin.php?page=user&msg=no_permission';
	}

	elseif (!$isOwnAccount && !canAssignRole($currentUserRole, $newRole)) {
		$message = 'Anda tidak memiliki izin untuk menetapkan role tersebut!';
		$messageType = 'error';
		$action = 'edit';
	} else {
		$checkStmt = $pdo->prepare("SELECT id FROM user WHERE username = :username AND id != :id");
		$checkStmt->execute([':username' => $username, ':id' => $id]);
		if ($checkStmt->fetch()) {
			$message = 'Username sudah digunakan oleh user lain!';
			$messageType = 'error';
			$action = 'edit';
		} else {
			$foto = $foto_lama;
			if ($foto === '') {
				$stmt = $pdo->prepare("SELECT foto FROM user WHERE id = :id");
				$stmt->execute([':id' => $id]);
				$current = $stmt->fetch();
				$foto = $current['foto'] ?? '';
			}

			$nama_foto = $_FILES['foto']['name'] ?? '';
			if ($nama_foto !== '') {
				$cek_upload = upload_foto($_FILES['foto']);
				if ($cek_upload['status']) {
					if (!empty($foto)) {
						$oldPath = $uploadFsDir . '/' . $foto;
						if (is_file($oldPath)) {
							@unlink($oldPath);
						}
					}
					$foto = $cek_upload['message'];
				} else {
					$message = $cek_upload['message'];
					$messageType = 'error';
					$action = 'edit';
				}
			}

			if (!empty($username) && $messageType !== 'error') {
				$finalRole = $isOwnAccount ? $targetRole : $newRole;

				if (!empty($password)) {
					$hashedPassword = hash('sha256', $password);
					$sql = "UPDATE user SET username = :username, password = :password, foto = :foto, role = :role WHERE id = :id";
					$stmt = $pdo->prepare($sql);
					$stmt->execute([
						':username' => $username,
						':password' => $hashedPassword,
						':foto' => $foto,
						':role' => $finalRole,
						':id' => $id
					]);
				} else {
					$sql = "UPDATE user SET username = :username, foto = :foto, role = :role WHERE id = :id";
					$stmt = $pdo->prepare($sql);
					$stmt->execute([
						':username' => $username,
						':foto' => $foto,
						':role' => $finalRole,
						':id' => $id
					]);
				}
				$redirect = 'admin.php?page=user&msg=updated';
			} else {
				if ($messageType !== 'error') {
					$message = 'Username wajib diisi!';
					$messageType = 'error';
				}
				$action = 'edit';
			}
		}
	}
}

if ($action === 'delete' && isset($_GET['id'])) {
	$id = $_GET['id'];

	$stmt = $pdo->prepare("SELECT username, foto, role FROM user WHERE id = :id");
	$stmt->execute([':id' => $id]);
	$user = $stmt->fetch();

	if ($user) {

		if ($user['username'] === $_SESSION['username']) {
			$redirect = 'admin.php?page=user&msg=self_delete';
		}

		elseif (!canManageUser($currentUserRole, $user['role'])) {
			$redirect = 'admin.php?page=user&msg=no_permission';
		} else {
			if (!empty($user['foto'])) {
				$imagePath = $uploadFsDir . '/' . $user['foto'];
				if (is_file($imagePath)) {
					@unlink($imagePath);
				}
			}

			$stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
			$stmt->execute([':id' => $id]);
			$redirect = 'admin.php?page=user&msg=deleted';
		}
	} else {
		$redirect = 'admin.php?page=user&msg=deleted';
	}
}

if (isset($_GET['msg'])) {
	switch ($_GET['msg']) {
		case 'added':
			$message = 'User berhasil ditambahkan!';
			$messageType = 'success';
			break;
		case 'updated':
			$message = 'User berhasil diperbarui!';
			$messageType = 'success';
			break;
		case 'deleted':
			$message = 'User berhasil dihapus!';
			$messageType = 'success';
			break;
		case 'self_delete':
			$message = 'Tidak dapat menghapus akun yang sedang login!';
			$messageType = 'error';
			break;
		case 'no_permission':
			$message = 'Anda tidak memiliki izin untuk melakukan aksi ini!';
			$messageType = 'error';
			break;
	}
}

if (!empty($redirect)) {
	echo '<script>window.location.href = "' . $redirect . '";</script>';
	exit();
}
?>

<?php
$editUser = null;
if ($action === 'edit' && isset($_GET['id'])) {
	$id = $_GET['id'];
	$stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
	$stmt->execute([':id' => $id]);
	$editUser = $stmt->fetch();
}
?>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 md:p-8">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
		<div>
			<h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">
				<i class="bi bi-people mr-2"></i>Manage Users
			</h1>
			<p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">Kelola semua akun pengguna sistem</p>
		</div>
		<div class="mt-2 md:mt-0 flex items-center gap-3">
			<?php
			$roleLabels = [
				'superadmin' => ['label' => 'Superadmin', 'bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400', 'icon' => 'bi-shield-fill-check'],
				'admin' => ['label' => 'Admin', 'bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'icon' => 'bi-shield-fill'],
				'user' => ['label' => 'User', 'bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'icon' => 'bi-person-fill']
			];
			$currentRoleInfo = $roleLabels[$currentUserRole] ?? $roleLabels['user'];
			?>
			<span class="hidden sm:inline-flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium <?= $currentRoleInfo['bg'] ?> <?= $currentRoleInfo['text'] ?>">
				<i class="bi <?= $currentRoleInfo['icon'] ?>"></i>
				Login: <?= $_SESSION['username'] ?> (<?= $currentRoleInfo['label'] ?>)
			</span>
			<button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 sm:py-3 sm:px-6 rounded-lg transition-colors inline-flex items-center gap-2 text-sm sm:text-base cursor-pointer">
				<i class="bi bi-plus-circle"></i> Tambah User
			</button>
		</div>
	</div>

	<!-- Role permission info -->
	<?php if ($currentUserRole === 'admin'): ?>
	<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
		<p class="text-blue-700 dark:text-blue-400 text-sm">
			<i class="bi bi-info-circle mr-2"></i>
			Sebagai <strong>Admin</strong>, Anda hanya dapat mengelola user biasa. Untuk mengelola Admin atau Superadmin, hubungi Superadmin.
		</p>
	</div>
	<?php endif; ?>

	<?php if ($message && $messageType === 'success'): ?>
	<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
		<p class="text-green-600 dark:text-green-400">
			<i class="bi bi-check-circle mr-2"></i><?= $message ?>
		</p>
	</div>
	<?php endif; ?>

	<?php if ($message && $messageType === 'error' && $action === 'list'): ?>
	<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
		<p class="text-red-600 dark:text-red-400">
			<i class="bi bi-exclamation-circle mr-2"></i><?= $message ?>
		</p>
	</div>
	<?php endif; ?>

	<div class="table-responsive" id="user_data"></div>
</div>

<!-- Add User Modal -->
<div id="addModal" class="fixed inset-0 z-50 <?= $action === 'add' ? '' : 'hidden' ?>" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
							<i class="bi bi-person-plus mr-2 text-blue-500"></i>Tambah User
						</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat akun pengguna baru</p>
					</div>
					<button type="button" onclick="closeAddModal()" class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
						<i class="bi bi-x-lg text-xl"></i>
					</button>
				</div>

				<!-- Modal Body -->
				<form method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
					<?php if ($message && $messageType === 'error' && $action === 'add'): ?>
					<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
						<p class="text-red-600 dark:text-red-400">
							<i class="bi bi-exclamation-circle mr-2"></i><?= htmlspecialchars($message) ?>
						</p>
					</div>
					<?php endif; ?>

					<div class="space-y-5">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username <span class="text-red-500">*</span></label>
							<input type="text" name="username" required
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Masukkan username...">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password <span class="text-red-500">*</span></label>
							<input type="password" name="password" required
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Masukkan password...">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Profil (Opsional)</label>
							<input type="file" name="foto" accept="image/*"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Format: JPG, JPEG, PNG, GIF, WEBP (Max: 800KB)</p>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role <span class="text-red-500">*</span></label>
							<select name="role" required
									class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base">
								<option value="user">User</option>
								<?php if ($currentUserRole === 'superadmin'): ?>
								<option value="admin">Admin</option>
								<option value="superadmin">Superadmin</option>
								<?php endif; ?>
							</select>
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
								<?php if ($currentUserRole === 'superadmin'): ?>
								Superadmin: Akses penuh. Admin: Hanya kelola user biasa. User: Akses terbatas.
								<?php else: ?>
								Anda hanya dapat membuat user biasa.
								<?php endif; ?>
							</p>
						</div>
					</div>

					<!-- Modal Footer -->
					<div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button type="button" onclick="closeAddModal()"
								class="w-full sm:w-auto px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-x-circle"></i> Batal
						</button>
						<button type="submit" name="add_user"
								class="w-full sm:w-auto px-6 py-3 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-check-circle"></i> Simpan User
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 z-50 <?= ($action === 'edit' && $editUser) ? '' : 'hidden' ?>" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
							<i class="bi bi-pencil-square mr-2 text-yellow-500"></i>Edit User
						</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui data pengguna</p>
					</div>
					<button type="button" onclick="closeEditModal()" class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
						<i class="bi bi-x-lg text-xl"></i>
					</button>
				</div>

				<!-- Modal Body -->
				<form method="POST" enctype="multipart/form-data" class="p-4 sm:p-6" id="editForm">
					<?php if ($message && $messageType === 'error' && $action === 'edit'): ?>
					<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
						<p class="text-red-600 dark:text-red-400">
							<i class="bi bi-exclamation-circle mr-2"></i><?= htmlspecialchars($message) ?>
						</p>
					</div>
					<?php endif; ?>

					<input type="hidden" name="id" id="edit_id" value="<?= $editUser ? $editUser['id'] : '' ?>">
					<input type="hidden" name="foto_lama" id="edit_foto_lama" value="<?= $editUser ? htmlspecialchars($editUser['foto'] ?? '') : '' ?>">

					<div class="space-y-5">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username <span class="text-red-500">*</span></label>
							<input type="text" name="username" id="edit_username" required
								   value="<?= $editUser ? htmlspecialchars($editUser['username']) : '' ?>"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Masukkan username...">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Baru</label>
							<input type="password" name="password" id="edit_password"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Kosongkan jika tidak ingin mengubah password...">
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengubah password</p>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Profil</label>
							<div id="currentFotoContainer" class="mb-4 <?= ($editUser && !empty($editUser['foto'])) ? '' : 'hidden' ?>">
								<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Foto saat ini:</p>
								<img id="currentFoto" src="<?= $editUser && !empty($editUser['foto']) ? 'img/' . htmlspecialchars($editUser['foto']) : '' ?>"
									 class="w-24 h-24 object-cover rounded-full border-2 border-gray-200 dark:border-gray-600">
							</div>
							<input type="file" name="foto" accept="image/*"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, JPEG, PNG, GIF, WEBP (Max: 800KB)</p>
						</div>

						<?php
						$isEditingOwnAccount = ($editUser && $editUser['username'] === $_SESSION['username']);
						$editTargetRole = $editUser ? ($editUser['role'] ?? 'user') : 'user';
						?>
						<div id="edit_role_container" class="<?= $isEditingOwnAccount ? 'hidden' : '' ?>">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role <span class="text-red-500">*</span></label>
							<select name="role" id="edit_role"
									class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base">
								<option value="user" <?= $editTargetRole === 'user' ? 'selected' : '' ?>>User</option>
								<?php if ($currentUserRole === 'superadmin'): ?>
								<option value="admin" <?= $editTargetRole === 'admin' ? 'selected' : '' ?>>Admin</option>
								<option value="superadmin" <?= $editTargetRole === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
								<?php endif; ?>
							</select>
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
								<?php if ($currentUserRole === 'superadmin'): ?>
								Superadmin: Akses penuh. Admin: Hanya kelola user biasa. User: Akses terbatas.
								<?php else: ?>
								Anda hanya dapat mengubah ke role user biasa.
								<?php endif; ?>
							</p>
						</div>
						<?php if ($isEditingOwnAccount): ?>
						<input type="hidden" name="role" value="<?= $editTargetRole ?>">
						<div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
							<p class="text-yellow-600 dark:text-yellow-400 text-sm">
								<i class="bi bi-info-circle mr-2"></i>Anda tidak dapat mengubah role akun Anda sendiri.
							</p>
						</div>
						<?php endif; ?>
					</div>

					<!-- Modal Footer -->
					<div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button type="button" onclick="closeEditModal()"
								class="w-full sm:w-auto px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-x-circle"></i> Batal
						</button>
						<button type="submit" name="edit_user"
								class="w-full sm:w-auto px-6 py-3 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-check-circle"></i> Update User
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-md transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="p-6 text-center">
					<div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
						<i class="bi bi-exclamation-triangle text-3xl text-red-600 dark:text-red-400"></i>
					</div>
					<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus User</h3>
					<p class="text-gray-500 dark:text-gray-400 text-sm">
						Apakah Anda yakin ingin menghapus user:<br>
						<span id="delete_user_name" class="font-semibold text-gray-700 dark:text-gray-300"></span>
					</p>
					<p class="text-red-500 dark:text-red-400 text-xs mt-2">
						<i class="bi bi-info-circle mr-1"></i>Tindakan ini tidak dapat dibatalkan!
					</p>
				</div>

				<!-- Modal Footer -->
				<div class="flex gap-3 p-6 pt-0">
					<button type="button" onclick="closeDeleteModal()"
							class="flex-1 px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
						<i class="bi bi-x-circle"></i> Batal
					</button>
					<a id="delete_confirm_link" href="#"
					   class="flex-1 px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
						<i class="bi bi-trash"></i> Hapus
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function openAddModal() {
	document.getElementById('addModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeAddModal() {
	document.getElementById('addModal').classList.add('hidden');
	document.body.style.overflow = '';
	window.history.replaceState({}, '', 'admin.php?page=user');
}

function openEditModal(id, username, foto, role) {
	document.getElementById('edit_id').value = id;
	document.getElementById('edit_username').value = username;
	document.getElementById('edit_password').value = '';
	document.getElementById('edit_foto_lama').value = foto || '';

	const fotoContainer = document.getElementById('currentFotoContainer');
	const fotoElement = document.getElementById('currentFoto');

	if (foto) {
		fotoElement.src = <?= json_encode('img/') ?> + foto;
		fotoContainer.classList.remove('hidden');
	} else {
		fotoContainer.classList.add('hidden');
	}

	const roleSelect = document.getElementById('edit_role');
	if (roleSelect && role) {
		roleSelect.value = role;
	}

	const currentUsername = <?= json_encode($_SESSION['username']) ?>;
	const roleContainer = document.getElementById('edit_role_container');
	if (roleContainer) {
		if (username === currentUsername) {
			roleContainer.classList.add('hidden');
		} else {
			roleContainer.classList.remove('hidden');
		}
	}

	document.getElementById('editModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeEditModal() {
	document.getElementById('editModal').classList.add('hidden');
	document.body.style.overflow = '';
	window.history.replaceState({}, '', 'admin.php?page=user');
}

document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeAddModal();
		closeEditModal();
		closeDeleteModal();
	}
});

function openDeleteModal(id, username) {
	document.getElementById('delete_user_name').textContent = '"' + username + '"';
	document.getElementById('delete_confirm_link').href = 'admin.php?page=user&action=delete&id=' + id;
	document.getElementById('deleteModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
	document.getElementById('deleteModal').classList.add('hidden');
	document.body.style.overflow = '';
}
</script>

<script>
$(document).ready(function () {
	window.load_data = function (hlm) {
		if (typeof hlm === 'undefined' || hlm === null || hlm === '') hlm = 1;
		hlm = parseInt(hlm, 10);
		if (isNaN(hlm) || hlm < 1) hlm = 1;

		$.ajax({
			url: "user_data.php",
			method: "POST",
			data: { hlm: hlm },
			success: function (data) {
				$('#user_data').html(data);
			},
			error: function (xhr) {
				$('#user_data').html(
					'<div class="alert alert-danger">Gagal memuat data (HTTP ' +
					(xhr && xhr.status ? xhr.status : 'error') + '). Silakan refresh atau login ulang.</div>'
				);
			}
		});
	};

	load_data(1);

	$(document).on('click', '.halaman', function (e) {
		e.preventDefault();
		var page = parseInt($(this).attr('id'), 10);
		if (!isNaN(page) && page > 0) {
			load_data(page);
		}
	});
});
</script>
