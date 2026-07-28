<?php

namespace App\Http\Controllers;

use App\Services\GlobalDataSyncService;

class PortSyncController extends Controller
{
    public function sync(GlobalDataSyncService $service)
    {
        $result = $service->syncPorts(true);

        $message = 'Sinkronisasi World Port Index selesai. '
            . 'Sumber: ' . $result['source_count']
            . ', Diminta: ' . $result['requested_count']
            . ', API diterima: ' . $result['received_count']
            . ', Baru: ' . $result['inserted_count']
            . ', Diperbarui: ' . $result['updated_count']
            . ', Dilewati: ' . $result['skipped_count']
            . ', Record gagal: ' . $result['failed_record_count']
            . ', Request gagal: ' . $result['failed_request_count'] . '.';

        return redirect()
            ->route('admin.index')
            ->with($result['failed_record_count'] + $result['failed_request_count'] > 0 ? 'error' : 'success', $message);
    }
}
