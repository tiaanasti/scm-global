<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class AdminPortController extends Controller
{
    /**
     * Menyimpan pelabuhan baru dari Panel Admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'country_id' => ['required', 'integer', 'exists:countries,id'],
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'status' => [
                    'required',
                    'string',
                    'in:Aman,Normal,Waspada,Siaga,Darurat',
                ],
                'port_risk_score' => [
                    'required',
                    'numeric',
                    'between:0,100',
                ],
            ],
            [
                'name.required' => 'Nama pelabuhan wajib diisi.',
                'name.string' => 'Nama pelabuhan harus berupa teks.',
                'name.max' => 'Nama pelabuhan maksimal 255 karakter.',

                'city.string' => 'Nama kota harus berupa teks.',
                'city.max' => 'Nama kota maksimal 255 karakter.',

                'country_id.required' => 'Negara wajib dipilih.',
                'country_id.integer' => 'Negara yang dipilih tidak valid.',
                'country_id.exists' => 'Negara yang dipilih tidak ditemukan.',

                'latitude.required' => 'Latitude wajib diisi.',
                'latitude.numeric' => 'Latitude harus berupa angka.',
                'latitude.between' => 'Latitude harus berada antara -90 sampai 90.',

                'longitude.required' => 'Longitude wajib diisi.',
                'longitude.numeric' => 'Longitude harus berupa angka.',
                'longitude.between' => 'Longitude harus berada antara -180 sampai 180.',

                'status.required' => 'Status pelabuhan wajib dipilih.',
                'status.in' => 'Status pelabuhan tidak valid.',

                'port_risk_score.required' => 'Skor risiko wajib diisi.',
                'port_risk_score.numeric' => 'Skor risiko harus berupa angka.',
                'port_risk_score.between' => 'Skor risiko harus berada antara 0 sampai 100.',
            ]
        );

        try {
            $country = DB::table('countries')
                ->select('id', 'name')
                ->where('id', $validated['country_id'])
                ->first();

            if (!$country) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Negara yang dipilih tidak ditemukan.',
                    ], 422);
                }
                return back()
                    ->withInput()
                    ->with('error', 'Negara yang dipilih tidak ditemukan.');
            }

            DB::transaction(function () use ($validated, $country): void {
                DB::table('ports')->insert([
                    'country_id' => $country->id,
                    'name' => trim($validated['name']),
                    'city' => $this->nullableString($validated['city'] ?? null),
                    'country_name' => $country->name,
                    'latitude' => (float) $validated['latitude'],
                    'longitude' => (float) $validated['longitude'],
                    'status' => $validated['status'],
                    'port_risk_score' => (float) $validated['port_risk_score'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $message = 'Pelabuhan "' . $validated['name'] . '" berhasil ditambahkan.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ], 201);
            }

            return redirect()
                ->route('admin.index')
                ->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelabuhan gagal ditambahkan. Periksa kembali data yang dimasukkan.',
                ], 500);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Pelabuhan gagal ditambahkan. Periksa kembali data yang dimasukkan.'
                );
        }
    }

    /**
     * Menampilkan halaman edit pelabuhan.
     */
    public function edit(int $id): View
    {
        $port = DB::table('ports')
            ->where('id', $id)
            ->first();

        abort_if(
            !$port,
            404,
            'Data pelabuhan tidak ditemukan.'
        );

        $countries = DB::table('countries')
            ->select(
                'id',
                'country_code',
                'name'
            )
            ->orderBy('name')
            ->get();

        if (request()->expectsJson()) {
            $html = view(
                'admin.partials.edit-port',
                compact('port', 'countries')
            )->render();
            return response()->json(['html' => $html]);
        }

        return view(
            'admin.ports.edit',
            compact('port', 'countries')
        );
    }

    /**
     * Memperbarui data pelabuhan.
     */
    public function update(Request $request, int $id)
    {
        $port = DB::table('ports')
            ->where('id', $id)
            ->first();

        if (!$port) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Data pelabuhan tidak ditemukan.'], 404);
            }
            return redirect()
                ->route('admin.index')
                ->with('error', 'Data pelabuhan tidak ditemukan.');
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'country_id' => ['required', 'integer', 'exists:countries,id'],
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'status' => [
                    'required',
                    'string',
                    'in:Aman,Normal,Waspada,Siaga,Darurat',
                ],
                'port_risk_score' => [
                    'required',
                    'numeric',
                    'between:0,100',
                ],
            ],
            [
                'name.required' => 'Nama pelabuhan wajib diisi.',
                'name.string' => 'Nama pelabuhan harus berupa teks.',
                'name.max' => 'Nama pelabuhan maksimal 255 karakter.',

                'city.string' => 'Nama kota harus berupa teks.',
                'city.max' => 'Nama kota maksimal 255 karakter.',

                'country_id.required' => 'Negara wajib dipilih.',
                'country_id.integer' => 'Negara yang dipilih tidak valid.',
                'country_id.exists' => 'Negara yang dipilih tidak ditemukan.',

                'latitude.required' => 'Latitude wajib diisi.',
                'latitude.numeric' => 'Latitude harus berupa angka.',
                'latitude.between' => 'Latitude harus berada antara -90 sampai 90.',

                'longitude.required' => 'Longitude wajib diisi.',
                'longitude.numeric' => 'Longitude harus berupa angka.',
                'longitude.between' => 'Longitude harus berada antara -180 sampai 180.',

                'status.required' => 'Status pelabuhan wajib dipilih.',
                'status.in' => 'Status pelabuhan tidak valid.',

                'port_risk_score.required' => 'Skor risiko wajib diisi.',
                'port_risk_score.numeric' => 'Skor risiko harus berupa angka.',
                'port_risk_score.between' => 'Skor risiko harus berada antara 0 sampai 100.',
            ]
        );

        try {
            $country = DB::table('countries')
                ->select('id', 'name')
                ->where('id', $validated['country_id'])
                ->first();

            if (!$country) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Negara yang dipilih tidak ditemukan.'], 422);
                }
                return back()
                    ->withInput()
                    ->with('error', 'Negara yang dipilih tidak ditemukan.');
            }

            DB::transaction(function () use (
                $id,
                $validated,
                $country
            ): void {
                DB::table('ports')
                    ->where('id', $id)
                    ->update([
                        'country_id' => $country->id,
                        'name' => trim($validated['name']),
                        'city' => $this->nullableString(
                            $validated['city'] ?? null
                        ),
                        'country_name' => $country->name,
                        'latitude' => (float) $validated['latitude'],
                        'longitude' => (float) $validated['longitude'],
                        'status' => $validated['status'],
                        'port_risk_score' => (float) $validated['port_risk_score'],
                        'updated_at' => now(),
                    ]);
            });

            $msg = 'Data pelabuhan "' . $validated['name'] . '" berhasil diperbarui.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg]);
            }

            return redirect()
                ->route('admin.index')
                ->with(
                    'success',
                    $msg
                );
        } catch (Throwable $exception) {
            report($exception);

            $msg = 'Data pelabuhan gagal diperbarui. Periksa kembali data yang dimasukkan.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 500);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    $msg
                );
        }
    }

    /**
     * Menghapus pelabuhan.
     */
    public function destroy(int $id)
    {
        $port = DB::table('ports')
            ->select('id', 'name')
            ->where('id', $id)
            ->first();

        if (!$port) {
            $msg = 'Data pelabuhan tidak ditemukan.';
            if (request()->expectsJson()) {
                return response()->json(['message' => $msg], 404);
            }
            return redirect()
                ->route('admin.index')
                ->with('error', $msg);
        }

        try {
            DB::transaction(function () use ($id): void {
                DB::table('ports')
                    ->where('id', $id)
                    ->delete();
            });

            $msg = 'Pelabuhan "' . $port->name . '" berhasil dihapus.';
            if (request()->expectsJson()) {
                return response()->json(['message' => $msg], 200);
            }
            return redirect()
                ->route('admin.index')
                ->with('success', $msg);
        } catch (Throwable $exception) {
            report($exception);

            $msg = 'Pelabuhan gagal dihapus karena masih digunakan oleh data lain.';
            if (request()->expectsJson()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()
                ->route('admin.index')
                ->with('error', $msg);
        }
    }

    /**
     * Mengubah string kosong menjadi null.
     */
    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}