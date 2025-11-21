<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\GenericArrayExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class BorrowReportController extends Controller
{
    public function form()
    {
        return view('reports.borrow_form');
    }

    public function generate(Request $request)
    {
        // ==== VALIDASI ====
        $request->validate([
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000|max:2100',
            'format'  => 'required|in:pdf,excel,csv,json',
            'columns' => 'required|array'
        ]);

        $month   = (int) $request->input('month');
        $year    = (int) $request->input('year');
        $cols    = $request->input('columns');
        $format  = strtolower($request->input('format'));
        $user = auth()->user();

        // Jika PEGawai atau KETUA TIM → hanya boleh melihat peminjaman miliknya
        if (in_array(strtolower($user->role), ['pegawai', 'ketua tim'])) {
            $ownOnly = true;
        } else {
            $ownOnly = false;
        }

        // ==== AMBIL DATA ====
        $borrowings = BorrowRequest::whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->when($ownOnly, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with([
                'user',
                'vehicle',
                'useReport',
                'team',
                'approvedBy'
            ])
            ->orderBy('start_at', 'asc')
            ->get();

        // ==== MAPPING ====
        $mapped = $borrowings->map(function ($b) use ($cols) {

            $r = $b->useReport;
            $approved = $b->approvedBy;

            $data = [];

            // ---- IDENTITAS ----
            if (in_array('nip', $cols))          $data['NIP Peminjam'] = $b->user->NIP ?? '-';
            if (in_array('nama', $cols))         $data['Nama Peminjam'] = $b->user->name ?? '-';

            if (in_array('kendaraan', $cols))    $data['Kendaraan'] = $b->vehicle->name ?? '-';

            // ---- TANGGAL PERGI ----
            if (in_array('start', $cols)) {
                $date = $b->start_at ? $b->start_at->format('d/m/Y') : '-';
                $time = $b->start_time ?: '-';
                $data['Pergi'] = "{$date} {$time}";
            }

            // ---- TANGGAL PULANG ----
            if (in_array('end', $cols)) {
                $date = $b->end_at ? $b->end_at->format('d/m/Y') : '-';
                $time = $b->end_time ?: '-';
                $data['Pulang'] = "{$date} {$time}";
            }

            // ---- DETAIL ----
            if (in_array('tujuan', $cols))       $data['Tujuan'] = $b->destination_address ?: '-';
            if (in_array('keperluan', $cols))    $data['Keperluan'] = $b->purpose_text ?: '-';
            if (in_array('status', $cols))       $data['Status'] = $b->status;

            // ---- REPORT (Jika belum completed) ----
            if ($b->status !== 'Completed' || !$r) {

                foreach ([
                    'fuel_before' => 'Bensin Sebelum',
                    'fuel_after'  => 'Bensin Setelah',
                    'km_before'   => 'KM Sebelum',
                    'km_after'    => 'KM Setelah',
                    'kondisi'     => 'Kondisi',
                    'catatan'     => 'Catatan'
                ] as $key => $label) {
                    if (in_array($key, $cols))
                        $data[$label] = '-';
                }

                return $data;
            }

            // ---- REPORT (Jika completed) ----
            if (in_array('fuel_before', $cols)) $data['Bensin Sebelum'] = $r->fuel_before;
            if (in_array('fuel_after', $cols))  $data['Bensin Setelah'] = $r->fuel_after;
            if (in_array('km_before', $cols))   $data['KM Sebelum'] = $r->km_before;
            if (in_array('km_after', $cols))    $data['KM Setelah'] = $r->km_after;

            // Kondisi
            if (in_array('kondisi', $cols))
                $data['Kondisi'] = $r->conditionSummary();

            // Catatan
            if (in_array('catatan', $cols)) {

                $notes = [];

                foreach ([
                    'hazards_note','horn_note','siren_note','tires_note',
                    'brakes_note','battery_note','start_engine_note'
                ] as $n) {
                    if (!empty($r->$n)) $notes[] = $r->$n;
                }

                $data['Catatan'] = count($notes) ? implode("; ", $notes) : '-';
            }

            return $data;
        });

        $filename = "Laporan_Peminjaman_{$month}_{$year}";

        // ==== JSON ====
        if ($format === 'json') {
            return Response::json($mapped);
        }

        // ==== CSV ====
        if ($format === 'csv') {

            $csv = fopen('php://temp', 'r+');

            // header
            if ($mapped->count()) {
                fputcsv($csv, array_keys($mapped->first()));
            }

            foreach ($mapped as $row) {
                fputcsv($csv, $row);
            }

            rewind($csv);
            $content = stream_get_contents($csv);

            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename={$filename}.csv");
        }

        // ==== EXCEL ====
        if ($format === 'excel') {
            return Excel::download(new GenericArrayExport($mapped->toArray()), "{$filename}.xlsx");
        }

        // ==== PDF ====
        if ($format === 'pdf') {

            $pdf = Pdf::loadView('reports.borrow_pdf', [
                'rows'  => $mapped,
                'month' => $month,
                'year'  => $year
            ])->setPaper('A4', 'landscape');

            return $pdf->download("{$filename}.pdf");
        }

        abort(500, 'Format tidak valid');
    }
}
