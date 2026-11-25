
@extends('layouts.app')

@section('content')

<style>
    .facility-page {
        --blue: #2586d5;
        --blue-dark: #1769b1;
        --blue-light: #eaf5ff;
        --text: #183653;
        --muted: #8295aa;
        max-width: 1250px;
        margin: 30px auto;
        padding: 0 20px 40px;
        color: var(--text);
    }

    .facility-form-card {
        background: rgba(255, 255, 255, .88);
        border: 1px solid rgba(255,255,255,.9);
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 18px 55px rgba(38, 104, 160, .09);
        backdrop-filter: blur(16px);
        animation: formEntrance .75s cubic-bezier(.2,.8,.2,1) both;
    }

    @keyframes formEntrance {
        from { opacity: 0; transform: translateY(25px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .facility-heading {
        display: flex;
        align-items: center;
        gap: 17px;
        margin-bottom: 30px;
    }

    .facility-heading-icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        color: #1680d0;
        background: linear-gradient(135deg,#e5f3ff,#d5eaff);
        font-size: 27px;
    }

    .facility-heading h2 {
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 6px;
        letter-spacing: -.5px;
    }

    .facility-heading p {
        color: var(--muted);
        margin: 0;
        font-size: 14px;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #4283b8;
        letter-spacing: .8px;
        text-transform: uppercase;
        margin: 8px 0 19px;
    }

    .facility-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .facility-column {
        display: flex;
        flex-direction: column;
        gap: 19px;
    }

    .facility-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #38546e;
    }

    .facility-field label span {
        color: #e05b6b;
    }

    .facility-control {
        width: 100%;
        min-height: 49px;
        border: 1px solid #dce7f1;
        border-radius: 12px;
        padding: 12px 15px;
        color: #29445e;
        background: #fafdff;
        outline: none;
        font-size: 14px;
        transition: .25s ease;
    }

    .facility-control:hover {
        border-color: #a9cfee;
    }

    .facility-control:focus {
        border-color: #2994e8;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(41,148,232,.13);
    }

    textarea.facility-control {
        min-height: 115px;
        resize: vertical;
    }

    .field-error {
        display: block;
        color: #d84b5c;
        font-size: 12px;
        margin-top: 6px;
    }

    .description-section {
        margin-top: 25px;
    }

    .upload-zone {
        position: relative;
        border: 2px dashed #b9d9f3;
        background: linear-gradient(145deg,#f7fbff,#edf7ff);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: .25s ease;
        min-height: 155px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: #278fe0;
        background: #e7f4ff;
        transform: translateY(-2px);
    }

    .upload-icon {
        width: 43px;
        height: 43px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #dcefff;
        color: #2184d0;
        font-size: 21px;
        margin-bottom: 10px;
    }

    .upload-zone strong {
        font-size: 13px;
        color: #315b7d;
    }

    .upload-zone small {
        color: #8ba0b4;
        font-size: 11px;
        margin-top: 5px;
    }

    .upload-zone input {
        display: none;
    }

    .image-preview {
        display: none;
        margin-top: 12px;
        width: 100%;
        max-height: 190px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #dce8f2;
    }

    .upload-filename {
        color: #2382c9;
        font-size: 12px;
        margin-top: 8px;
        overflow-wrap: anywhere;
    }

    .form-divider {
        height: 1px;
        background: #edf1f6;
        margin: 28px 0 22px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    .btn-save-facility,
    .btn-cancel-facility {
        border-radius: 11px;
        padding: 12px 23px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: .25s ease;
    }

    .btn-save-facility {
        border: 0;
        color: white;
        background: linear-gradient(135deg,#3299e8,#176fbd);
        box-shadow: 0 7px 18px rgba(34,130,207,.22);
        position: relative;
        overflow: hidden;
    }

    .btn-save-facility::before {
        content: "";
        position: absolute;
        top: 0;
        left: -120%;
        width: 70%;
        height: 100%;
        background: linear-gradient(100deg,transparent,rgba(255,255,255,.4),transparent);
        transform: skewX(-20deg);
        transition: .65s;
    }

    .btn-save-facility:hover::before {
        left: 150%;
    }

    .btn-save-facility:hover {
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(34,130,207,.3);
    }

    .btn-save-facility:disabled {
        opacity: .7;
        cursor: wait;
        transform: none;
    }

    .btn-cancel-facility {
        color: #55718b;
        border: 1px solid #d7e3ed;
        background: white;
    }

    .btn-cancel-facility:hover {
        color: #1678c5;
        border-color: #8dc4ed;
        background: #f1f8ff;
    }

    .validation-summary {
        background: #fff1f2;
        border: 1px solid #ffd5da;
        color: #b93849;
        padding: 13px 17px;
        border-radius: 12px;
        margin-bottom: 22px;
        font-size: 13px;
    }

    @media(max-width: 768px) {
        .facility-page {
            padding: 0 12px 25px;
            margin-top: 18px;
        }

        .facility-form-card {
            padding: 21px 17px;
            border-radius: 18px;
        }

        .facility-columns {
            grid-template-columns: 1fr;
            gap: 19px;
        }

        .facility-heading h2 {
            font-size: 21px;
        }

        .form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .form-actions a,
        .form-actions button {
            text-align: center;
            width: 100%;
        }
    }

    @media(prefers-reduced-motion: reduce) {
        .facility-form-card {
            animation: none;
        }

        .facility-control,
        .upload-zone,
        .btn-save-facility,
        .btn-cancel-facility {
            transition: none;
        }
    }
</style>

<div class="facility-page">

    <div class="facility-form-card">

        <div class="facility-heading">
            <div class="facility-heading-icon">🏢</div>
            <div>
                <h2>{{ $f->exists ? 'Ubah Fasilitas' : 'Tambah Fasilitas' }}</h2>
                <p>
                    Lengkapi informasi sarana dan prasarana kampus
                    agar dapat dikelola melalui SIMFAS.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="validation-summary">
                <strong>Periksa kembali formulir:</strong>
                Terdapat data yang belum sesuai.
            </div>
        @endif

        <form
            id="facilityForm"
            method="POST"
            enctype="multipart/form-data"
            action="{{ $f->exists ? '/facilities/'.$f->id : '/facilities' }}"
        >
            @csrf

            @if($f->exists)
                @method('PUT')
            @endif

            <div class="form-section-title">Informasi fasilitas</div>

            <div class="facility-columns">

                {{-- KOLOM KIRI --}}
                <div class="facility-column">

                    <div class="facility-field">
                        <label>Kode inventaris <span>*</span></label>
                        <input
                            class="facility-control"
                            name="code"
                            value="{{ old('code', $f->code) }}"
                            placeholder="Contoh: INV-001"
                            maxlength="30"
                            required
                        >
                        @error('code') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="facility-field">
                        <label>Kategori <span>*</span></label>
                        <input
                            class="facility-control"
                            name="category"
                            value="{{ old('category', $f->category) }}"
                            placeholder="Contoh: Elektronik"
                            maxlength="60"
                            required
                        >
                        @error('category') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="facility-field">
                        <label>Jumlah unit <span>*</span></label>
                        <input
                            class="facility-control"
                            type="number"
                            name="units"
                            value="{{ old('units', $f->units ?? 1) }}"
                            min="1"
                            required
                        >
                        @error('units') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="facility-field">
                        <label>Kondisi fasilitas <span>*</span></label>
                        <select class="facility-control" name="condition" required>
                            @foreach([
                                'baik' => 'Baik',
                                'rusak_ringan' => 'Rusak ringan',
                                'rusak_sedang' => 'Rusak sedang',
                                'rusak_berat' => 'Rusak berat'
                            ] as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('condition', $f->condition ?? 'baik') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('condition') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                </div>

                {{-- KOLOM KANAN --}}
                <div class="facility-column">

                    <div class="facility-field">
                        <label>Nama fasilitas <span>*</span></label>
                        <input
                            class="facility-control"
                            name="name"
                            value="{{ old('name', $f->name) }}"
                            placeholder="Contoh: Proyektor Epson"
                            maxlength="150"
                            required
                        >
                        @error('name') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="facility-field">
                        <label>Lokasi <span>*</span></label>
                        <input
                            class="facility-control"
                            name="location"
                            value="{{ old('location', $f->location) }}"
                            placeholder="Contoh: Laboratorium Komputer"
                            maxlength="150"
                            required
                        >
                        @error('location') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="facility-field">
                        <label>Status fasilitas <span>*</span></label>
                        <select class="facility-control" name="status" required>
                            @foreach([
                                'tersedia' => 'Tersedia',
                                'dipinjam' => 'Dipinjam',
                                'pemeliharaan' => 'Dalam pemeliharaan',
                                'tidak_dapat_digunakan' => 'Tidak dapat digunakan'
                            ] as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('status', $f->status ?? 'tersedia') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- UPLOAD FOTO --}}
                    <div class="facility-field">
                        <label>Foto fasilitas <small style="color:#91a4b7">(Opsional)</small></label>

                        <label class="upload-zone" id="uploadZone" for="facilityImage">
                            <div class="upload-icon">📷</div>
                            <strong>Klik atau tarik foto ke sini</strong>
                            <small>JPG, JPEG, PNG, WEBP · Maksimal 4 MB</small>

                            <input
                                type="file"
                                id="facilityImage"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                            <img
                                id="imagePreview"
                                class="image-preview"
                                alt="Preview foto fasilitas"
                                @if($f->image_path)
                                    src="{{ asset('storage/'.$f->image_path) }}"
                                    style="display:block"
                                @endif
                            >

                            <span class="upload-filename" id="uploadFilename">
                                @if($f->image_path)
                                    Foto fasilitas saat ini
                                @else
                                    Belum ada foto dipilih
                                @endif
                            </span>
                        </label>

                        @error('image') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="description-section">
                <div class="facility-field">
                    <label>Deskripsi fasilitas</label>
                    <textarea
                        class="facility-control"
                        name="description"
                        placeholder="Tuliskan keterangan tambahan mengenai fasilitas..."
                    >{{ old('description', $f->description) }}</textarea>
                    @error('description') <small class="field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-divider"></div>

            <div class="form-actions">
                <a href="/facilities" class="btn-cancel-facility">
                    Batal
                </a>

                <button type="submit" class="btn-save-facility" id="saveButton">
                    {{ $f->exists ? 'Simpan Perubahan' : 'Simpan Fasilitas' }}
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('facilityImage');
    const preview = document.getElementById('imagePreview');
    const filename = document.getElementById('uploadFilename');
    const zone = document.getElementById('uploadZone');
    const form = document.getElementById('facilityForm');
    const button = document.getElementById('saveButton');

    function showPreview(file) {
        if (!file) return;

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Format foto harus JPG, PNG, atau WEBP.');
            input.value = '';
            return;
        }

        if (file.size > 4 * 1024 * 1024) {
            alert('Ukuran foto maksimal 4 MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
            filename.textContent = file.name;
        };

        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function () {
        showPreview(this.files[0]);
    });

    zone.addEventListener('dragover', function (event) {
        event.preventDefault();
        zone.classList.add('dragover');
    });

    zone.addEventListener('dragleave', function () {
        zone.classList.remove('dragover');
    });

    zone.addEventListener('drop', function (event) {
        event.preventDefault();
        zone.classList.remove('dragover');

        const file = event.dataTransfer.files[0];

        if (file) {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            showPreview(file);
        }
    });

    form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Menyimpan...';
    });
});
</script>

@endsection
