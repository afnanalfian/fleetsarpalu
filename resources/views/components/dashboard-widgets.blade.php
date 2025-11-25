<div class="row">


<div class="col-md-3">
<div class="card text-white bg-primary mb-3">
<div class="card-body">
<h5 class="card-title">Total Kendaraan</h5>
<p class="card-text">10 Unit</p>
</div>
</div>
</div>


<div class="col-md-3">
<div class="card text-white bg-success mb-3">
<div class="card-body">
<h5 class="card-title">Tersedia</h5>
<p class="card-text">7 Unit</p>
</div>
</div>
</div>


<div class="col-md-3">
<div class="card text-white bg-warning mb-3">
<div class="card-body">
<h5 class="card-title">Dipinjam</h5>
<p class="card-text">2 Unit</p>
</div>
</div>
</div>


<div class="col-md-3">
<div class="card text-white bg-danger mb-3">
<div class="card-body">
<h5 class="card-title">Rusak</h5>
<p class="card-text">1 Unit</p>
</div>
</div>
</div>
</div>


{{-- Grafik dummy peminjaman per bulan --}}
<div class="card mt-3">
<div class="card-header">Grafik Peminjaman per Bulan</div>
<div class="card-body">
<canvas id="borrowChart" height="100"></canvas>
</div>
</div>


<script>
// Dummy Chart (nanti diganti dynamic)
var ctx = document.getElementById('borrowChart').getContext('2d');
new Chart(ctx, {
type: 'line',
data: {
labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
datasets: [{
label: 'Peminjaman',
data: [5,3,7,4,6,8]
}]
}
});
</script>
