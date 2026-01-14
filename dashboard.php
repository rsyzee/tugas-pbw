<?php
$sql1 = "SELECT * FROM article ORDER BY tanggal DESC";
$hasil1 = $pdo->query($sql1);
$jumlah_article = $hasil1->rowCount();

$sql2 = "SELECT * FROM gallery";
$hasil2 = $pdo->query($sql2);
$jumlah_gallery = $hasil2->rowCount();

$sql3 = "SELECT * FROM user";
$hasil3 = $pdo->query($sql3);
$jumlah_user = $hasil3->rowCount();
?>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
	<h1 class="text-2xl md:text-3xl font-bold mb-4">
		<i class="bi bi-house-door mr-2"></i>Admin Dashboard
	</h1>
	<p class="text-lg text-gray-600 dark:text-gray-300">
		Selamat datang, <span class="text-blue-500 font-semibold"><?= $_SESSION['username'] ?></span>
	</p>

	<!-- Quick Stats Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
		<div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
			<div class="flex items-center gap-4">
				<div class="bg-blue-500 text-white p-3 rounded-lg">
					<i class="bi bi-file-earmark-text text-2xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Articles</p>
					<p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $jumlah_article ?></p>
				</div>
			</div>
		</div>

		<div class="bg-green-50 dark:bg-green-900/30 rounded-xl p-6 border border-green-200 dark:border-green-800">
			<div class="flex items-center gap-4">
				<div class="bg-green-500 text-white p-3 rounded-lg">
					<i class="bi bi-images text-2xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Gallery</p>
					<p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $jumlah_gallery ?></p>
				</div>
			</div>
		</div>

		<div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl p-6 border border-purple-200 dark:border-purple-800">
			<div class="flex items-center gap-4">
				<div class="bg-purple-500 text-white p-3 rounded-lg">
					<i class="bi bi-people text-2xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Users</p>
					<p class="text-2xl font-bold text-purple-600 dark:text-purple-400"><?= $jumlah_user ?></p>
				</div>
			</div>
		</div>

		<div class="bg-orange-50 dark:bg-orange-900/30 rounded-xl p-6 border border-orange-200 dark:border-orange-800">
			<div class="flex items-center gap-4">
				<div class="bg-orange-500 text-white p-3 rounded-lg">
					<i class="bi bi-person-check text-2xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 dark:text-gray-400">Logged In</p>
					<p class="text-lg font-bold text-orange-600 dark:text-orange-400"><?= $_SESSION['username'] ?></p>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="mt-8">
		<h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
		<div class="flex flex-wrap gap-4">
			<a href="admin.php?page=article" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
				<i class="bi bi-newspaper"></i> Manage Articles
			</a>
			<a href="admin.php?page=gallery" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
				<i class="bi bi-images"></i> Manage Gallery
			</a>
			<a href="admin.php?page=user" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
				<i class="bi bi-people"></i> Manage Users
			</a>
		</div>
	</div>
</div>
