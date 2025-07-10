<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Hasil Analisis K-Means Clustering</title>
	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- Plotly.js -->
	<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
	<!-- DataTables CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

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

		.dataTables_wrapper {
			padding: 20px;
			background: white;
			border-radius: 8px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		.table thead th {
			background-color: #007bff;
			color: white;
		}

		.table tbody tr:hover {
			background-color: #f1f1f1;
		}
	</style>
</head>

<body>

	<div class="container">
		<h3 class="mt-5">Data Pasien</h3>

		<div class="table-responsive mt-4">
			<table id="patientDataTable" class="table table-bordered table-striped">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Pasien</th>
						<th>Jenis Penyakit</th>
						<th>Kelompok Usia</th>
						<th>Jenis Kelamin</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($original_data as $data)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							<td>{{ $data['pasien'] }}</td>
							<td>{{ $data['jenis_penyakit'] }}</td>
							<td>{{ $data['kelompok_usia'] }}</td>
							<td>{{ $data['jenis_kelamin'] }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<h3 class="mt-5">Data Pasien Transformed</h3>

		<div class="table-responsive mt-4">
			<table id="patientTransformedDataTable" class="table table-bordered table-striped">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Pasien</th>
						<th>Jenis Penyakit</th>
						<th>Kelompok Usia</th>
						<th>Jenis Kelamin</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($transformed_data as $data)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							<td>{{ $data['pasien'] }}</td>
							<td>{{ $data['penyakit_id'] }}</td>
							<td>{{ $data['usia'] }}</td>
							<td>{{ $data['jk'] }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<h2 class="mb-4">Hasil Analisis Clustering per Iterasi</h2>

		@foreach ($iterations as $iteration)
			<div class="chart-card mt-4">
				<h5>Iterasi {{ $iteration['iteration_number'] }}</h5>

				<h6>Centroids Awal</h6>
				<table id="initialCentroidsTable{{ $iteration['iteration_number'] }}" class="table table-bordered table-striped">
					<thead class="table-light">
						<tr>
							<th>#</th>
							<th>Centroid</th>
							<th>Koordinat</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($iteration['initial_centroids'] as $index => $centroid)
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>Centroid {{ $index + 1 }}</td>
								<td>{{ implode(', ', $centroid) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				<h6>Centroids Baru</h6>
				<table id="newCentroidsTable{{ $iteration['iteration_number'] }}" class="table table-bordered table-striped">
					<thead class="table-light">
						<tr>
							<th>#</th>
							<th>Centroid</th>
							<th>Koordinat</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($iteration['new_centroids'] as $index => $centroid)
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>Centroid {{ $index + 1 }}</td>
								<td>{{ implode(', ', $centroid) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				<h6>Cluster dan Anggota</h6>
				@foreach ($iteration['clusters'] as $cluster)
					<div class="mt-3">
						<h6>Cluster {{ $cluster['cluster'] }} (Jumlah Anggota: {{ $cluster['size'] }})</h6>
						<div class="table-responsive">
							<table id="tableCluster{{ $iteration['iteration_number'] }}_{{ $cluster['cluster'] }}"
								class="table table-bordered table-striped">
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
									@foreach ($cluster['members'] as $index => $member)
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>{{ $member['pasien'] }}</td>
											<td>{{ $original_data[$member['index']]['jenis_penyakit'] }}</td>
											<td>{{ $original_data[$member['index']]['kelompok_usia'] }}</td>
											<td>{{ $original_data[$member['index']]['jenis_kelamin'] }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endforeach
			</div>
		@endforeach

		<script>
			$(document).ready(function() {
				// Inisialisasi DataTables untuk setiap tabel centroids
				@foreach ($iterations as $iteration)
					$('#initialCentroidsTable{{ $iteration['iteration_number'] }}').DataTable({
						paging: false,
						searching: false,
						ordering: false
					});

					$('#newCentroidsTable{{ $iteration['iteration_number'] }}').DataTable({
						paging: false,
						searching: false,
						ordering: false
					});

					// Inisialisasi DataTables untuk setiap cluster
					@foreach ($iteration['clusters'] as $cluster)
						$('#tableCluster{{ $iteration['iteration_number'] }}_{{ $cluster['cluster'] }}').DataTable({
							paging: true,
							searching: true,
							ordering: true,
							lengthChange: true,
							pageLength: 5,
							language: {
								info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
								infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
								emptyTable: "Tidak ada data tersedia di tabel",
								lengthMenu: "Tampilkan _MENU_ entri",
								search: "Pencarian:",
								paginate: {
									first: "Pertama",
									last: "Terakhir",
									next: "Berikutnya",
									previous: "Sebelumnya"
								}
							}
						});
					@endforeach
				@endforeach
			});
		</script>

		<h2 class="mb-4">Hasil Analisis Clustering</h2>

		<div class="row">
			<div class="col-md-6">
				<div class="chart-card">
					<h5>Jumlah Pasien per Cluster (Bar)</h5>
					<canvas id="barChart"></canvas>
				</div>
			</div>

			<div class="col-md-6">
				<div class="chart-card">
					<h5>Distribusi Cluster (Pie)</h5>
					<canvas id="pieChart"></canvas>
				</div>
			</div>
		</div>

		<div class="chart-card">
			<h5>Centroid Cluster (3D Scatter)</h5>
			<div id="centroidChart" style="height: 500px; width: 100%;"></div>
		</div>

		<h3 class="mt-5">Analisis Penyakit Berdasarkan Cluster</h3>
		@foreach ($iterations[0]['clusters'] as $cluster)
			<div class="chart-card mt-4">
				<h5>Cluster {{ $cluster['cluster'] }}</h5>
				<!-- Canvas for the pie chart -->

				<div class="row mb-4">
					<div class="col-md-6">
						<canvas id="chartClusterSummary{{ $cluster['cluster'] }}"></canvas>
					</div>
					<div class="col-md-6">
						<div class="table-responsive mt-4">
							<table id="tableClusterSummary{{ $cluster['cluster'] }}" class="table table-bordered table-striped">
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
									@foreach ($cluster['members'] as $index => $member)
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>{{ $member['pasien'] }}</td>
											<td>{{ $original_data[$member['index']]['jenis_penyakit'] }}</td>
											<td>{{ $original_data[$member['index']]['kelompok_usia'] }}</td>
											<td>{{ $original_data[$member['index']]['jenis_kelamin'] }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		@endforeach

		<script>
			$(document).ready(function() {
				@foreach ($iterations[0]['clusters'] as $cluster)
					$('#tableClusterSummary{{ $cluster['cluster'] }}').DataTable({
						paging: true,
						searching: true,
						ordering: true,
						lengthChange: true,
						pageLength: 5,
						language: {
							info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
							infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
							emptyTable: "Tidak ada data tersedia di tabel",
							lengthMenu: "Tampilkan _MENU_ entri",
							search: "Pencarian:",
							paginate: {
								first: "Pertama",
								last: "Terakhir",
								next: "Berikutnya",
								previous: "Sebelumnya"
							}
						}
					});

					// Data untuk pie chart
					const penyakitCounts{{ $cluster['cluster'] }} = {};
					@foreach ($cluster['members'] as $member)
						const jenisPenyakit{{ $cluster['cluster'] }}_{{ $loop->index }} =
							"{{ $original_data[$member['index']]['jenis_penyakit'] }}";
						penyakitCounts{{ $cluster['cluster'] }}[
							jenisPenyakit{{ $cluster['cluster'] }}_{{ $loop->index }}] = (
							penyakitCounts{{ $cluster['cluster'] }}[
								jenisPenyakit{{ $cluster['cluster'] }}_{{ $loop->index }}] || 0) + 1;
					@endforeach

					const labels{{ $cluster['cluster'] }} = Object.keys(penyakitCounts{{ $cluster['cluster'] }});
					const data{{ $cluster['cluster'] }} = Object.values(penyakitCounts{{ $cluster['cluster'] }});

					const ctx{{ $cluster['cluster'] }} = document.getElementById(
						'chartClusterSummary{{ $cluster['cluster'] }}').getContext('2d');
					new Chart(ctx{{ $cluster['cluster'] }}, {
						type: 'pie',
						data: {
							labels: labels{{ $cluster['cluster'] }},
							datasets: [{
								label: 'Distribusi Jenis Penyakit',
								data: data{{ $cluster['cluster'] }},
								backgroundColor: [
									'rgba(255, 99, 132, 0.2)',
									'rgba(54, 162, 235, 0.2)',
									'rgba(255, 206, 86, 0.2)',
									'rgba(75, 192, 192, 0.2)',
									'rgba(153, 102, 255, 0.2)',
									'rgba(255, 159, 64, 0.2)',
									'rgba(199, 199, 199, 0.2)'
								],
								borderColor: [
									'rgba(255, 99, 132, 1)',
									'rgba(54, 162, 235, 1)',
									'rgba(255, 206, 86, 1)',
									'rgba(75, 192, 192, 1)',
									'rgba(153, 102, 255, 1)',
									'rgba(255, 159, 64, 1)',
									'rgba(199, 199, 199, 1)'
								],
								borderWidth: 1
							}]
						},
						options: {
							responsive: true,
							plugins: {
								legend: {
									position: 'top',
								},
								title: {
									display: true,
									text: 'Distribusi Penyakit di Cluster {{ $cluster['cluster'] }}'
								}
							}
						}
					});
				@endforeach
			});
		</script>

		<div class="row">
			<div class="col-md-12 text-end">
				<form action="{{ route('clusters.store') }}" method="POST" id="saveClustersForm">
					@csrf
					<input type="hidden" name="clusters" value="{{ json_encode($iterations[0]['clusters']) }}">
					<button type="submit" class="btn btn-success">Simpan Hasil Cluster</button>
				</form>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('#patientDataTable').DataTable({
				paging: true,
				searching: true,
				ordering: true,
				lengthChange: true,
				language: {
					info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri"
				}
			});
		});

		$(document).ready(function() {
			$('#patientTransformedDataTable').DataTable({
				paging: true,
				searching: true,
				ordering: true,
				lengthChange: true,
				language: {
					info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri"
				}
			});
		});

		const clusters = @json($iterations[0]['clusters']);
		const colors = ['#ff6384', '#36a2eb', '#4bc0c0']; // Define colors for each cluster

		// Count the number of patients in each cluster for the bar chart
		const clusterCounts = {};
		clusters.forEach(item => {
			const cluster = item.cluster;
			clusterCounts[cluster] = (clusterCounts[cluster] || 0) + item.size;
		});

		const clusterLabels = Object.keys(clusterCounts);
		const clusterData = Object.values(clusterCounts);

		// Bar Chart for Number of Patients per Cluster
		new Chart(document.getElementById('barChart'), {
			type: 'bar',
			data: {
				labels: clusterLabels.map(c => 'Cluster ' + c),
				datasets: [{
					label: 'Jumlah Pasien',
					data: clusterData,
					backgroundColor: colors
				}]
			}
		});

		// Pie Chart for cluster distribution
		const pieData = clusterLabels.map(c => ({
			label: 'Cluster ' + c,
			value: clusterCounts[c]
		}));
		new Chart(document.getElementById('pieChart'), {
			type: 'pie',
			data: {
				labels: pieData.map(d => d.label),
				datasets: [{
					data: pieData.map(d => d.value),
					backgroundColor: colors
				}]
			}
		});

		// Centroid Plot (3D Scatter)
		const centroids = @json($iterations[0]['new_centroids']);
		const membersData = [];

		// Collect the members along with their clusters
		clusters.forEach(cluster => {
			cluster.members.forEach(member => {
				membersData.push({
					x: member.point[0], // Feature 1
					y: member.point[1], // Feature 2
					z: member.point[2], // Feature 3
					label: member.pasien,
					cluster: cluster.cluster // Keep track of the cluster
				});
			});
		});

		const xCentroids = centroids.map(c => c[0]);
		const yCentroids = centroids.map(c => c[1]);
		const zCentroids = centroids.map(c => c[2]);

		const centroidTrace = {
			x: xCentroids,
			y: yCentroids,
			z: zCentroids,
			mode: 'markers+text',
			type: 'scatter3d',
			marker: {
				size: 10,
				color: 'black', // Color for centroids
				symbol: 'cross' // Centroid markers
			},
			text: xCentroids.map((_, i) => `Centroid ${i + 1}`),
			textposition: 'top center'
		};

		// Create the member trace with colors corresponding to their clusters
		const memberTrace = {
			x: membersData.map(m => m.x),
			y: membersData.map(m => m.y),
			z: membersData.map(m => m.z),
			mode: 'markers',
			type: 'scatter3d',
			marker: {
				size: 5,
				color: membersData.map(m => colors[m.cluster - 1]), // Use cluster index for color
				opacity: 0.8
			},
			text: membersData.map(m => m.label), // Display patient name on hover
			textposition: 'top center'
		};

		const data = [centroidTrace, memberTrace];
		const layout = {
			title: 'Centroid and Member Data in 3D',
			scene: {
				xaxis: {
					title: 'Jenis Penyakit'
				},
				yaxis: {
					title: 'Usia'
				},
				zaxis: {
					title: 'Jenis Kelamin'
				}
			}
		};

		Plotly.newPlot('centroidChart', data, layout);
	</script>

</body>

</html>
