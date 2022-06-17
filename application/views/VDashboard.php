<div class="main-panel">
	<div class="content">
		<div class="panel-header bg-primary-gradient">
			<div class="page-inner py-5">
				<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
					<div>
						<h2 class="text-white pb-2 fw-bold">Dashboard</h2>
						<h5 class="text-white op-7 mb-2">admin dashboard - geprec</h5>
					</div>
				</div>
			</div>
		</div>
		<div class="page-inner mt--5">
			<div class="row mt--2">
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card shadow">
						<div class="card-body">
							<div class="row no-gutters align-items-center">
								<div class="col mr-2">
									<div class="font-weight-bold text-uppercase mb-1">
										<h3>Data Kunjungan</h3>
									</div>
									<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $kunjungan ?> Pelanggan</div>
								</div>
								<div class="col-auto">
									<i class="fa fa-users fa-2x text-gray-300"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card shadow">
						<div class="card-body">
							<div class="row no-gutters align-items-center">
								<div class="col mr-2">
									<div class="font-weight-bold text-uppercase mb-1">
										<h3>Daftar Kunjungan</h3>
									</div>
									<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $daftar_kunjungan ?> Kunjungan</div>
								</div>
								<div class="col-auto">
									<i class="fa fa-street-view fa-2x text-primary"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card shadow">
						<div class="card-body">
							<div class="row no-gutters align-items-center">
								<div class="col mr-2">
									<div class="font-weight-bold text-uppercase mb-1">
										<h3>Jumlah Pengguna</h3>
									</div>
									<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $pengguna ?> Pengguna</div>
								</div>
								<div class="col-auto">
									<i class="fa fa-users fa-2x text-primary"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card shadow">
						<div class="card-body">
							<div class="row no-gutters align-items-center">
								<div class="col mr-2">
									<div class="font-weight-bold text-uppercase mb-1">
										<h3>Total Riwayat Kunjungan</h3>
									</div>
									<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $total_kunjungan ?> Kunjungan</div>
								</div>
								<div class="col-auto">
									<i class="fa fa-history fa-2x text-gray-300"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-8">
					<div class="card full-height">
						<div class="card-header">
							<div class="card-head-row">
								<div class="card-title">List Kunjungan per Pengguna</div>
							</div>
						</div>
						<div class="card-body">
							<?= $detail_pengguna; ?>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="row">
						<div class="col-12">
							<div class="card card-warning shadow">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="font-weight-bold text-uppercase mb-1">
												<h3>Jumlah<br>Data Menunggu</h3>
											</div>
											<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $jml_riwayat_0 ?> data</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="card card-primary shadow">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="font-weight-bold text-uppercase mb-1">
												<h3>Jumlah<br>Data Diterima</h3>
											</div>
											<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $jml_riwayat_1 ?> data</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-check-circle fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="card card-danger shadow">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="font-weight-bold text-uppercase mb-1">
												<h3>Jumlah<br>Data Ditolak</h3>
											</div>
											<div id="" class="h2 mb-0 font-weight-bold text-gray-800"><?= $jml_riwayat_2 ?> data</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-times-circle fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>