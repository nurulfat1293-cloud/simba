@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900">Input Alokasi Honor</h2>
        <p class="mt-2 text-slate-600">Lengkapi data di bawah ini untuk mengalokasikan honor dan nilai ganti rugi mitra.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-bold uppercase">Peringatan:</p>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li class="text-sm text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
        <form action="{{ route('transaksi.store') }}" method="POST" id="honorForm" class="p-8">
            @csrf

            <!-- Hidden Inputs for Raw Values -->
            <input type="hidden" name="harga_input" id="harga_satuan_raw" value="{{ old('harga_input') }}">
            <input type="hidden" name="nilai_lain" id="nilai_lain_raw" value="{{ old('nilai_lain', 0) }}">

            <!-- STEP 1: PILIH KEGIATAN -->
            <div class="mb-8 p-5 bg-slate-50 rounded-xl border border-slate-200">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">1. Pilih Kegiatan</label>
                    <span id="display_mata_anggaran" class="hidden px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-mono font-bold border border-indigo-200">
                        <i class="fas fa-wallet mr-1"></i> <span id="val_mata_anggaran"></span>
                    </span>
                </div>
                <select name="id_kegiatan" id="id_kegiatan" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition duration-200" required>
                    <option value="">-- Pilih Kegiatan Aktif --</option>
                    @foreach($kegiatan as $k)
                        <option value="{{ $k->id }}" data-anggaran="{{ $k->mata_anggaran }}" {{ old('id_kegiatan') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kegiatan }} ({{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- STEP 2 & 3: PILIH SPK & JABATAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">2. Pilih SPK Mitra</label>
                    <select name="id_spk" id="id_spk" class="w-full rounded-lg border-slate-300 py-3 px-4 bg-white disabled:bg-slate-100 transition duration-200" {{ old('id_spk') ? '' : 'disabled' }} required>
                        <option value="">-- Pilih Kegiatan Terlebih Dahulu --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">3. Pilih Jabatan Mitra</label>
                    <select name="id_jabatan" id="id_jabatan" class="w-full rounded-lg border-slate-300 py-3 px-4 focus:ring-indigo-500 transition duration-200" required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatan as $j)
                            <option value="{{ $j->id }}" {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- STEP 4: INPUT & VALIDASI -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Kolom Input -->
                <div class="lg:col-span-2 p-6 bg-indigo-50 rounded-2xl border border-indigo-100 space-y-5">
                    <label class="block text-sm font-bold text-indigo-900 mb-2 uppercase border-b border-indigo-200 pb-2">4. Input Nilai & Beban Kerja</label>
                    
                    <div>
                        <span class="text-xs text-indigo-600 font-bold uppercase tracking-wide">Harga Satuan Aktual (Rp)</span>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-indigo-400 font-bold">Rp</span>
                            <input type="text" id="harga_input_mask" class="w-full pl-10 rounded-xl border-indigo-200 py-3 px-4 text-lg font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 transition" placeholder="0" required>
                        </div>
                        <p id="hks_not_found" class="mt-2 text-xs text-red-500 font-bold hidden italic"><i class="fas fa-exclamation-circle mr-1"></i> Referensi HKS Belum Diatur</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-indigo-600 font-bold uppercase tracking-wide">Volume</span>
                            <input type="number" step="0.001" name="volume_target" id="volume_target" value="{{ old('volume_target') }}" class="w-full rounded-xl border-indigo-200 py-3 px-4 mt-1 text-lg font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 transition" placeholder="0" required>
                        </div>
                        <div>
                            <span class="text-xs text-indigo-600 font-bold uppercase tracking-wide">Nilai Tambah (Lain)</span>
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-indigo-400 font-bold">Rp</span>
                                <input type="text" id="nilai_lain_mask" class="w-full pl-10 rounded-xl border-indigo-200 py-3 px-4 text-lg font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 transition" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Info Validasi -->
                <div class="space-y-6">
                    <div class="p-6 bg-blue-900 rounded-2xl text-white shadow-lg border-b-4 border-blue-700">
                        <label class="block text-sm font-bold text-blue-200 mb-4 uppercase text-center border-b border-blue-700 pb-2 tracking-widest">
                            <i class="fas fa-tag mr-2"></i>Referensi HKS
                        </label>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-blue-300 uppercase font-semibold">Satuan:</span>
                                <span id="hks_satuan_display" class="font-bold text-white bg-blue-800 px-3 py-1 rounded-full text-sm">-</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-blue-800 pt-3">
                                <span class="text-xs text-blue-300 uppercase font-semibold">Maks. Harga:</span>
                                <span id="hks_max_display" class="font-extrabold text-2xl text-white">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-900 rounded-2xl text-white shadow-lg border-b-4 border-slate-700">
                        <label class="block text-sm font-bold text-slate-400 mb-4 uppercase text-center border-b border-slate-700 pb-2 tracking-widest">
                            <i class="fas fa-shield-alt mr-2"></i>Aturan SBML & Kuota
                        </label>
                        
                        <div class="space-y-4">
                            <div id="sbml_rule_info" class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                                <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Kategori Pemenang (Pagu):</p>
                                <p id="nama_sbml_label" class="text-sm font-bold text-indigo-300">-</p>
                                <div class="flex justify-between mt-2">
                                    <span class="text-[10px] text-slate-400 uppercase">Limit Bulanan:</span>
                                    <span id="sbml_max_display" class="text-xs font-mono" data-raw-max="0">Rp 0</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-400 uppercase">Alokasi Saat Ini:</span>
                                    <span id="total_honor_display" class="font-bold text-emerald-400">Rp 0</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2.5">
                                    <div id="sbml_progress_bar" class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <div class="flex justify-between border-t border-slate-800 pt-2 text-[10px]">
                                    <span class="text-slate-400 uppercase">Sudah Digunakan (Bulan Ini):</span>
                                    <span id="honor_spk_display" class="font-bold text-slate-300">Rp 0</span>
                                </div>
                            </div>

                            <div class="p-3 bg-orange-500/10 border border-orange-500/20 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-orange-200 uppercase">Sisa Kuota:</span>
                                    <span id="sisa_sbml_display" class="font-extrabold text-orange-400 text-xl tracking-tighter" data-raw-sisa="0">Rp 0</span>
                                </div>
                                <p id="sbml_warning_text" class="text-[9px] text-orange-300 italic mt-1 hidden text-right">
                                    *Nilai input melebihi sisa kuota SBML
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" id="btnSubmit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 px-6 rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-lg group">
                <i class="fas fa-save group-hover:animate-bounce"></i> SIMPAN ALOKASI HONOR
            </button>
        </form>
    </div>
</div>

<script>
    const idKegiatan = document.getElementById('id_kegiatan');
    const idSpk = document.getElementById('id_spk');
    const idJabatan = document.getElementById('id_jabatan');
    const hargaInputMask = document.getElementById('harga_input_mask');
    const hargaRaw = document.getElementById('harga_satuan_raw');
    const nilaiLainMask = document.getElementById('nilai_lain_mask');
    const nilaiLainRaw = document.getElementById('nilai_lain_raw');
    const volumeInput = document.getElementById('volume_target');
    const honorForm = document.getElementById('honorForm');

    let currentHksPrice = 0;

    // Load initial values on DOM Content Loaded
    window.addEventListener('DOMContentLoaded', async () => {
        // Init masks for old values
        if (hargaRaw.value) {
            hargaInputMask.value = formatRibuan(hargaRaw.value);
        }
        if (nilaiLainRaw.value) {
            nilaiLainMask.value = formatRibuan(nilaiLainRaw.value);
        }
        
        if(idKegiatan.value) {
            updateMataAnggaranDisplay(idKegiatan);
            // Tunggu load SPK selesai jika ada old value
            await loadSpk(idKegiatan.value, "{{ old('id_spk') }}");
            
            if(idJabatan.value) {
                await fetchHksReference();
            }

            if(idSpk.value) {
                await fetchHonorInfo(); // Akan panggil logic baru
            }
        }
        // Jalankan kalkulasi perdana
        calculateHonor();
    });

    function formatRibuan(angka) {
        if (!angka) return "";
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }

    function getRawValue(str) {
        if(!str) return 0;
        // Hapus titik ribuan dan ganti koma desimal ke titik
        return parseFloat(str.replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    function formatIDR(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
    }

    // Masking Event Listeners
    hargaInputMask.addEventListener('input', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        this.value = formatRibuan(val);
        hargaRaw.value = getRawValue(this.value);
        calculateHonor();
    });

    nilaiLainMask.addEventListener('input', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        this.value = formatRibuan(val);
        nilaiLainRaw.value = getRawValue(this.value);
        calculateHonor();
    });

    volumeInput.addEventListener('input', calculateHonor);

    idKegiatan.addEventListener('change', async function() {
        updateMataAnggaranDisplay(this);
        await loadSpk(this.value);
        await fetchHksReference();
        calculateHonor();
    });

    idJabatan.addEventListener('change', async () => {
        await fetchHksReference();
        await fetchHonorInfo(); 
        calculateHonor();
    });

    idSpk.addEventListener('change', async () => {
        await fetchHonorInfo();
        calculateHonor();
    });

    function updateMataAnggaranDisplay(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const anggaran = selectedOption ? selectedOption.getAttribute('data-anggaran') : null;
        const display = document.getElementById('display_mata_anggaran');
        const val = document.getElementById('val_mata_anggaran');

        if (anggaran && selectElement.value) {
            display.classList.remove('hidden');
            val.innerText = anggaran;
        } else {
            display.classList.add('hidden');
        }
    }

    async function loadSpk(kegId, selectedSpk = null) {
        if (!kegId) return resetForm();
        try {
            idSpk.disabled = false;
            idSpk.innerHTML = '<option value="">Memuat SPK...</option>';
            const resSpk = await fetch(`/api/get-spk-by-kegiatan/${kegId}`);
            const dataSpk = await resSpk.json();
            
            idSpk.innerHTML = '<option value="">-- Pilih SPK --</option>';
            dataSpk.forEach(s => {
                const namaMitra = s.mitra ? s.mitra.nama_lengkap : 'N/A';
                const isSelected = selectedSpk == s.nomor_spk ? 'selected' : '';
                // Value pakai ID, bukan nomor_spk agar konsisten dengan controller
                idSpk.innerHTML += `<option value="${s.id}" ${isSelected}>${s.nomor_spk} | ${namaMitra}</option>`;
            });
        } catch (error) { console.error("Error loading SPK:", error); }
    }

    async function fetchHksReference() {
        const kegId = idKegiatan.value;
        const jabId = idJabatan.value;
        if (!kegId || !jabId) {
             document.getElementById('hks_max_display').innerText = "Rp 0";
             document.getElementById('hks_satuan_display').innerText = "-";
             currentHksPrice = 0;
             return;
        }

        try {
            const res = await fetch(`/api/get-hks-by-jabatan?id_kegiatan=${kegId}&id_jabatan=${jabId}`);
            const data = await res.json();
            if (data.status === 'success') {
                currentHksPrice = parseFloat(data.harga_satuan);
                document.getElementById('hks_max_display').innerText = formatIDR(data.harga_satuan);
                document.getElementById('hks_satuan_display').innerText = data.nama_satuan;
                document.getElementById('hks_not_found').classList.add('hidden');
            } else {
                currentHksPrice = 0;
                document.getElementById('hks_not_found').classList.remove('hidden');
                document.getElementById('hks_max_display').innerText = "Rp 0";
            }
        } catch (error) { console.error("Error fetching HKS:", error); }
    }

    // === PERBAIKAN LOGIC FETCH & ERROR HANDLING ===
    async function fetchHonorInfo() {
        const spkId = idSpk.value;
        const kegId = idKegiatan.value;
        const jabId = idJabatan.value;
        
        // Jangan request jika SPK belum dipilih
        if (!spkId) {
            document.getElementById('honor_spk_display').innerText = "Rp 0";
            return;
        }

        document.getElementById('nama_sbml_label').innerText = "Menghitung...";

        try {
            const response = await fetch("{{ route('api.check-honor-limit') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // Penting agar Laravel return JSON saat error
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    spk_id: spkId,
                    kegiatan_id: kegId,
                    jabatan_id: jabId
                })
            });

            // Cek status HTTP dulu (agar tidak error parsing HTML sebagai JSON)
            if (!response.ok) {
                console.error("HTTP Error:", response.status, response.statusText);
                document.getElementById('nama_sbml_label').innerText = `Error Server (${response.status})`;
                return;
            }

            const data = await response.json();
            
            if (data.status === 'success') {
                document.getElementById('honor_spk_display').innerText = formatIDR(data.total_used || 0);
                document.getElementById('sbml_max_display').innerText = formatIDR(data.effective_sbml || 0);
                document.getElementById('sisa_sbml_display').innerText = formatIDR(data.remaining || 0);
                document.getElementById('nama_sbml_label').innerText = data.winning_category || 'Belum Ditentukan';
                
                document.getElementById('sisa_sbml_display').setAttribute('data-raw-sisa', data.remaining || 0);
                document.getElementById('sbml_max_display').setAttribute('data-raw-max', data.effective_sbml || 0);
            } else {
                console.error("API Error (Logical):", data.message);
                document.getElementById('nama_sbml_label').innerText = "Gagal Memuat: " + data.message;
            }

        } catch (error) { 
            console.error("Fetch/Network Error:", error); 
            document.getElementById('nama_sbml_label').innerText = "Gagal Koneksi";
        }
    }

    function calculateHonor() {
        const hrg = getRawValue(hargaInputMask.value);
        const vol = parseFloat(volumeInput.value) || 0;
        const extra = getRawValue(nilaiLainMask.value);
        
        const totalInput = (hrg * vol) + extra;
        document.getElementById('total_honor_display').innerText = formatIDR(totalInput);
        
        const sisaKapasitas = parseFloat(document.getElementById('sisa_sbml_display').getAttribute('data-raw-sisa')) || 0;
        const limitSbml = parseFloat(document.getElementById('sbml_max_display').getAttribute('data-raw-max')) || 0;
        const progressBar = document.getElementById('sbml_progress_bar');
        
        // Logika Progress Bar
        let usedPercentage = 0;
        if (limitSbml > 0) {
            // Berapa yang sudah dipakai sebelumnya
            const alreadyUsed = limitSbml - sisaKapasitas; 
            // Total = (Sudah Dipakai) + (Yang sedang diinput)
            const currentTotalUsage = alreadyUsed + totalInput;
            usedPercentage = (currentTotalUsage / limitSbml) * 100;
        }
        
        progressBar.style.width = `${Math.min(usedPercentage, 100)}%`;
        
        const btn = document.getElementById('btnSubmit');
        const hksDisplay = document.getElementById('hks_max_display');
        
        // Cek kondisi over limit
        // (sisaKapasitas + 100) -> Toleransi Rp100 perak untuk pembulatan
        const isOverSbml = limitSbml > 0 && totalInput > (sisaKapasitas + 100); 
        const isOverHks = currentHksPrice > 0 && hrg > currentHksPrice;

        if (isOverSbml) {
            progressBar.classList.remove('bg-indigo-500');
            progressBar.classList.add('bg-red-500');
            document.getElementById('sbml_warning_text').classList.remove('hidden');
            btn.className = "w-full bg-red-600 text-white font-black py-5 px-6 rounded-2xl shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest text-lg cursor-not-allowed";
            btn.innerHTML = '<i class="fas fa-ban"></i> MELEBIHI SISA KUOTA SBML';
            btn.disabled = true;
        } else {
            progressBar.classList.add('bg-indigo-500');
            progressBar.classList.remove('bg-red-500');
            document.getElementById('sbml_warning_text').classList.add('hidden');
            btn.disabled = false;
            
            if (isOverHks) {
                btn.className = "w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-5 px-6 rounded-2xl shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest text-lg transition-colors";
                btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> MELEBIHI HKS MASTER (TETAP SIMPAN)';
                hksDisplay.classList.add('text-red-400', 'animate-pulse');
            } else {
                btn.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 px-6 rounded-2xl shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest text-lg group transition-colors";
                btn.innerHTML = '<i class="fas fa-save group-hover:animate-bounce"></i> SIMPAN ALOKASI HONOR';
                hksDisplay.classList.remove('text-red-400', 'animate-pulse');
            }
        }
    }

    honorForm.addEventListener('submit', function(e) {
        // Pastikan nilai raw terupdate sebelum dikirim
        hargaRaw.value = getRawValue(hargaInputMask.value);
        nilaiLainRaw.value = getRawValue(nilaiLainMask.value);
        
        if (!hargaRaw.value || hargaRaw.value <= 0) {
            e.preventDefault();
            // Gunakan message UI ketimbang alert browser
            const msg = document.createElement('div');
            msg.className = "fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-2xl z-50";
            msg.innerText = "Harga satuan tidak boleh kosong!";
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 3000);
        }
    });

    function resetForm() {
        idSpk.disabled = true;
        idSpk.innerHTML = '<option value="">-- Pilih Kegiatan Terlebih Dahulu --</option>';
        document.getElementById('display_mata_anggaran').classList.add('hidden');
        document.getElementById('honor_spk_display').innerText = "Rp 0";
        document.getElementById('sisa_sbml_display').innerText = "Rp 0";
        document.getElementById('sisa_sbml_display').setAttribute('data-raw-sisa', 0);
        calculateHonor();
    }
</script>
@endsection