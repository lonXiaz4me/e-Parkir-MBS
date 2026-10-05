<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
{
    $reapplyData = null;

    if ($request->filled('reapply')) {
        $old = Application::where('app_no', $request->query('reapply'))
            ->where('user_id', auth()->id())
            ->where('app_status', 'not_approved')
            ->first();

        if ($old) {
            $reapplyData = [
                'company_name'     => $old->company_name,
                'ssm_no'           => $old->ssm_no,
                'company_email'    => $old->company_email,
                'company_no'       => $old->company_phone,
                'category'         => $old->category,
                'type_of_business' => $old->type_of_business,
                'location'         => $old->location,
                'total_parking'    => $old->total_parking,
            ];
            // Deliberately excluded: not_approved_reason, app_status,
            // app_status_msg, ssm_img/ic_img/licence_img/location_img —
            // since images are usually the actual problem, force re-upload.
        }
    }

    return view('auth.application', compact('reapplyData'));
}

    public function store(Request $request)
    {
        // ── 1. Validate ──────────────────────────────────────────────────────
        $validated = $request->validate([
            'company_name'     => ['required', 'string', 'max:255'],
            'ssm_no'           => ['required', 'string', 'max:100'],
            // FIX #9: Added server-side validation for company_email and
            // company_no. Previously these were only validated in the browser
            // (JS), meaning anyone bypassing the frontend could submit without
            // them. They were also never saved — silently discarded on every
            // submission.
            'company_email'    => ['required', 'email', 'max:255'],
            'company_no'       => ['required', 'string', 'max:20'],
            'category'         => ['required', 'string', 'in:Kategori A,Kategori B'],
            'type_of_business' => ['required', 'string', 'max:255'],
            'location'         => ['required', 'string', 'max:255'],
            'location_coords'  => ['nullable', 'string', 'max:255'],
            'total_parking'    => ['required', 'integer', 'min:1'],
            'ssm_img'          => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'ic_img'           => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'licence_img'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'location_img'     => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'declaration'      => ['required', 'accepted'],
        ], [
            'company_name.required'     => 'Nama syarikat wajib diisi.',
            'ssm_no.required'           => 'No. Pendaftaran SSM wajib diisi.',
            // FIX #9: Malay error messages for the new fields
            'company_email.required'    => 'Email rasmi syarikat wajib diisi.',
            'company_email.email'       => 'Sila masukkan alamat email yang sah.',
            'company_no.required'       => 'No. telefon syarikat wajib diisi.',
            'category.required'         => 'Sila pilih kategori perniagaan.',
            'category.in'               => 'Kategori perniagaan tidak sah.',
            'type_of_business.required' => 'Sila pilih jenis perniagaan.',
            'location.required'         => 'Sila pilih lokasi petak.',
            'total_parking.required'    => 'Jumlah petak dimohon wajib diisi.',
            'total_parking.min'         => 'Jumlah petak mestilah sekurang-kurangnya 1.',
            'ssm_img.required'          => 'Salinan SSM syarikat wajib dimuat naik.',
            'ssm_img.image'             => 'Fail SSM mestilah dalam format imej.',
            'ssm_img.max'               => 'Saiz gambar SSM tidak boleh melebihi 5MB.',
            'ic_img.required'           => 'Salinan IC pemohon wajib dimuat naik.',
            'ic_img.image'              => 'Fail IC mestilah dalam format imej.',
            'ic_img.max'                => 'Saiz gambar IC tidak boleh melebihi 5MB.',
            'licence_img.required'      => 'Salinan lesen perniagaan wajib dimuat naik.',
            'licence_img.image'         => 'Fail lesen mestilah dalam format imej.',
            'licence_img.max'           => 'Saiz gambar lesen tidak boleh melebihi 5MB.',
            'location_img.required'     => 'Gambar lokasi wajib dimuat naik.',
            'location_img.image'        => 'Fail lokasi mestilah dalam format imej.',
            'location_img.max'          => 'Saiz gambar lokasi tidak boleh melebihi 5MB.',
            'declaration.required'      => 'Sila tandakan perisytiharan sebelum menghantar.',
            'declaration.accepted'      => 'Sila tandakan perisytiharan sebelum menghantar.',
        ]);

        // ── 2. Store uploaded images BEFORE the transaction ──────────────────
        $userId   = auth()->id();
        $basePath = "applications/{$userId}";

        // Uploads now go to Azure Blob Storage instead of local disk.
        // We store the full public blob URL in the DB (e.g.
        // https://evocity2022storage.blob.core.windows.net/mbs-image/...).
        $uploadToAzure = function (\Illuminate\Http\UploadedFile $file, string $folder) {
            $ext      = $file->getClientOriginalExtension();
            $slug     = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = now()->valueOf() . '-' . $slug . '_' . (string) \Illuminate\Support\Str::uuid() . '.' . $ext;
            $path     = "{$folder}/{$filename}";

            $ok = Storage::disk('azure_image')->put($path, file_get_contents($file->getRealPath()));

            if (!$ok) {
                throw new \RuntimeException("Azure upload failed for path: {$path}");
            }

            return Storage::disk('azure_image')->url($path);
        };

        // NOTE: location_img is uploaded separately from the other three
        // documents. The camera-capture front end already names the file
        // itself (e.g. "1782291119_loc_1782291105923.jpg") before it ever
        // reaches this controller, so — unlike ssm/ic/licence, which are
        // arbitrary user-picked files that need a collision-safe generated
        // name — we preserve that original client filename as-is instead of
        // rebuilding it with the slug+UUID scheme. This keeps location_img
        // naming consistent with how it has always looked, and avoids a
        // second, differently-shaped filename convention for this one field.
        $uploadLocationToAzure = function (\Illuminate\Http\UploadedFile $file, string $folder) {
            $originalName = $file->getClientOriginalName();
            $filename     = \Illuminate\Support\Str::slug(
                pathinfo($originalName, PATHINFO_FILENAME),
                '_'
            ) . '.' . $file->getClientOriginalExtension();
            $path = "{$folder}/{$filename}";

            Storage::disk('azure_image')->put($path, file_get_contents($file->getRealPath()));

            return Storage::disk('azure_image')->url($path);
        };

        // FIX #24: Recompute the price server-side instead of trusting
        // $request->input('grand_total') (a hidden field calculated in JS,
        // editable via devtools before submit — see
        // Application::calculateMonthlyRate() for details). Done before the
        // file uploads so a bad/tampered location can be rejected without
        // writing any files to disk first.
        $calculatedTotal = Application::calculateMonthlyRate(
            $validated['location'],
            (int) $validated['total_parking']
        );

        if ($calculatedTotal === null) {
            return back()->withInput()->withErrors([
                'location' => 'Lokasi yang dipilih tidak sah. Sila pilih semula daripada senarai.',
            ]);
        }

        $ssmPath      = $uploadToAzure($request->file('ssm_img'), "{$basePath}/ssm");
        $icPath       = $uploadToAzure($request->file('ic_img'), "{$basePath}/ic");
        $licencePath  = $uploadToAzure($request->file('licence_img'), "{$basePath}/licence");
        $locationPath = $uploadLocationToAzure($request->file('location_img'), "{$basePath}/location");

        // ── 3. Generate app_no atomically & persist (fix #3) ─────────────────
        //
        // FIX #23: Cache::lock(...)->block(5, ...) throws a
        // LockTimeoutException if the lock isn't free within 5 seconds —
        // previously nothing caught this, so under genuine concurrent load
        // (many applicants submitting near a deadline, for example) some
        // requests would crash with a raw, unhelpful 500 error page right
        // at the final step of a long multi-section form.
        //
        // Worse, the four files above were already written to disk in step
        // 2, BEFORE this lock block runs. If the lock times out and the
        // request dies here, those uploads become orphaned on disk forever
        // — never attached to any Application row, never cleaned up.
        //
        // FIX: catch the timeout, delete the orphaned uploads we just wrote,
        // and send the user back to the form with a clear message asking
        // them to retry, instead of leaving them looking at a crash page
        // and silently leaking storage.
        try {
            $application = Cache::lock('app_no_sequence', 10)->block(5, function () use (
                $validated, $userId, $ssmPath, $icPath, $licencePath, $locationPath, $calculatedTotal
            ) {
                return DB::transaction(function () use (
                    $validated, $userId, $ssmPath, $icPath, $licencePath, $locationPath, $calculatedTotal
                ) {
                    // FIX #25: app_no now derives from the latest app_id
                    // instead of a per-day count. The previous scheme
                    // (MBS-{today}-{count of today's rows + 1}) reset its
                    // sequence portion back to 0001 every day, which meant
                    // app_no was NOT globally increasing/orderable — e.g.
                    // MBS-20260716-0003 and MBS-20260717-0001 look "lower"
                    // on the second day despite being created later.
                    //
                    // FIX: Base the sequence on Application::max('app_id')
                    // (the table's real auto-increment high-water mark) + 1,
                    // defaulting to 1 when the table is empty. This is
                    // always strictly increasing regardless of what day an
                    // application is submitted on. The date prefix is kept
                    // purely for human readability — it does NOT reset the
                    // counter, so app_no as a whole stays monotonic.
                    //
                    // Still generated inside Cache::lock('app_no_sequence')
                    // above + this DB transaction, so two concurrent
                    // requests can't read the same max and collide.
                    $today    = now()->format('Ymd');
                    $sequence = (int) Application::max('app_id') + 1;
                    $appNo    = "MBS-{$today}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);

                    return Application::create([
                        'app_no'           => $appNo,
                        'user_id'          => $userId,
                        'company_name'     => $validated['company_name'],
                        'ssm_no'           => $validated['ssm_no'],
                        // FIX #9: Save company_email and company_no
                        'company_email'    => $validated['company_email'],
                        'company_no'       => $validated['company_no'],
                        'ssm_img'          => $ssmPath,
                        'category'         => $validated['category'],
                        'type_of_business' => $validated['type_of_business'],
                        'location'         => $validated['location'],
                        'location_img'     => $locationPath,
                        'location_coords'  => $validated['location_coords'] ?? null,
                        'total_parking'    => (int) $validated['total_parking'],
                        'ic_img'           => $icPath,
                        'licence_img'      => $licencePath,
                        'app_status'       => 'pending',
                        // FIX #24: was $request->input('grand_total') — a
                        // value the client calculated and could freely edit
                        // before submitting. Now uses the price the server
                        // itself computed above from the validated location
                        // and lot count, which the client cannot influence.
                        'total_amount'     => $calculatedTotal,
                    ]);
                });
            });
        } catch (LockTimeoutException $e) {
            // $ssmPath etc. are now full Azure blob URLs, e.g.
            // https://{account}.blob.core.windows.net/{container}/{blobPath}
            // The 'azure' disk is already scoped to the container, so strip
            // both the host and the leading "/{container}/" segment.
            $container  = config('filesystems.disks.azure_image.container');
            $toBlobPath = function (string $url) use ($container) {
                $path = ltrim(parse_url($url, PHP_URL_PATH), '/');
                return \Illuminate\Support\Str::startsWith($path, "{$container}/")
                    ? \Illuminate\Support\Str::after($path, "{$container}/")
                    : $path;
            };

            Storage::disk('azure')->delete([
                $toBlobPath($ssmPath),
                $toBlobPath($icPath),
                $toBlobPath($licencePath),
                $toBlobPath($locationPath),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'app_no' => 'Sistem sedang sibuk memproses permohonan lain. Sila cuba hantar permohonan anda semula sebentar lagi.',
                ]);
        }

        // ── 4. Send notification ─────────────────────────────────────────────
        Notification::send(
            $userId,
            $application->app_id,
            'app_submitted',
            "app_submitted:{$application->app_no}"
        );

        // ── 5. Redirect ──────────────────────────────────────────────────────
        return redirect()->route('application')
            ->with('success', "Permohonan {$application->app_no} telah berjaya dihantar. Kami akan menghubungi anda setelah semakan selesai.");
    }

    public function show(Request $request)
    {
        return view('auth.camera-capture');
    }

    // ── Download application as PDF ──────────────────────────────────────────
    public function download(string $appNo)
    {
        $application = Application::where('app_no', $appNo)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Images are now stored as full Azure blob URLs, so fetch them over
        // HTTP instead of reading from local disk.
        $toBase64 = function (?string $url): ?string {
            if (!$url) return null;

            $response = Http::get($url);
            if ($response->failed()) return null;

            $mime = $response->header('Content-Type') ?: 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode($response->body());
        };

        $images = [
            'ssm'      => $toBase64($application->ssm_img),
            'ic'       => $toBase64($application->ic_img),
            'licence'  => $toBase64($application->licence_img),
            'location' => $toBase64($application->location_img),
        ];

        $user = Auth::user();

        $pdf = Pdf::loadView('auth.pdf.application', compact('application', 'images', 'user'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Permohonan-' . $application->app_no . '.pdf');
    }
}