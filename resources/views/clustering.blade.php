<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Hasil Clustering</title>
	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		body {
			padding: 30px;
			background-color: #f9f9f9;
		}

		h2,
		h3 {
			margin-top: 40px;
		}

		.chart-card {
			background: white;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 30px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
		}

		.metric-card {
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
		}
	</style>
</head>

<body>

	<div class="container">
		<h2 class="mb-4">Hasil Analisis Clustering</h2>

		<div class="row">
			<!-- Pie Chart -->
			<div class="col-md-6">
				<div class="chart-card">
					<h5>Distribusi Cluster (Pie)</h5>
					<canvas id="pieChart"></canvas>
				</div>
			</div>

			<!-- Bar Chart -->
			<div class="col-md-6">
				<div class="chart-card">
					<h5>Jumlah Pasien per Cluster (Bar)</h5>
					<canvas id="barChart"></canvas>
				</div>
			</div>
		</div>

		<!-- Scatter Plot -->
		{{-- <div class="chart-card">
			<h5>Visualisasi Pasien (Scatter)</h5>
			<canvas id="scatterChart"></canvas>
		</div> --}}

		<!-- Centroid Plot -->
		<div class="chart-card">
			<h5>Centroid Cluster</h5>
			<canvas id="centroidChart"></canvas>
		</div>

		<!-- Metrics -->
		<div class="row mt-4">
			<div class="col-md-6 offset-md-3">
				<div class="metric-card">
					<h5 class="mb-3">Metrics Evaluasi</h5>
					<ul class="list-group">
						<li class="list-group-item">
							<strong>Silhouette Score:</strong> {{ $metrics['silhouette'] }}
						</li>
						<li class="list-group-item">
							<strong>WCSS:</strong> {{ $metrics['wcss'] }}
						</li>
						<li class="list-group-item">
							{{-- <strong>Davies-Bouldin Index:</strong> {{ $metrics['davies_bouldin'] }} --}}
						</li>
						<li class="list-group-item">
							<strong>Jumlah Iterasi:</strong> {{ $iterations }}
						</li>
					</ul>
				</div>
			</div>
		</div>


		<h3 class="mt-5">Analisis Penyakit Berdasarkan Cluster</h3>

		@php
			$byCluster = $datasetClustered->groupBy('cluster');
			$jenisPenyakitMap = $jenisPenyakit->pluck('name', 'id')->toArray();
		@endphp

		@foreach ($byCluster as $clusterId => $items)
			<div class="chart-card">
				<h5>Cluster {{ $clusterId }}</h5>

				<!-- Hitung jenis penyakit -->
				@php
					$penyakitCounts = $items->groupBy('jenis_penyakit')->map->count();
					$usiaCounts = $items->groupBy('kelompok_usia')->map->count();
					$genderCounts = $items->groupBy('jenis_kelamin')->map->count();

					// Hitung jumlah penyakit per nama
					$penyakitCounts = $items->groupBy('jenis_penyakit')->mapWithKeys(function ($group, $key) use ($jenisPenyakitMap) {
					    $label = $jenisPenyakitMap[$key] ?? 'Tidak Diketahui';
					    return [$label => $group->count()];
					});

					// Ambil label dan data untuk chart
					$penyakitLabels = $penyakitCounts->keys()->values();
					$penyakitValues = $penyakitCounts->values();
				@endphp

				<div class="row mb-4">
					<!-- Jenis Penyakit -->
					<div class="col-md-4">
						<canvas id="penyakitChart{{ $clusterId }}"></canvas>
					</div>

					<!-- Kelompok Usia -->
					<div class="col-md-4">
						<canvas id="usiaChart{{ $clusterId }}"></canvas>
					</div>

					<!-- Jenis Kelamin -->
					<div class="col-md-4">
						<canvas id="genderChart{{ $clusterId }}"></canvas>
					</div>
				</div>

				<!-- Tabel Detail Pasien -->
				<div class="table-responsive mt-4">
					<table class="table table-bordered table-striped">
						<thead class="table-light">
							<tr>
								<th>#</th>
								<th>Nama Pasien</th>
								<th>Jenis Penyakit</th>
								<th>Kelompok Usia</th>
								<th>Jenis Kelamin</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($items as $index => $pasien)
								<tr>
									<td>{{ $index + 1 }}</td>
									<td>{{ $pasien->pasien }}</td>
									<td>{{ $jenisPenyakitMap[$pasien->jenis_penyakit] ?? 'Tidak diketahui' }}</td>
									<td>{{ $pasien->kelompok_usia }}</td>
									<td>{{ $pasien->jenis_kelamin }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<!-- Script Chart -->
				<script>
					new Chart(document.getElementById('penyakitChart{{ $clusterId }}'), {
						type: 'pie',
						data: {
							labels: {!! json_encode($penyakitCounts->keys()) !!},
							datasets: [{
								data: {!! json_encode($penyakitCounts->values()) !!},
								backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8aff8a', '#9966FF']
							}]
						}
					});

					new Chart(document.getElementById('usiaChart{{ $clusterId }}'), {
						type: 'bar',
						data: {
							labels: {!! json_encode($usiaCounts->keys()) !!},
							datasets: [{
								label: 'Jumlah',
								data: {!! json_encode($usiaCounts->values()) !!},
								backgroundColor: '#4BC0C0'
							}]
						},
						options: {
							plugins: {
								legend: {
									display: false
								}
							}
						}
					});

					new Chart(document.getElementById('genderChart{{ $clusterId }}'), {
						type: 'doughnut',
						data: {
							labels: {!! json_encode($genderCounts->keys()) !!},
							datasets: [{
								data: {!! json_encode($genderCounts->values()) !!},
								backgroundColor: ['#FF9F40', '#9966FF']
							}]
						}
					});
				</script>
			</div>
		@endforeach


	</div>

	<script>
		const clusters = @json($clusters);
		const centroids = @json($centroids);

		const clusterCounts = {};
		clusters.forEach(item => {
			const cluster = item.cluster;
			clusterCounts[cluster] = (clusterCounts[cluster] || 0) + 1;
		});

		const labels = Object.keys(clusterCounts).map(c => 'Cluster ' + c);
		const counts = Object.values(clusterCounts);
		const colors = ['#ff6384', '#36a2eb', '#4bc0c0', '#9966ff', '#ff9f40'];

		// Pie Chart
		new Chart(document.getElementById('pieChart'), {
			type: 'pie',
			data: {
				labels: labels,
				datasets: [{
					data: counts,
					backgroundColor: colors
				}]
			}
		});

		// Bar Chart
		new Chart(document.getElementById('barChart'), {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Jumlah Pasien',
					data: counts,
					backgroundColor: colors
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false
					}
				}
			}
		});

		// Scatter Plot
		const scatterDatasets = Object.keys(clusterCounts).map(clusterId => ({
			label: 'Cluster ' + clusterId,
			data: clusters
				.filter(c => c.cluster == clusterId)
				.map((c, i) => ({
					x: c.dataset_id,
					y: i
				})),
			backgroundColor: colors[clusterId - 1]
		}));

		new Chart(document.getElementById('scatterChart'), {
			type: 'scatter',
			data: {
				datasets: scatterDatasets
			},
			options: {
				scales: {
					x: {
						title: {
							display: true,
							text: 'Dataset ID'
						}
					},
					y: {
						title: {
							display: true,
							text: 'Pasien Index'
						}
					}
				}
			}
		});

		// Centroid Plot
		const centroidDataset = {
			label: 'Centroids',
			data: centroids.map(c => ({
				x: c[0],
				y: c[1]
			})),
			backgroundColor: '#000000',
			pointRadius: 8,
			pointStyle: 'rectRot'
		};

		new Chart(document.getElementById('centroidChart'), {
			type: 'scatter',
			data: {
				datasets: [centroidDataset]
			},
			options: {
				scales: {
					x: {
						title: {
							display: true,
							text: 'Feature 1'
						}
					},
					y: {
						title: {
							display: true,
							text: 'Feature 2'
						}
					}
				}
			}
		});
	</script>

</body>

</html>
