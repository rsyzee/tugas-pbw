<?php
session_start();
include "db-conn.php";
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>My Daily Journal</title>
		<script>
			(function () {
				try {
					var theme = localStorage.getItem('theme') || 'light';
					if (theme === 'dark') {
						document.documentElement.classList.add('dark');
					} else {
						document.documentElement.classList.remove('dark');
					}
				} catch (e) {}
			})();
		</script>
		<script src="https://cdn.tailwindcss.com"></script>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
			rel="stylesheet"
		/>
		<script>
			tailwind.config = {
				darkMode: "class",
			};
		</script>
		<style>
			html {
				scroll-behavior: smooth;
			}
			.schedule-card {
				transition:
					transform 0.22s ease,
					box-shadow 0.22s ease;
			}
			.schedule-card:hover {
				transform: translateY(-6px) scale(1.02);
			}
			.profile-img {
				transition: transform 0.3s ease;
			}
			.profile-img:hover {
				transform: scale(1.06);
			}

			section[id] {
				scroll-margin-top: 80px;
			}
		</style>
	</head>
	<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
		<nav
			class="bg-gray-50 dark:bg-gray-900 shadow-sm fixed top-0 w-full z-50 py-3 transition-colors duration-300"
		>
			<div class="container mx-auto px-4">
				<div class="flex items-center justify-between">
					<a
						class="text-xl font-bold text-gray-900 dark:text-gray-100"
						href="#"
						>Nafha's Daily Journal</a
					>

					<div class="flex items-center gap-4">
						<button class="md:hidden px-3 py-2" id="menuToggle">
							<i class="bi bi-list text-2xl"></i>
						</button>

						<button
							class="px-3 py-2 rounded-full border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
							id="themeToggle"
						>
							<i id="themeIcon" class="bi bi-moon-stars"></i>
						</button>

						<ul class="hidden md:flex gap-6 items-center">
							<li>
								<a
									class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
									href="#"
									>Home</a
								>
							</li>
							<li>
								<a
									class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
									href="#article"
									>Article</a
								>
							</li>
							<li>
								<a
									class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
									href="#gallery"
									>Gallery</a
								>
							</li>
							<li>
								<a
									class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
									href="#schedule"
									>Schedule</a
								>
							</li>
							<li>
								<a
									class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
									href="#profile"
									>Profile</a
								>
							</li>
							<?php if (isset($_SESSION['username'])): ?>
							<li class="relative group">
								<button class="flex items-center gap-1 text-green-600 dark:text-green-400 font-bold hover:text-green-700 dark:hover:text-green-300 transition-colors" id="dropdownBtn">
									<i class="bi bi-person-check-fill mr-1"></i>
									<?= htmlspecialchars($_SESSION['username']) ?>
									<i class="bi bi-chevron-down text-sm"></i>
								</button>
								<ul id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2 z-50">
									<li>
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" href="admin.php">
											<i class="bi bi-person-fill-gear mr-2"></i>Admin Panel
										</a>
									</li>
									<li>
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-red-500" href="logout.php">
											<i class="bi bi-box-arrow-right mr-2"></i>Logout
										</a>
									</li>
								</ul>
							</li>
							<?php else: ?>
							<li>
								<a
									class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors"
									href="login.php"
									>Login</a
								>
							</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<ul
					id="mobileMenu"
					class="hidden md:hidden mt-4 space-y-2 text-center"
				>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="#"
							>Home</a
						>
					</li>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="#article"
							>Article</a
						>
					</li>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="#gallery"
							>Gallery</a
						>
					</li>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="#schedule"
							>Schedule</a
						>
					</li>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="#profile"
							>Profile</a
						>
					</li>
					<?php if (isset($_SESSION['username'])): ?>
					<li>
						<span class="block py-2 text-green-600 dark:text-green-400 font-bold">
							<i class="bi bi-person-check-fill mr-1"></i>
							<?= htmlspecialchars($_SESSION['username']) ?>
						</span>
					</li>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="admin.php"
							>Admin Panel</a
						>
					</li>
					<li>
						<a
							class="block py-2 text-red-500 hover:text-red-600 transition-colors"
							href="logout.php"
							>Logout</a
						>
					</li>
					<?php else: ?>
					<li>
						<a
							class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
							href="login.php"
							>Login</a
						>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</nav>

		<!-- Hero Section Begin -->
		<section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900">
			<!-- Background Pattern -->
			<div class="absolute inset-0 opacity-30 dark:opacity-20">
				<div class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
				<div class="absolute top-40 right-10 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
				<div class="absolute bottom-20 left-1/2 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
			</div>

			<div class="container mx-auto px-4 pt-24 pb-12 relative z-10">
				<div class="flex flex-col-reverse md:flex-row items-center justify-between gap-12">
					<!-- Text Content -->
					<div class="flex-1 text-center md:text-left">
						<div class="inline-block px-4 py-2 bg-blue-100 dark:bg-blue-900/50 rounded-full mb-6">
							<span class="text-blue-600 dark:text-blue-400 text-sm font-medium">
								<i class="bi bi-journal-richtext mr-2"></i>Digital Journal
							</span>
						</div>

						<h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
							<span class="text-gray-800 dark:text-white">Catat Aktivitas,</span><br>
							<span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Simpan Kenangan,</span><br>
							<span class="text-gray-800 dark:text-white">Setiap Hari</span>
						</h1>

						<p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-xl mx-auto md:mx-0">
							Jurnal harian digital untuk merekam semua kegiatan dan momen pentingmu dengan mudah dan terorganisir.
						</p>

						<!-- Date & Time -->
						<div class="flex flex-col sm:flex-row items-center justify-center md:justify-start gap-4 mb-8">
							<div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
								<i class="bi bi-calendar3 text-blue-500"></i>
								<span id="tanggal" class="text-gray-700 dark:text-gray-300 font-medium"></span>
							</div>
							<div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
								<i class="bi bi-clock text-purple-500"></i>
								<span id="jam" class="text-gray-700 dark:text-gray-300 font-medium font-mono"></span>
							</div>
						</div>

						<!-- CTA Buttons -->
						<div class="flex flex-col sm:flex-row items-center justify-center md:justify-start gap-4">
							<a href="#article" class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2">
								<i class="bi bi-book"></i> Lihat Artikel
							</a>
							<a href="#profile" class="w-full sm:w-auto bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center gap-2">
								<i class="bi bi-person"></i> Tentang Saya
							</a>
						</div>
					</div>

					<!-- Hero Image -->
					<div class="flex-1 flex justify-center">
						<div class="relative">
							<!-- Decorative Elements -->
							<div class="absolute -top-4 -left-4 w-24 h-24 bg-green-500 rounded-2xl opacity-20 animate-bounce" style="animation-duration: 3s;"></div>
							<div class="absolute -bottom-4 -right-4 w-32 h-32 bg-blue-500 rounded-full opacity-20 animate-bounce" style="animation-duration: 4s;"></div>

							<!-- Terminal Window -->
							<div class="relative bg-gradient-to-br from-green-500 to-blue-600 p-1 rounded-3xl shadow-2xl">
								<div class="bg-gray-900 rounded-3xl overflow-hidden">
									<!-- Terminal Header -->
									<div class="bg-gray-800 px-4 py-3 flex items-center gap-2">
										<div class="w-3 h-3 rounded-full bg-red-500"></div>
										<div class="w-3 h-3 rounded-full bg-yellow-500"></div>
										<div class="w-3 h-3 rounded-full bg-green-500"></div>
										<span class="ml-4 text-gray-400 text-sm font-mono">user@linux:~</span>
									</div>
									<!-- Terminal Body -->
									<div class="p-6 font-mono text-sm md:text-base w-64 md:w-80 h-64 md:h-80 flex flex-col justify-start">
										<p class="text-green-400 mb-2">$ whoami</p>
										<p class="text-gray-300 mb-4">nafhx</p>
										<p class="text-green-400 mb-2">$ cat interests.txt</p>
										<p class="text-gray-300 mb-1">🐧 Linux</p>
										<p class="text-gray-300 mb-1">🔓 Reverse Engineering</p>
										<p class="text-gray-300 mb-1">🌐 Network Programming</p>
										<p class="text-gray-300 mb-4">⚡ Low-Level Dev</p>
										<p class="text-green-400">$ <span class="animate-pulse">|</span></p>
									</div>
								</div>
							</div>

							<!-- Floating Cards -->
							<div class="absolute -top-6 -left-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 animate-bounce" style="animation-duration: 2s;">
								<div class="flex items-center gap-2">
									<div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/50 rounded-lg flex items-center justify-center">
										<i class="bi bi-terminal-fill text-orange-500 text-xl"></i>
									</div>
									<div>
										<p class="text-xs text-gray-500 dark:text-gray-400">Shell</p>
										<p class="text-sm font-bold text-gray-800 dark:text-white">Bash</p>
									</div>
								</div>
							</div>

							<div class="absolute -top-6 -right-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 animate-bounce" style="animation-duration: 2.2s;">
								<div class="flex items-center gap-2">
									<div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center">
										<i class="bi bi-shield-lock-fill text-red-500 text-xl"></i>
									</div>
									<div>
										<p class="text-xs text-gray-500 dark:text-gray-400">Security</p>
										<p class="text-sm font-bold text-gray-800 dark:text-white">RE</p>
									</div>
								</div>
							</div>

							<div class="absolute -bottom-6 -left-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 animate-bounce" style="animation-duration: 2.5s;">
								<div class="flex items-center gap-2">
									<div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
										<i class="bi bi-diagram-3-fill text-blue-500 text-xl"></i>
									</div>
									<div>
										<p class="text-xs text-gray-500 dark:text-gray-400">Dev</p>
										<p class="text-sm font-bold text-gray-800 dark:text-white">Network</p>
									</div>
								</div>
							</div>

							<div class="absolute -bottom-6 -right-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 animate-bounce" style="animation-duration: 2.8s;">
								<div class="flex items-center gap-2">
									<div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
										<i class="bi bi-code-slash text-purple-500 text-xl"></i>
									</div>
									<div>
										<p class="text-xs text-gray-500 dark:text-gray-400">Code</p>
										<p class="text-sm font-bold text-gray-800 dark:text-white">ASM, C, Rust</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Scroll Indicator -->
				<div class="mt-12 md:mt-0 md:absolute md:bottom-8 md:left-1/2 md:transform md:-translate-x-1/2 flex justify-center w-full md:w-auto">
					<a href="#article" class="flex flex-col items-center text-gray-400 hover:text-blue-500 transition-colors animate-bounce">
						<span class="text-sm mb-2">Scroll Down</span>
						<i class="bi bi-chevron-double-down text-2xl"></i>
					</a>
				</div>
			</div>
		</section>
		<!-- Hero Section End -->

		<!-- Article Section Begin -->
		<section id="article" class="py-12 px-4 bg-gray-50 dark:bg-gray-900">
			<div class="container mx-auto">
				<h2 class="text-center text-3xl md:text-4xl font-bold mb-8">
					<i class="bi bi-newspaper mr-2"></i>Articles
				</h2>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<?php
					$sql = "SELECT * FROM article ORDER BY tanggal DESC";
					$hasil = $pdo->query($sql);

					if ($hasil->rowCount() > 0) {
						while ($row = $hasil->fetch()) {
					?>
						<div class="schedule-card">
							<div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10 border border-gray-200 dark:border-gray-700 h-full flex flex-col">
								<!-- Image -->
								<?php if ($row["gambar"] != '' && file_exists('img/' . $row["gambar"])) { ?>
									<img
										src="img/<?= htmlspecialchars($row["gambar"]) ?>"
										class="w-full h-48 object-cover"
										alt="<?= htmlspecialchars($row["judul"]) ?>"
									/>
								<?php } else { ?>
									<div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
										<i class="bi bi-newspaper text-white text-5xl"></i>
									</div>
								<?php } ?>

								<!-- Content -->
								<div class="p-6 flex-1 flex flex-col">
									<h3 class="font-bold text-xl mb-2 text-gray-800 dark:text-gray-100">
										<?= htmlspecialchars($row["judul"]) ?>
									</h3>
									<p class="text-gray-600 dark:text-gray-300 text-sm flex-1 mb-4">
										<?= htmlspecialchars(substr($row["isi"], 0, 120)) ?><?= strlen($row["isi"]) > 120 ? '...' : '' ?>
									</p>

									<!-- Footer -->
									<div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-700">
										<span class="flex items-center gap-1">
											<i class="bi bi-calendar3"></i>
											<?= $row["tanggal"] ?>
										</span>
										<span class="flex items-center gap-1">
											<i class="bi bi-person"></i>
											<?= htmlspecialchars($row["username"]) ?>
										</span>
									</div>
								</div>
							</div>
						</div>
					<?php
						}
					} else {
					?>
						<div class="col-span-full text-center py-12">
							<div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
								<i class="bi bi-inbox text-5xl mb-3"></i>
								<p class="text-lg font-medium">Belum ada artikel</p>
								<p class="text-sm">Artikel akan ditampilkan di sini</p>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</section>
		<!-- Article Section End -->

		<!-- Gallery Section Begin -->
		<section id="gallery" class="py-20 px-4 bg-gray-50 dark:bg-gray-900 relative overflow-hidden">
			<div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
				<div class="absolute top-20 left-10 w-64 h-64 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
				<div class="absolute bottom-20 right-10 w-64 h-64 bg-purple-200 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
			</div>

			<div class="container mx-auto relative z-10">
				<div class="text-center mb-12">
					<h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">
						<i class="bi bi-images mr-2"></i>Gallery
					</h2>
				</div>

				<?php

				$sql_gallery = "SELECT * FROM gallery WHERE gambar != '' ORDER BY tanggal DESC";
				$hasil_gallery = $pdo->query($sql_gallery);
				?>

				<div class="relative max-w-5xl mx-auto group">
					<!-- Carousel Wrapper -->
					<div class="overflow-hidden rounded-2xl md:rounded-3xl shadow-2xl aspect-[4/3] md:aspect-[16/9] relative bg-gray-200 dark:bg-gray-800 border-2 md:border-4 border-white dark:border-gray-700 transform transition-transform duration-300 hover:scale-[1.01]">
						<div id="carousel-inner" class="flex transition-transform duration-700 ease-in-out h-full">
							<?php
							if ($hasil_gallery->rowCount() > 0) {
								while ($row_gallery = $hasil_gallery->fetch()) {
							?>
							<div class="w-full flex-shrink-0 relative h-full group/slide">
								<img
									src="img/<?= htmlspecialchars($row_gallery["gambar"]) ?>"
									class="w-full h-full object-cover transition-transform duration-700 group-hover/slide:scale-105"
									alt="<?= htmlspecialchars($row_gallery["judul"] ?: 'Gallery Image') ?>"
								>
								<!-- Gradient Overlay -->
								<div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 md:via-black/20 to-transparent opacity-90 md:opacity-80"></div>

								<!-- Caption -->
								<div class="absolute bottom-0 left-0 right-0 p-4 md:p-8 text-white transform translate-y-0 transition-transform duration-500">
									<div class="bg-black/30 md:bg-white/10 backdrop-blur-md rounded-xl p-4 md:p-6 border border-white/10 md:border-white/20 shadow-lg">
										<span class="inline-block px-2 py-1 md:px-3 md:py-1 bg-blue-600 rounded-full text-[10px] md:text-xs font-bold mb-2 md:mb-3 shadow-md">
											<i class="bi bi-calendar3 mr-1"></i> <?= date('d M Y', strtotime($row_gallery["tanggal"])) ?>
										</span>
										<?php if (!empty($row_gallery["judul"])): ?>
										<h3 class="text-lg md:text-2xl font-bold mb-1 md:mb-2 text-shadow-sm truncate"><?= htmlspecialchars($row_gallery["judul"]) ?></h3>
										<?php endif; ?>
										<?php if (!empty($row_gallery["deskripsi"])): ?>
										<p class="text-gray-200 text-xs md:text-sm line-clamp-2 leading-relaxed"><?= htmlspecialchars(strip_tags($row_gallery["deskripsi"])) ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php
								}
							} else {
							?>
							<div class="w-full flex-shrink-0 h-full flex items-center justify-center text-gray-500 bg-gray-100 dark:bg-gray-800">
								<div class="text-center">
									<i class="bi bi-image text-6xl mb-4 text-gray-300 dark:text-gray-600 block"></i>
									<p class="text-xl font-medium">Belum ada momen yang dibagikan</p>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>

					<!-- Navigation Buttons -->
					<button id="prevBtn" class="hidden md:block absolute top-1/2 -left-12 -translate-y-1/2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-4 rounded-full shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 z-20 opacity-0 group-hover:opacity-100 border border-gray-100 dark:border-gray-700">
						<i class="bi bi-chevron-left text-xl"></i>
					</button>
					<button id="nextBtn" class="hidden md:block absolute top-1/2 -right-12 -translate-y-1/2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-4 rounded-full shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 z-20 opacity-0 group-hover:opacity-100 border border-gray-100 dark:border-gray-700">
						<i class="bi bi-chevron-right text-xl"></i>
					</button>

					<!-- Indicators -->
					<div class="absolute -bottom-8 md:-bottom-10 left-1/2 -translate-x-1/2 flex gap-2 md:gap-3" id="carousel-indicators">
					</div>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function() {
					const carouselInner = document.getElementById('carousel-inner');
					const prevBtn = document.getElementById('prevBtn');
					const nextBtn = document.getElementById('nextBtn');
					const indicatorsContainer = document.getElementById('carousel-indicators');

					const slides = carouselInner.children;
					const totalSlides = slides.length;
					let currentSlide = 0;
					let autoSlideInterval;

					for (let i = 0; i < totalSlides; i++) {
						const dot = document.createElement('button');
						dot.className = `w-3 h-3 rounded-full transition-all duration-300 ${i === 0 ? 'bg-blue-600 w-8' : 'bg-gray-300 dark:bg-gray-600 hover:bg-blue-400'}`;
						dot.addEventListener('click', () => goToSlide(i));
						indicatorsContainer.appendChild(dot);
					}

					const updateIndicators = () => {
						const dots = indicatorsContainer.children;
						for (let i = 0; i < dots.length; i++) {
							dots[i].className = `w-3 h-3 rounded-full transition-all duration-300 ${i === currentSlide ? 'bg-blue-600 w-8' : 'bg-gray-300 dark:bg-gray-600 hover:bg-blue-400'}`;
						}
					};

					const goToSlide = (index) => {
						if (index < 0) index = totalSlides - 1;
						if (index >= totalSlides) index = 0;

						currentSlide = index;
						carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
						updateIndicators();
						resetAutoSlide();
					};

					const nextSlide = () => goToSlide(currentSlide + 1);
					const prevSlide = () => goToSlide(currentSlide - 1);

					const startAutoSlide = () => {
						autoSlideInterval = setInterval(nextSlide, 2500);
					};

					const resetAutoSlide = () => {
						clearInterval(autoSlideInterval);
						startAutoSlide();
					};

					prevBtn.addEventListener('click', prevSlide);
					nextBtn.addEventListener('click', nextSlide);

					let touchStartX = 0;
					let touchEndX = 0;

					carouselInner.addEventListener('touchstart', e => {
						touchStartX = e.changedTouches[0].screenX;
					});

					carouselInner.addEventListener('touchend', e => {
						touchEndX = e.changedTouches[0].screenX;
						if (touchStartX - touchEndX > 50) nextSlide();
						if (touchEndX - touchStartX > 50) prevSlide();
					});

					if (totalSlides > 0) {
						startAutoSlide();
					}
				});
			</script>
		</section>
		<!-- Gallery Section End -->

		<!-- Schedule Section Begin -->
		<section id="schedule" class="py-12 px-4 bg-gray-50 dark:bg-gray-900">
			<div class="container mx-auto">
				<h2 class="text-center text-3xl md:text-4xl font-bold mb-8">
					<i class="bi bi-calendar-week mr-2"></i>Jadwal Kuliah
				</h2>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
					<div class="schedule-card cursor-pointer">
						<div class="bg-white dark:bg-gray-800 border-2 border-blue-500 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10">
							<div class="bg-blue-500 text-white text-center font-bold py-3">
								Senin
							</div>
							<div class="p-6 text-center space-y-4">
								<p class="leading-relaxed">
									09:00 - 10:30<br /><b>Basis Data</b><br />Ruang
									H.3.4
								</p>
								<p class="leading-relaxed">
									13:00 - 15:00<br /><b>Kriptografi</b><br />Ruang H.3.1
								</p>
							</div>
						</div>
					</div>

					<div class="schedule-card cursor-pointer">
						<div
							class="bg-white dark:bg-gray-800 border-2 border-green-500 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10">
							<div
								class="bg-green-500 text-white text-center font-bold py-3"
							>
								Selasa
							</div>
							<div class="p-6 text-center space-y-4">
								<p class="leading-relaxed">
									08:00 - 09:30<br /><b>Pemrograman Web</b><br />Ruang D.2.J
								</p>
								<p class="leading-relaxed">
									14:00 - 16:00<br /><b>Basis Data</b><br />Ruang D.3.M
								</p>
							</div>
						</div>
					</div>

					<div class="schedule-card cursor-pointer">
						<div
							class="bg-white dark:bg-gray-800 border-2 border-red-500 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10"
						>
							<div
								class="bg-red-500 text-white text-center font-bold py-3"
							>
								Rabu
							</div>
							<div class="p-6 text-center space-y-4">
								<p class="leading-relaxed">
									10:00 - 12:00<br /><b>Probabilitas & Stats </b><br />Ruang D.2.A
								</p>
								<p class="leading-relaxed">
									11:00 - 13:00<br /><b>PKN</b
									><br />Ruang E.3
								</p>
							</div>
						</div>
					</div>

					<div class="schedule-card cursor-pointer">
						<div
							class="bg-white dark:bg-gray-800 border-2 border-yellow-400 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10"
						>
							<div
								class="bg-yellow-400 text-gray-900 text-center font-bold py-3"
							>
								Kamis
							</div>

							<div class="p-6 text-center space-y-4">
								<p class="leading-relaxed">
									08:00 - 10:00<br />Logika Informatika</b><br />Ruang H.4.2
								</p>
								<p class="leading-relaxed">
									11:00 - 13:00<br /><b>Sistem Operasi</b
									><br />Ruang H.3.4
								</p>
							</div>
						</div>
					</div>

					<div class="schedule-card cursor-pointer">
						<div class="bg-white dark:bg-gray-800 border-2 border-blue-500 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl dark:hover:shadow-white/10">
							<div class="bg-blue-500 text-white text-center font-bold py-3">
								Jumat
							</div>
							<div class="p-6 text-center space-y-4">
								<p class="leading-relaxed">
									09:00 - 10:30<br /><b>Basis Data</b><br />Ruang H.3.4
								</p>
								<p class="leading-relaxed">
									13:00 - 15:00<br /><b>Kriptografi</b><br />Ruang H.3.1
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Schedule Section End -->

		<!-- Profile Section Begin -->
		<section id="profile" class="py-12 px-4 bg-gray-50 dark:bg-gray-900">
			<h2 class="text-center text-3xl md:text-4xl font-bold mb-8">
				Profil Mahasiswa
			</h2>
			<div class="container mx-auto">
				<div
					class="flex flex-col md:flex-row items-center justify-center gap-8 max-w-4xl mx-auto"
				>
					<div class="text-center">
						<img
							src="http://mahasiswa.dinus.ac.id/images/foto/A/A11/2024/A11.2024.15579.jpg"
							class="rounded-full border-4 border-gray-300 dark:border-gray-600 shadow-lg profile-img w-44 h-44 object-cover mx-auto hover:shadow-2xl hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300"
						/>
					</div>
					<div class="w-full md:w-auto md:flex-1">
						<div
							class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-2xl dark:hover:shadow-white/10 hover:-translate-y-2"
						>
							<h5 class="font-bold text-xl text-center mb-2">
								Ahmad Rassya Annafi'ulhaq
							</h5>
							<p
								class="text-center text-gray-500 dark:text-gray-400 mb-4"
							>
								Mahasiswa Teknik Informatika
							</p>
							<table class="w-full">
								<tr
									class="border-b border-gray-100 dark:border-gray-700"
								>
									<th
										class="text-left py-2 font-semibold pr-4"
									>
										NIM
									</th>
									<td class="py-2">: A11.2024.15579</td>
								</tr>
								<tr
									class="border-b border-gray-100 dark:border-gray-700"
								>
									<th
										class="text-left py-2 font-semibold pr-4"
									>
										Program Studi
									</th>
									<td class="py-2">: Teknik Informatika</td>
								</tr>
								<tr>
									<th
										class="text-left py-2 font-semibold pr-4">
										Email
									</th>
									<td class="py-2">: nafhqd@gmail.com</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Profile Section End -->

		<script>
			const themeToggle = document.getElementById("themeToggle");
			const themeIcon = document.getElementById("themeIcon");
			const menuToggle = document.getElementById("menuToggle");
			const mobileMenu = document.getElementById("mobileMenu");

			const currentTheme = localStorage.getItem("theme") || "light";
			if (currentTheme === "dark") {
				document.documentElement.classList.add("dark");
			}
			updateThemeIcon();

			themeToggle.addEventListener("click", function () {
				document.documentElement.classList.toggle("dark");
				const newTheme = document.documentElement.classList.contains(
					"dark",
				)
					? "dark"
					: "light";
				localStorage.setItem("theme", newTheme);
				updateThemeIcon();
			});

			function updateThemeIcon() {
				const isDark =
					document.documentElement.classList.contains("dark");
				themeIcon.className = isDark
					? "bi bi-sun-fill"
					: "bi bi-moon-stars";
			}

			menuToggle.addEventListener("click", function () {
				mobileMenu.classList.toggle("hidden");
			});

			const dropdownBtn = document.getElementById("dropdownBtn");
			const dropdownMenu = document.getElementById("dropdownMenu");

			if (dropdownBtn && dropdownMenu) {
				dropdownBtn.addEventListener("click", function () {
					dropdownMenu.classList.toggle("hidden");
				});

				document.addEventListener("click", function (e) {
					if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
						dropdownMenu.classList.add("hidden");
					}
				});
			}

			function updateDateTime() {
				const now = new Date();
				const options = {
					weekday: 'long',
					year: 'numeric',
					month: 'long',
					day: 'numeric'
				};
				const tanggal = now.toLocaleDateString('id-ID', options);
				const jam = now.toLocaleTimeString('id-ID', {
					hour: '2-digit',
					minute: '2-digit',
					second: '2-digit'
				});

				document.getElementById('tanggal').textContent = tanggal;
				document.getElementById('jam').textContent = jam;
			}

			updateDateTime();
			setInterval(updateDateTime, 1000);
		</script>

		<!-- Footer Begin -->
		<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
			<!-- Main Footer -->
			<div class="container mx-auto px-4 py-12">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
					<!-- Brand -->
					<div class="lg:col-span-2">
						<a href="#" class="text-2xl font-bold text-gray-900 dark:text-white mb-4 inline-block">
							<i class="bi bi-journal-richtext mr-2 text-blue-500"></i>Nafha's Daily Journal
						</a>
						<p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md">
							Jurnal harian digital untuk merekam semua kegiatan dan momen penting dengan mudah dan terorganisir. Catat setiap momen berharga dalam hidupmu.
						</p>
						<div class="flex gap-3">
							<a href="https://github.com/rsyzee" target="_blank" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-blue-500 dark:hover:bg-blue-500 rounded-lg flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-white transition-all duration-300">
								<i class="bi bi-github text-lg"></i>
							</a>
							<a href="https://t.me/zxscyf" target="_blank" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-blue-500 dark:hover:bg-blue-500 rounded-lg flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-white transition-all duration-300">
								<i class="bi bi-telegram text-lg"></i>
							</a>
							<a href="https://instagram.com/nafhx7" target="_blank" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-pink-500 dark:hover:bg-pink-500 rounded-lg flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-white transition-all duration-300">
								<i class="bi bi-instagram text-lg"></i>
							</a>
							<a href="mailto:nafhqd@gmail.com" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-red-500 dark:hover:bg-red-500 rounded-lg flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-white transition-all duration-300">
								<i class="bi bi-envelope-fill text-lg"></i>
							</a>
						</div>
					</div>

					<!-- Quick Links -->
					<div>
						<h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
							<i class="bi bi-link-45deg mr-2 text-blue-500"></i>Quick Links
						</h4>
						<ul class="space-y-3">
							<li>
								<a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
									<i class="bi bi-chevron-right text-xs"></i> Home
								</a>
							</li>
							<li>
								<a href="#article" class="text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
									<i class="bi bi-chevron-right text-xs"></i> Articles
								</a>
							</li>
							<li>
								<a href="#gallery" class="text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
									<i class="bi bi-chevron-right text-xs"></i> Gallery
								</a>
							</li>
							<li>
								<a href="#schedule" class="text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
									<i class="bi bi-chevron-right text-xs"></i> Schedule
								</a>
							</li>
							<li>
								<a href="#profile" class="text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
									<i class="bi bi-chevron-right text-xs"></i> Profile
								</a>
							</li>
						</ul>
					</div>

					<!-- Contact Info -->
					<div>
						<h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
							<i class="bi bi-geo-alt mr-2 text-blue-500"></i>Contact Info
						</h4>
						<ul class="space-y-3">
							<li class="flex items-start gap-3 text-gray-600 dark:text-gray-400">
								<i class="bi bi-building text-blue-500 mt-1"></i>
								<span>Universitas Dian Nuswantoro<br>Semarang, Indonesia</span>
							</li>
							<li class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
								<i class="bi bi-mortarboard text-blue-500"></i>
								<span>S1 - Teknik Informatika</span>
							</li>
							<li class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
								<i class="bi bi-envelope text-blue-500"></i>
								<a href="mailto:nafhqd@gmail.com" class="hover:text-blue-500 transition-colors">nafhqd@gmail.com</a>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Bottom Footer -->
			<div class="border-t border-gray-200 dark:border-gray-800">
				<div class="container mx-auto px-4 py-6">
					<div class="flex flex-col md:flex-row items-center justify-between gap-4">
						<p class="text-gray-600 dark:text-gray-400 text-sm text-center md:text-left">
							&copy; 2025 <span class="font-semibold text-gray-900 dark:text-white">Nafha's Daily Journal</span>. All rights reserved.
						</p>
						<p class="text-gray-500 dark:text-gray-500 text-sm flex items-center gap-2">
							<i class="bi bi-code-slash text-blue-500"></i> Capstone Project - Pemrograman Web
						</p>
					</div>
				</div>
			</div>

			<!-- Back to Top Button -->
			<button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 invisible translate-y-4 z-50 flex items-center justify-center">
				<i class="bi bi-chevron-up text-xl"></i>
			</button>

			<script>
				const backToTop = document.getElementById('backToTop');

				window.addEventListener('scroll', () => {
					if (window.scrollY > 300) {
						backToTop.classList.remove('opacity-0', 'invisible', 'translate-y-4');
						backToTop.classList.add('opacity-100', 'visible', 'translate-y-0');
					} else {
						backToTop.classList.add('opacity-0', 'invisible', 'translate-y-4');
						backToTop.classList.remove('opacity-100', 'visible', 'translate-y-0');
					}
				});

				backToTop.addEventListener('click', () => {
					window.scrollTo({ top: 0, behavior: 'smooth' });
				});
			</script>
		</footer>
		<!-- Footer End -->
	</body>
</html>
